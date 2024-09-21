<?php

namespace App\Http\Controllers;

use App\Contracts\DirectionShortLink;
use App\Contracts\DirectionSnapshotContent;
use App\Contracts\DirectionsShortContent;
use App\Contracts\Responses\GetDirectionSnapshotResponse;
use App\Contracts\Responses\GetDirectionsResponse;
use App\Contracts\Responses\ResponseWithId;
use App\Models\AbiturientDirectionLink;
use App\Models\Direction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Models\Abiturient;
use App\Contracts\PlaceSnapshot;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Redis;

use App\Contracts\Responses\DefaultResponse;

class AbiturientController extends Controller
{
    public function index() : JsonResponse
    {
        return new JsonResponse("{empty_endpoint}");
    }

    public function registerPost(Request $request) : JsonResponse
    {
        $request->validate([
            'email' => 'required', 
            'password' => 'required', 
            'first_name' => 'required',
            'second_name' => 'required', 
            'is_admin' => 'required']);

        $json = $request->getContent();

        $array = json_decode($json, true);

        $user_email = $array['email'];

        $existed_user = Abiturient::where('email', $user_email)->first();

        if (isset($existed_user))
        {
            $failResponseModel = new DefaultResponse(
                null,
                'User with email ' . $user_email . ' is already existed',
                false);

            return new JsonResponse(json_encode($failResponseModel), Response::HTTP_OK, [], true);
        }

        $guid = new GUID();

        $token = $guid->NewGUID();

        $abiturient = new Abiturient([
            'email' => $user_email,
            'password'=> $array['password'],
            'first_name'=> $array['first_name'],
            'second_name' => $array['second_name'],
            'is_admin' => $array['is_admin'],
            'has_diplom_original' => false,
            'is_requested' => false,
            'is_enrolled' => false,
            'token' => $token
        ]);

        $abiturient->save();

        $created_abiturient = Abiturient::where('email', $user_email)->first();

        $abiturient_id = $created_abiturient['id'];

        $successResponseModel = new ResponseWithId(
            $abiturient_id, 
            $token, 
            $array['is_admin'],
            null, 
            null, 
            true);

        return new JsonResponse(json_encode($successResponseModel), Response::HTTP_OK, [], true);
    }

        /**
     * @Route("/login", methods={"POST"})
     *
     */
    public function loginPost(Request $request) : JsonResponse
    {
        $request->validate([
            'email' => 'required', 
            'password' => 'required']);

        $json = $request->getContent();

        $array = json_decode($json, true);

        error_log('we are here 1');

        $user_email = $array['email'];
        $user_password = $array['password'];

        $existed_user = Abiturient::where('email', $user_email)->first();

        if (!isset($existed_user))
        {
            $failResponseModel = new DefaultResponse(
                null,
                'User with email ' . $user_email . ' is not already existed',
                false);

            return new JsonResponse(json_encode($failResponseModel), Response::HTTP_OK, [], true);
        }

        if ($existed_user['password'] != $user_password)
        {
            $failResponseModel = new DefaultResponse(
                null,
                'Passwords not equals',
                false);

            return new JsonResponse(json_encode($failResponseModel), Response::HTTP_OK, [], true);
        }

        $guid = new GUID();

        $token = $guid->NewGUID();

        DB::update('update abiturients set token = ?', [$token]);

        $successResponseModel = new ResponseWithId(
            $existed_user['id'],
            $token,
            $existed_user['is_admin'],
            null,
            null,
            true);

        return new JsonResponse(json_encode($successResponseModel), Response::HTTP_OK, [], true);
    }

    public function logoutPost(Request $request) : ?JsonResponse
    {
        $basicAuthentificator = new BasicAuthentificator();

        $auth_result = $basicAuthentificator->IsUserExistsAndTokenValid($request);

        if ($auth_result != null)
        {
            return $auth_result;
        }

        $json = $request->getContent();

        $array = json_decode($json, true);
        
        $successResponseModel = new ResponseWithId(
            $array['abiturient_id'], 
            $array['token'],
            false,
            null, 
            null, 
            true);

        return new JsonResponse(json_encode($successResponseModel), Response::HTTP_OK, [], true);   
    }

    public function getUserLKContent(Request $request) : ?JsonResponse
    {
        $basicAuthentificator = new BasicAuthentificator();

        $auth_result = $basicAuthentificator->IsUserExistsAndTokenValid($request);

        if ($auth_result != null)
        {
            return $auth_result;
        }
        
        $json = $request->getContent();

        $array = json_decode($json, true);

        $abiturient_id = $array['abiturient_id'];

        $cached_value = Redis::get($abiturient_id);

        if (isset($cached_value))
        {
            return new JsonResponse($cached_value, Response::HTTP_OK, [], true);   
        }

        $redisCacheActualizer = new RedisCacheActualizer();

        return $redisCacheActualizer->ActualizeCache($abiturient_id,  true);
    }

    public function getAllDirections(Request $request) : ?JsonResponse
    {
        $basicAuthentificator = new BasicAuthentificator();

        $auth_result = $basicAuthentificator->IsUserExistsAndTokenValid($request);

        if ($auth_result != null)
        {
            return $auth_result;
        }
        
        $json = $request->getContent();

        $array = json_decode($json, true);

        $directions_db = Direction::all();

        $directions = array();

        foreach ($directions_db as $current_item)
        {
            $directionLink = new DirectionShortLink($current_item['id'], $current_item['caption']);

            array_push($directions, $directionLink);
        }

        $directionsContent = new DirectionsShortContent($directions);

        $responseModel = new GetDirectionsResponse(
            $array['abiturient_id'], 
            $array['token'], 
            $directionsContent, 
            null, 
            true);

        $jsonResponse = json_encode($responseModel);

        return new JsonResponse($jsonResponse, Response::HTTP_OK, [], true);   
    }

    public function getDirectionSnapshot(Request $request) : ?JsonResponse
    {
        $basicAuthentificator = new BasicAuthentificator();

        $old_request = $request;

        $auth_result = $basicAuthentificator->IsUserExistsAndTokenValid($request);

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
            $abiturient= Abiturient::where('id', $current_item['abiturient_id'])->first();

            $abiturient_name = $abiturient['first_name'] . ' ' . $abiturient['second_name'];

            $place = new PlaceSnapshot(
                $current_item['place'], 
                $current_item['abiturient_id'], 
                $abiturient_name,
                $current_item['mark'],
                $current_item['admission_status'],
                $current_item['prioritet_number'],
                $current_item['has_diplom_original']);

            array_push($places, $place);
        }

        $placesContent = new DirectionSnapshotContent(
            $direction_id, 
            $direction_name, 
            $direction['budget_places_number'],
            $direction['min_ball'],
            $places);

        $responseModel = new GetDirectionSnapshotResponse(
            $array['abiturient_id'], 
            $array['token'], 
            $placesContent, 
            null, 
            true);

        $jsonResponse = json_encode($responseModel);

        return new JsonResponse($jsonResponse, Response::HTTP_OK, [], true);   
    }

    private $headers = ['Content-Type' => 'application/json', 'Access-Control-Allow-Origin' => '*'];
}