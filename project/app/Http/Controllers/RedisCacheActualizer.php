<?php

namespace App\Http\Controllers;

use App\Contracts\DirectionLink;
use App\Contracts\Responses\DefaultResponse;
use App\Contracts\Responses\GetUserLkContentResponse;
use App\Contracts\UserLkContent;
use App\Models\Abiturient;
use App\Models\AbiturientDirectionLink;
use App\Models\Direction;
use Illuminate\Support\Facades\Redis;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class RedisCacheActualizer
{
    public final function ActualizeCache($abiturient_id, $need_to_return): ?JsonResponse
    {
        $existed_user = Abiturient::where('id', $abiturient_id)->first();

        if (!isset($existed_user))
        {
            $failResponseModel = new DefaultResponse(
                null,
                'Abiturient with id ' . strval($abiturient_id) . ' does not exist',
                false);

            return new JsonResponse(json_encode($failResponseModel), Response::HTTP_BAD_REQUEST, [], true);  
        }

        $directions_links_db = AbiturientDirectionLink::where('abiturient_id', $abiturient_id)->get();

        error_log(count( $directions_links_db));

        $directions_links = array();

        foreach($directions_links_db as $current_item)
        {
            $direction_id = $current_item['direction_id'];

            $direction = Direction::where('id', $direction_id)->first();

            if (!isset($direction))
            {
                $failResponseModel = new DefaultResponse(
                    null,
                    'Direction with id ' . strval($direction_id) . ' does not exist',
                    false);
    
                return new JsonResponse(json_encode($failResponseModel), Response::HTTP_BAD_REQUEST, [], true);  
            }

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
            $existed_user['is_enrolled'],
            $directions_links
        );

        $responseModel = new GetUserLkContentResponse(
            $abiturient_id, 
            $existed_user['token'], 
            $directionsLinksContent, 
            null, 
            true);

        $jsonResponse = json_encode($responseModel);

        Redis::set($abiturient_id, $jsonResponse); 
        
        if ($need_to_return)
        {
            return new JsonResponse($jsonResponse, Response::HTTP_OK, [], true);   
        }
        else
        {
            return null;
        }
    }
}