<?php

namespace App\Http\Controllers;

use App\Contracts\AbiturientEmptyMark;
use App\Contracts\AbiturientLink;
use App\Contracts\AllAbiturientsContent;
use App\Contracts\DirectionEmptySnapshotContent;
use App\Contracts\Responses\AddDirectionWithSettingsResponse;
use App\Contracts\Responses\GetAllAbiturientsResponse;
use App\Contracts\Responses\GetDirectionEmptyResultsResponse;
use App\Models\AbiturientDirectionLink;
use App\Models\Direction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Models\Abiturient;
use Symfony\Component\HttpFoundation\Response;

use App\Contracts\Responses\DefaultResponse;

class AdminController extends Controller
{
    public function index() : JsonResponse
    {
        return new JsonResponse("{empty_endpoint}");
    }

    public function addNewDirection(Request $request) : ?JsonResponse
    {
        $basicAuthentificator = new BasicAuthentificator();

        $auth_result = $basicAuthentificator->IsUserExistsAndTokenValidAndAdmin($request);

        if ($auth_result != null)
        {
            return $auth_result;
        }
        
        $json = $request->getContent();

        $array = json_decode($json, true);

        $direction_caption = $array['direction_caption'];
        $budget_places_number = $array['budget_places_number'];
        $min_ball = $array['min_ball'];

        $directionWithSameCaption = Direction::where('caption', $direction_caption)->first();

        if (isset($directionWithSameCaption))
        {
            $failResponseModel = new DefaultResponse(
                null,
                'Direction with caption ' . $direction_caption . ' is already exists',
                false);

            return new JsonResponse(json_encode($failResponseModel), Response::HTTP_OK, [], true);
        }

        $newDirection = new Direction([
            'caption' => $direction_caption,
            'budget_places_number' => $budget_places_number,
            'min_ball' => $min_ball,
            'is_filled' => false,
            'is_finalized' => false
        ]);

        $newDirection->save();

        $addedDirection = Direction::where('caption', $direction_caption)->first();

        $successResponseModel = new AddDirectionWithSettingsResponse(
            $array['abiturient_id'],
            $array['token'],
            $addedDirection['id'],
            null,
            null,
            true);

        return new JsonResponse(json_encode($successResponseModel), Response::HTTP_OK, [], true);
    }

    public function addAbiturientLinks(Request $request) : ?JsonResponse
    {
        $basicAuthentificator = new BasicAuthentificator();

        $auth_result = $basicAuthentificator->IsUserExistsAndTokenValidAndAdmin($request);

        if ($auth_result != null)
        {
            return $auth_result;
        }
        
        $json = $request->getContent();

        error_log($json);

        $array = json_decode($json, true);

        $content = $array['content'];

        $target_abiturient_id = $content['target_abiturient_id'];

        $targetAbiturient = Abiturient::where('id', $target_abiturient_id)->first(); 

        if (!isset($targetAbiturient))
        {
            $failResponseModel = new DefaultResponse(
                null,
                'Target abiturient with id ' . $target_abiturient_id . ' not already exists',
                false);

            return new JsonResponse(json_encode($failResponseModel), Response::HTTP_OK, [], true);
        }

        if ($targetAbiturient['is_requested'] == true)
        {
            $failResponseModel = new DefaultResponse(
                null,
                'Target abiturient with id ' . $target_abiturient_id . ' is already requested to directions',
                false);

            return new JsonResponse(json_encode($failResponseModel), Response::HTTP_OK, [], true);
        }

        $has_diplom_original = $content['has_diplom_original'];

        $direction_links = $content['directions_links'];

        DB::update('update abiturients set has_diplom_original = ? where id = ?',
         [$has_diplom_original, $target_abiturient_id]);

        foreach ($direction_links as $link)
        {
            $direction_id = $link['direction_id'];

            $existedDirection = Direction::where('id', $direction_id)->first();

            if (!isset($existedDirection))
            {
                $failResponseModel = new DefaultResponse(
                    null,
                    'Direction with id ' . $direction_id . ' not already exists',
                    false);
    
                return new JsonResponse(json_encode($failResponseModel), Response::HTTP_OK, [], true);
            }

            $db_link = new AbiturientDirectionLink([
                'abiturient_id' => $target_abiturient_id,
                'direction_id' => $direction_id,
                'place' => 0,
                'mark' => 0,
                'admission_status' => 'request_in_progress',
                'prioritet_number' => $link['prioritet_number'],
                'has_diplom_original' => $has_diplom_original
            ]);

            $db_link->save();
        }

        DB::update('update abiturients set is_requested = ? where id = ?',
         [true, $target_abiturient_id]);

        $redisCacheActualizer = new RedisCacheActualizer();

        $redisCacheActualizer->ActualizeCache($target_abiturient_id,  false);

        $successResponseModel = new DefaultResponse(
            null,
            null,
            true);

        return new JsonResponse(json_encode($successResponseModel), Response::HTTP_OK, [], true);
    }


