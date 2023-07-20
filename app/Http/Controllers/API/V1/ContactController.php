<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use App\Mail\SendEmail;
use Exception;

class ContactController extends Controller
{

    public $response = [];

    public function SendEmail(Request $request){
        try{
            $references = [
                'nhardbalansag@gmail.com',
                'bernardbalansag01@gmail.com',
                'at@kablapp.com',
                'king@kingvaservices.com',
                'bernard@kingvaservices.com'
            ];

            Mail::to($references)->send(new SendEmail($request));

            $this->response = [
                'status' => true,
                'message' => "success",
                'error' => false
            ];

            return response()->json($this->response, 200);

        }catch(Exception $err){
            $this->response = [
                'status' => false,
                'message' => $err->getMessage(),
                'error' => true
            ];

            return response()->json($this->response, 500);
        }
    }
}
