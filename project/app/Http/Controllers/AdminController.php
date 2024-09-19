<?php

namespace App\Http\Controllers;

use App\Contracts\AbiturientEmptyMark;
use App\Contracts\AbiturientLink;
use App\Contracts\AllAbiturientsContent;
use App\Contracts\DirectionEmptySnapshotContent;
use App\Contracts\DirectionLink;
use App\Contracts\DirectionLinksList;
use App\Contracts\DirectionShortLink;
use App\Contracts\DirectionsShortContent;
use App\Contracts\Responses\GetAllAbiturientsResponse;
use App\Contracts\Responses\GetDirectionEmptyResultsResponse;
use App\Contracts\Responses\GetDirectionsResponse;
use App\Contracts\Responses\GetUserLkContentResponse;
use App\Contracts\Responses\ResponseWithId;
use App\Contracts\UserLkContent;
use App\Models\AbiturientDirectionLink;
use App\Models\Direction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\MessageBag;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use App\Models\Abiturient;
use Symfony\Component\HttpFoundation\Response;
use App\Contracts\Requests\RegisterRequest;
use App\Contracts\Requests\LoginRequest;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Illuminate\Support\Facades\Redis;

use App\Contracts\Responses\DefaultResponse;

class AdminController extends Controller
{
    public function index() : JsonResponse
    {
        return new JsonResponse("{empty_endpoint}");
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

        for ($i = 0; $i < count($abiturients_db); $i++)
        {
            $current_item = $abiturients_db[$i];

            $full_name = $current_item['first_name'] . ' ' . $current_item['second_name'];

            $abiturientLink = new AbiturientLink($current_item['id'], $full_name);

            array_push($abiturients, $abiturientLink);
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
            foreach ($abiturientsMarks as $abiturientUpdated)
            {
                DB::update(
                    'update abiturient_direction_links set mark = ? where direction_id = ? and abiturient_id = ?',
                    [$abiturientUpdated['mark'], $direction_id, $abiturientUpdated['abiturient_id']]);
            }

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

            return new JsonResponse(json_encode($failResponseModel), Response::HTTP_BAD_REQUEST, [], true);
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

        $places_db = AbiturientDirectionLink::where('direction_id', $direction_id)->get();

        $places = $places_db;

        asort($places, "compare");

        $direction = Direction::where('id', $direction_id)->first();

        if (!isset($direction))
        {
            $failResponseModel = new DefaultResponse(
                null,
                'Direction with id ' . strval($direction_id) . ' does not exist',
                false);

            return new JsonResponse(json_encode($failResponseModel), Response::HTTP_BAD_REQUEST, [], true);  
        }

        if ($direction['is_finalized'] == true)
        {
            $failResponseModel = new DefaultResponse(
                null,
                'Direction with id ' . strval($direction_id) . ' was already finalized',
                false);

            return new JsonResponse(json_encode($failResponseModel), Response::HTTP_BAD_REQUEST, [], true);           
        }

        $direction_budget_count = $direction['budget_places_number'];
        $min_ball = $direction['min_ball'];

        $number = 0;

        $new_places = array();

        foreach ($places as $currentPlace)
        {
            if ($currentPlace['has_diplom_original'] == true 
                && $currentPlace['mark'] >= $min_ball 
                && $number < $direction_budget_count)
                {
                    $currentPlace['admission_status'] = 'enrolled';
                }
            else 
            {
                $currentPlace['failed'] = 'enrolled';
            }

            array_push($new_places, $currentPlace);
        } 

        DB::delete('delete from abiturient_direction_links where direction_id = ?', [$direction_id]);

        foreach ($new_places as $newCurrentPlace)
        {
            $newCurrentPlace->save();
        }

        $successResponseModel = new DefaultResponse(
            null,
            null,
            true);

        return new JsonResponse(json_encode($successResponseModel), Response::HTTP_OK, [], true); 
    }

    function compare($a, $b)
    {
        return $a['mark'] > $b['mark'];
    }
}