    public function addOriginalDiplom(Request $request) : ?JsonResponse
    {
        $basicAuthentificator = new BasicAuthentificator();

        $auth_result = $basicAuthentificator->IsUserExistsAndTokenValidAndAdmin($request);

        if ($auth_result != null)
        {
            return $auth_result;
        }
        
        $json = $request->getContent();

        $array = json_decode($json, true);

        $target_abiturient_id = $array['target_abiturient_id'];

        $targetAbiturient = Abiturient::where('id', $target_abiturient_id)->first(); 

        if (!isset($targetAbiturient))
        {
            $failResponseModel = new DefaultResponse(
                null,
                'Target abiturient with id ' . $target_abiturient_id . ' not already exists',
                false);

            return new JsonResponse(json_encode($failResponseModel), Response::HTTP_OK, [], true);
        }

        if ($targetAbiturient['is_requested'] == false)
        {
            $failResponseModel = new DefaultResponse(
                null,
                'Target abiturient with id ' . $target_abiturient_id . ' was not already requested to directions',
                false);

            return new JsonResponse(json_encode($failResponseModel), Response::HTTP_OK, [], true);
        }

        $has_diplom_original = $array['has_diplom_original'];

        DB::update('update abiturients set has_diplom_original = ? where id = ?',
         [$has_diplom_original, $target_abiturient_id]);

         $redisCacheActualizer = new RedisCacheActualizer();

         $redisCacheActualizer->ActualizeCache($target_abiturient_id,  false);

        $successResponseModel = new DefaultResponse(
            null,
            null,
            true);

        return new JsonResponse(json_encode($successResponseModel), Response::HTTP_OK, [], true);
    }

    public function getAllAbiturients(Request $request) : ?JsonResponse
    {
        $basicAuthentificator = new BasicAuthentificator();

        $auth_result = $basicAuthentificator->IsUserExistsAndTokenValidAndAdmin($request);

        if ($auth_result != null)
        {
            return $auth_result;
        }
        
        $json = $request->getContent();

        $array = json_decode($json, true);

        $abiturients_db = Abiturient::all();

        $abiturients = array();

        foreach($abiturients_db as $current_item)
        {
            if ($current_item['is_admin'] == false)
            {
                $full_name = $current_item['first_name'] . ' ' . $current_item['second_name'];

                $abiturientLink = new AbiturientLink(
                    $current_item['id'], 
                    $full_name,
                    $current_item['is_requested'],
                    $current_item['is_enrolled'],
                    $current_item['has_diplom_original']);
    
                array_push($abiturients, $abiturientLink);
            }
        }

        $abiturientsContent = new AllAbiturientsContent($abiturients);

        $responseModel = new GetAllAbiturientsResponse(
            $array['abiturient_id'], 
            $array['token'], 
            $abiturientsContent, 
            null, 
            true);

        $jsonResponse = json_encode($responseModel);

        return new JsonResponse($jsonResponse, Response::HTTP_OK, [], true);   
    }

    public function getRequestedAbiturients(Request $request) : ?JsonResponse
    {
        $basicAuthentificator = new BasicAuthentificator();

        $auth_result = $basicAuthentificator->IsUserExistsAndTokenValidAndAdmin($request);

        if ($auth_result != null)
        {
            return $auth_result;
        }
        
        $json = $request->getContent();

        $array = json_decode($json, true);

        $abiturients_db = Abiturient::where('is_requested', true)->get();

        $abiturients = array();

        foreach($abiturients_db as $current_item)
        {
            if ($current_item['is_admin'] == false)
            {
                $full_name = $current_item['first_name'] . ' ' . $current_item['second_name'];

                $abiturientLink = new AbiturientLink(
                    $current_item['id'], 
                    $full_name,
                    $current_item['is_requested'],
                    $current_item['is_enrolled'],
                    $current_item['has_diplom_original']);
    
                array_push($abiturients, $abiturientLink);
            }
        }

        $abiturientsContent = new AllAbiturientsContent($abiturients);

        $responseModel = new GetAllAbiturientsResponse(
            $array['abiturient_id'], 
            $array['token'], 
            $abiturientsContent, 
            null, 
            true);

        $jsonResponse = json_encode($responseModel);

        return new JsonResponse($jsonResponse, Response::HTTP_OK, [], true);   
    }

