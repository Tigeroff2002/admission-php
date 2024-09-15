<?php

namespace App\Http\Controllers;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Contracts\Responses\DefaultResponse;
use App\Models\Abiturient;
use Illuminate\Http\Request;

class BasicAuthentificator
{
    public function IsUserExistsAndTokenValid(Request $request) : ?JsonResponse
    {
        $request->validate(['abiturient_id' => 'required', 'token' => 'required']);

        $json = $request->getContent();

        $array = json_decode($json, true);

        $existed_user = Abiturient::where('id', $array['abiturient_id'])->first();

        if (!isset($existed_user))
        {
            $failResponseModel = new DefaultResponse(
                null,
                'User with id ' . strval($array['abiturient_id']) . ' does not exist not already existed',
                false);

            return new JsonResponse(json_encode($failResponseModel), Response::HTTP_BAD_REQUEST, [], true);          
        }

        if ($existed_user['token'] != $array['token'])
        {
            $failResponseModel = new DefaultResponse(
                null,
                'Tokens not equals. Go to login page',
                false);

            return new JsonResponse(json_encode($failResponseModel), Response::HTTP_BAD_REQUEST, [], true);                
        }

        return null;
    }

    public function IsUserExistsAndTokenValidAndAdmin(Request $request): ?JsonResponse
    {
        $request->validate(['abiturient_id' => 'required', 'token' => 'required']);

        $json = $request->getContent();

        $array = json_decode($json, true);

        $existed_user = Abiturient::where('id', $array['abiturient_id'])->first();

        if ($this->IsUserExistsAndTokenValid($request) != null)
        {
            if (!$existed_user['is_admin'])
            {
                $failResponseModel = new DefaultResponse(
                    null,
                    'User with id' . strval($array['abiturient_id'] . 'is not admin'),
                    false);
    
                return new JsonResponse(json_encode($failResponseModel), Response::HTTP_BAD_REQUEST, [], true);                  
            }

            return null;
        }

        return null;
    }
}