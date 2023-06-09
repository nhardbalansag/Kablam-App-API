<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\V1\FileUpload\FileUploads;
use App\Models\V1\Media\Medias;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MediaController extends Controller
{

    public $response = [];

    public function getAllUpload(Request $request){
        try{

        }catch (Exception $exception) {
            $this->response = [
                'token' => null,
                'information' => null,
                'status' => false,
                'error' => $exception->getMessage()
            ];

            return response()->json($this->response, 500); // 500 Internal Server Error
        }
    }

    public function uploadMedia(Request $request){
        try{
            if($request->hasFile('uploaded_file')){

                foreach($request->file('uploaded_file') as $index => $file){
                    // $media = new Medias;
                    // $FileUploads = new FileUploads;

                    //origin name
                    $origin_name = $file->getClientOriginalName();
                    //sizex
                    // $size = $file->getClientSize();
                    //duration
                    // $duration =  $request->lesson_file_size[$index];
                    //extension
                    // $extension = $file->guessClientExtension();
                    //mimetype
                    // $mimetype = $file->getClientMimeType();

                    $this->response =  [
                        $origin_name ,
                        // $size ,
                        // // $duration ,
                        // $extension ,
                        // $mimetype
                    ];
                }

                $file = $request->file('uploaded_file');

                return response()->json($file, 200);
            }
        }catch (Exception $exception) {
            $this->response = [
                'status' => false,
                'error' => $exception->getMessage()
            ];

            return response()->json($this->response, 500); // 500 Internal Server Error
        }
    }
}
