<?php

namespace App\Http\Controllers;

use App\Contracts\AbiturientLink;
use App\Contracts\AllAbiturientsContent;
use App\Contracts\DirectionLink;
use App\Contracts\DirectionLinksList;
use App\Contracts\DirectionShortLink;
use App\Contracts\DirectionSnapshotContent;
use App\Contracts\DirectionsShortContent;
use App\Contracts\Responses\GetAllAbiturientsResponse;
use App\Contracts\Responses\GetDirectionSnapshotResponse;
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
use App\Contracts\PlaceSnapshot;
use Symfony\Component\HttpFoundation\Response;
use App\Contracts\Requests\RegisterRequest;
use App\Contracts\Requests\LoginRequest;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
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

            return new JsonResponse(json_encode($failResponseModel), Response::HTTP_BAD_REQUEST, [], true);
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
            'token' => $token
        ]);

        $abiturient->save();

        $created_abiturient = Abiturient::where('email', $user_email)->first();

        $abiturient_id = $created_abiturient['id'];

        $successResponseModel = new ResponseWithId($abiturient_id, $token, null, null, true);

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

        $user_email = $array['email'];
        $user_password = $array['password'];

        $existed_user = Abiturient::where('email', $user_email)->first();

        if (!isset($existed_user))
        {
            $failResponseModel = new DefaultResponse(
                null,
                'User with email ' . $user_email . ' is not already existed',
                false);

            return new JsonResponse(json_encode($failResponseModel), Response::HTTP_BAD_REQUEST, [], true);
        }

        if ($existed_user['password'] != $user_password)
        {
            $failResponseModel = new DefaultResponse(
                null,
                'Passwords not equals',
                false);

            return new JsonResponse(json_encode($failResponseModel), Response::HTTP_BAD_REQUEST, [], true);
        }

        $guid = new GUID();

        $token = $guid->NewGUID();

        DB::update('update abiturients set token = ?', [$token]);

        $successResponseModel = new ResponseWithId($existed_user['id'], $token, null, null, true);

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

        $cached_value = Redis::get(1);

        if (isset($cached_value))
        {
            return new JsonResponse($cached_value, Response::HTTP_OK, [], true);   
        }

        $existed_user = Abiturient::where('id', $abiturient_id)->first();

        $directions_links_db = AbiturientDirectionLink::where('abiturient_id', $abiturient_id)->get();

        $directions_links = array();

        for ($i = 0; $i < count($directions_links_db); $i++)
        {
            $current_item = $directions_links_db[$i];

            $direction_id = $current_item['direction_id'];

            $direction = Direction::where('id', $direction_id)->first();

            $directionLink = 
                new DirectionLink(
                    $direction_id, 
                    $direction['caption'], 
                    $current_item['place'], 
                    $current_item['mark'], 
                    $current_item['admission_status'],
                    $current_item['prioritet_number']);

            array_push($directions_links, $directionLink);
        }

        $directionsLinksContent = new UserLkContent(
            $existed_user['first_name'], 
            $existed_user['second_name'],
            $existed_user['email'],
            $existed_user['has_diplom_original'],
            $directions_links
        );

        $responseModel = new GetUserLkContentResponse(
            $abiturient_id, 
            $array['token'], 
            $directionsLinksContent, 
            null, 
            true);

        $jsonResponse = json_encode($responseModel);

        Redis::set($abiturient_id, $jsonResponse);

        return new JsonResponse($jsonResponse, Response::HTTP_OK, [], true);   
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

        for ($i = 0; $i < count($directions_db); $i++)
        {
            $current_item = $directions_db[$i];

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

        for ($i = 0; $i < count($places_db); $i++)
        {
            $current_item = $places_db[$i];

            $abiturient_name = Abiturient::where('id', $current_item['abiturient_id'])->first();

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

        $placesContent = new DirectionSnapshotContent($direction_id, $direction_name, $places);

        $responseModel = new GetDirectionSnapshotResponse(
            $array['abiturient_id'], 
            $array['token'], 
            $placesContent, 
            null, 
            true);

        $jsonResponse = json_encode($responseModel);

        return new JsonResponse($jsonResponse, Response::HTTP_OK, [], true);   
    }
}