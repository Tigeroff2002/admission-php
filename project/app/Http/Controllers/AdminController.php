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

        for ($i = 0; $i < count($places_db); $i++)
        {
            $current_item = $places_db[$i];

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

    function compare($a, $b)
    {
        return $a['mark'] > $b['mark'];
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

        $direction_budget_count = $direction['budget_places_number'];
        $min_ball = $direction['min_ball'];

        for ($i = 0; $i < count($places_db); $i++)
        {
            $current_item = $places_db[$i];

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
}