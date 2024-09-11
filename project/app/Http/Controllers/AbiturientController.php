<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\MessageBag;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use App\Models\Abiturient;

use App\Models\RawUser;

class AbiturientController extends Controller
{
    public function index() : JsonResponse
    {
        return new JsonResponse("{empty_endpoint}");
    }

    public function loginPost(SerializerInterface $serializer, Request $request) : JsonResponse
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required'
          ]);      

        $json = file_get_contents('php://input');

        $data = json_decode($json, true);

        $id = $data['abiturient_id'];
        
        $existed_user = Abiturient::where('id', $id)->first();

        if (!isset($existed_user))
        {
            $failed_data = $serializer->serialize("", JsonEncoder::FORMAT);
            return new JsonResponse($failed_data, Response::HTTP_BAD_REQUEST, [], false);           
        }

        $data = $serializer->serialize($existed_user, JsonEncoder::FORMAT);
        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }
}