    public function getEnrolledAbiturients(Request $request) : ?JsonResponse
    {
        $basicAuthentificator = new BasicAuthentificator();

        $auth_result = $basicAuthentificator->IsUserExistsAndTokenValidAndAdmin($request);

        if ($auth_result != null)
        {
            return $auth_result;
        }
        
        $json = $request->getContent();

        $array = json_decode($json, true);

        $abiturients_db = Abiturient::where('is_enrolled', true)->get();

        $abiturients = array();

        foreach($abiturients_db as $current_item)
        {
            if ($current_item['is_admin'] == false)
            {
                $full_name = $current_item['first_name'] . ' ' . $current_item['second_name'];

                $abiturientLink = new AbiturientLink(
                    $current_item['id'], 
                    $full_name,
                    $current_item['is_requested'],
                    $current_item['is_enrolled'],
                    $current_item['has_diplom_original']);
    
                array_push($abiturients, $abiturientLink);
            }
        }

        $abiturientsContent = new AllAbiturientsContent($abiturients);

        $responseModel = new GetAllAbiturientsResponse(
            $array['abiturient_id'], 
            $array['token'], 
            $abiturientsContent, 
            null, 
            true);

        $jsonResponse = json_encode($responseModel);

        return new JsonResponse($jsonResponse, Response::HTTP_OK, [], true);   
    }

    public function getDirectionEmptySnapshot(Request $request) : ?JsonResponse
    {
        $basicAuthentificator = new BasicAuthentificator();

        $auth_result = $basicAuthentificator->IsUserExistsAndTokenValidAndAdmin($request);

        if ($auth_result != null)
        {
            return $auth_result;
        }
        
        $json = $request->getContent();

        $array = json_decode($json, true);

        $direction_id = $array['direction_id'];

        $direction = Direction::where('id', $direction_id)->first();

        $direction_name = $direction['caption'];

        $places_db = AbiturientDirectionLink::where('direction_id', $direction_id)->get();

        $places = array();

        foreach ($places_db as $current_item)
        {
            $abiturient_name = Abiturient::where('id', $current_item['abiturient_id'])->first();

            $place = new AbiturientEmptyMark(
                $current_item['abiturient_id'], 
                $abiturient_name,
                0);

            array_push($places, $place);
        }

        $placesContent = new DirectionEmptySnapshotContent($direction_id, $direction_name, $places);

        $responseModel = new GetDirectionEmptyResultsResponse(
            $array['abiturient_id'], 
            $array['token'], 
            $placesContent, 
            null, 
            true);

        $jsonResponse = json_encode($responseModel);

        return new JsonResponse($jsonResponse, Response::HTTP_OK, [], true);   
    }

    // https://stackoverflow.com/questions/66025194/php-laravel-frontend-how-to-upload-send-csv-file-to-backend-api
    public function fillDirectionMarks(Request $request) : ?JsonResponse
    {
        $basicAuthentificator = new BasicAuthentificator();

        $auth_result = $basicAuthentificator->IsUserExistsAndTokenValidAndAdmin($request);

        if ($auth_result != null)
        {
            return $auth_result;
        }
        
        $json = $request->getContent();

        $array = json_decode($json, true);

        $content = $array['content'];

        $direction_id = $content['direction_id'];

        $direction = Direction::where('id', $direction_id)->first();

        if ($direction != null)
        {
            if ($direction['is_filled'] == true)
            {
                $failResponseModel = new DefaultResponse(
                    null,
                    'Direction with id ' . strval($direction_id) . ' was already filled with exam marks',
                    false);
    
                return new JsonResponse(json_encode($failResponseModel), Response::HTTP_OK, [], true);           
            }
        }

        $abiturientsMarks = $content['abiturients'];

        $existedAbiturients = AbiturientDirectionLink::where('direction_id', $direction_id)->get();

        $is_request_validated_bydb = true;

        foreach ($abiturientsMarks as $abiturientUpdated)
        {
            $abiturientExisted = false;

            foreach ($existedAbiturients as $abiturient)
            {
                if ($abiturient['abiturient_id'] == $abiturientUpdated['abiturient_id'])
                {
                    $abiturientExisted = true;

                    break;
                }
            }

            if ($abiturientExisted == false)
            {
                $is_request_validated_bydb = false;
                break;
            }
        }

        if ($is_request_validated_bydb == true)
        {
            $newAbiturients = array();

            foreach($existedAbiturients as $existedAbiturient)
            {
                foreach ($abiturientsMarks as $abiturientUpdated)
                {
                    if ($existedAbiturient['abiturient_id'] == $abiturientUpdated['abiturient_id'])
                    {
                        $existedAbiturient['mark'] = $abiturientUpdated['mark'];

                        break;
                    }
                }

                array_push($newAbiturients, $existedAbiturient);
            }

/*             for ($i = 0; $i < count($newAbiturients); $i++)
            {
                for ($j = $i + 1; $j < count($abiturientsMarks) - 1; $j++)
                {
                    if ($newAbiturients[$i] < $newAbiturients[$j])
                    {
                        $temp = $newAbiturients[$i];

                        $newAbiturients[$i] = $newAbiturients[$j];

                        $newAbiturients[$j] = $temp;
                    }
                }
            } */

            usort($newAbiturients, function ($item1, $item2){
                return $item2['mark'] <=> $item1['mark'];
            });

            DB::delete('delete from abiturient_direction_links where direction_id = ?', [$direction_id]);

            $place_number = 1;

            $redisCacheActualizer = new RedisCacheActualizer();

            foreach ($newAbiturients as $newAbiturient)
            {
                $newAbiturientLink = new AbiturientDirectionLink([
                    'abiturient_id' => $newAbiturient['abiturient_id'],
                    'direction_id' => $newAbiturient['direction_id'],
                    'mark' => $newAbiturient['mark'],
                    'place' => $place_number,
                    'admission_status' => $newAbiturient['admission_status'],
                    'prioritet_number' => $newAbiturient['prioritet_number'],
                    'has_diplom_original' => $newAbiturient['has_diplom_original'],
                ]);

                $newAbiturientLink->save();

                $place_number++;

                $redisCacheActualizer->ActualizeCache($newAbiturient['abiturient_id'],  false);
            }

            DB::update('update directions set is_filled = ? where id = ?', [true, $direction_id]);

            $successResponseModel = new DefaultResponse(
                null,
                null,
                true);

            return new JsonResponse(json_encode($successResponseModel), Response::HTTP_OK, [], true);
        }
        else 
        {
            $failResponseModel = new DefaultResponse(
                null,
                'Some abiturients from input list are not existed',
                false);

            return new JsonResponse(json_encode($failResponseModel), Response::HTTP_OK, [], true);
        }
    }

    public function directionFinalize(Request $request) : ?JsonResponse
    {
        $basicAuthentificator = new BasicAuthentificator();

        $auth_result = $basicAuthentificator->IsUserExistsAndTokenValidAndAdmin($request);

        if ($auth_result != null)
        {
            return $auth_result;
        }
        
        $json = $request->getContent();

        $array = json_decode($json, true);

        $direction_id = $array['direction_id'];

        $direction = Direction::where('id', $direction_id)->first();

        if (!isset($direction))
        {
            $failResponseModel = new DefaultResponse(
                null,
                'Direction with id ' . strval($direction_id) . ' does not exist',
                false);

            return new JsonResponse(json_encode($failResponseModel), Response::HTTP_OK, [], true);  
        }

        if ($direction['is_filled'] == false)
        {
            $failResponseModel = new DefaultResponse(
                null,
                'Direction with id ' . strval($direction_id) . ' was not already filled with exam marks',
                false);

            return new JsonResponse(json_encode($failResponseModel), Response::HTTP_OK, [], true);           
        }

        if ($direction['is_finalized'] == true)
        {
            $failResponseModel = new DefaultResponse(
                null,
                'Direction with id ' . strval($direction_id) . ' was already finalized',
                false);

            return new JsonResponse(json_encode($failResponseModel), Response::HTTP_OK, [], true);           
        }

        $places_db = AbiturientDirectionLink::where('direction_id', $direction_id)->get();

        $direction_budget_count = $direction['budget_places_number'];
        $min_ball = $direction['min_ball'];

        $number = 0;

        $new_places = array();

        foreach ($places_db as $currentPlace)
        {
            if ($currentPlace['has_diplom_original'] == true 
                && $currentPlace['mark'] >= $min_ball 
                && $number < $direction_budget_count)
                {
                    $currentPlace['admission_status'] = 'enrolled';
                    $number++;
                }
            else 
            {
                $currentPlace['admission_status'] = 'failed';
            }

            array_push($new_places, $currentPlace);
        }

        $redisCacheActualizer = new RedisCacheActualizer();
        
        foreach($new_places as $newPlace)
        {
            DB::update(
                'update abiturient_direction_links set admission_status = ? where direction_id = ? and abiturient_id = ?',
                [$newPlace['admission_status'], $direction_id, $newPlace['abiturient_id']]);

            if ($newPlace['admission_status'] == 'enrolled')
            {
                DB::update('update abiturients set is_enrolled = ? where id = ?',
                 [true, $newPlace['abiturient_id']]);

                $redisCacheActualizer->ActualizeCache($newPlace['abiturient_id'],  false);
            }
        }

        DB::update('update directions set is_finalized = ? where id = ?',
         [true, $direction_id]);

        $successResponseModel = new DefaultResponse(
            null,
            null,
            true);

        return new JsonResponse(json_encode($successResponseModel), Response::HTTP_OK, [], true); 
    }
}