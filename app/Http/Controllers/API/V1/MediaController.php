<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\V1\FileUpload\FileUploads;
use App\Models\V1\Media\Medias;
use App\Models\V1\MediaCopyright\MediaCopyRight;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\Auth;

use Illuminate\Validation\Rules\File;

class MediaController extends Controller
{

    public $response = [];

    public function getAllUpload(Request $request){
        try{

            $medias = DB::table('medias')
            ->join('file_uploads', 'file_uploads.media_id', '=', 'medias.id')
            ->paginate(15);

            $this->response = [
                'data' => $medias,
                'status' => true,
                'message' => "success",
                'error' => false
            ];

            return response()->json($this->response, 200, [], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

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

            $validator = Validator::make($request->all(), [
                'thumbnail' => [
                    'required',
                    File::image()
                        // ->min(1 * 1024)
                        ->max(1024 * 1024 * 1024), // 1gb
                ],
                'video' => [
                    'required',
                    File::types(['mp4'])
                        // ->min(1 * 1024)
                        ->max(1024 * 1024 * 1024), // 1gb
                ],
                'audio' => [
                    'required',
                    File::types(['mp3'])
                        // ->min(1 * 1024)
                        ->max(1024 * 1024 * 1024), // 1gb
                ],
                'copyright' => ['required'],
                'proofID' => [
                    'required',
                    File::types(['pdf', 'jpg', 'png'])
                        // ->min(1 * 1024)
                        ->max(1024 * 1024 * 1024), // 1gb
                ],
                'media_title' => ['required', 'string', 'max:100', 'min:2'],
                'media_description' => ['required', 'string', 'max:200', 'min:5'],
                'media_description' => ['required', 'string', 'max:200', 'min:5'],
                'copyright_media_id' => ['required', 'string', 'max:255', 'min:2'],
                'copyright_owner_information' => ['required', 'string', 'max:255', 'min:5']
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors();

                $this->response = [
                    'status' => false,
                    'message' => $errors,
                    'error' => false
                ];

                return response()->json($this->response, 422); // 422 Unprocessable Entity - Validation Error
            }

            $has_thumbnail = $request->hasFile('thumbnail'); // jpeg|png
            $has_video = $request->hasFile('video'); // mp4
            $has_audio = $request->hasFile('audio'); // mp3
            $has_copyright = $request->hasFile('copyright'); // .txt
            $has_proofID = $request->hasFile('proofID'); // pdf|jpeg|png

            if($has_thumbnail && $has_video && $has_audio && $has_copyright && $has_proofID){

                DB::beginTransaction();

                $media = new Medias;
                $media->media_title = $request->media_title;
                $media->media_description =  $request->media_description;
                $media->user_id = Auth::user()->id;
                $media->save();

                $MediaCopyRight = new MediaCopyRight;
                $MediaCopyRight->copyright_media_id = $request->copyright_media_id;
                $MediaCopyRight->copyright_owner_information = $request->copyright_owner_information;
                $MediaCopyRight->user_id = Auth::user()->id;
                $MediaCopyRight->media_id = $media->id;
                $MediaCopyRight->save();

                $file_arr = array(
                    ["file" => $request->file('thumbnail'), "type" => 'thumbnail'],
                    ["file" => $request->file('video'), "type" => 'video'],
                    ["file" => $request->file('audio'), "type" => 'audio'],
                    ["file" => $request->file('copyright'), "type" => 'copyright'],
                );

                foreach ($file_arr as $key => $value) {
                    //origin name
                    $origin_name = $value['file']->getClientOriginalName();
                    //size
                    $size = $value['file']->getSize();
                    //duration
                    $duration = null;

                    if($value['file']->guessClientExtension() == "mp3" || $value['file']->guessClientExtension() == "mp4"){
                        // $duration =  $request->duration;
                        $duration =  null;
                    }
                    //extension
                    $extension = $value['file']->guessClientExtension();
                    //mimetype
                    $mimetype = $value['file']->getClientMimeType();

                    //url
                    $date = new DateTime();
                    $type = explode("/", $mimetype);
                    $url = $value['file']->storeAs('file',md5_file($value['file']->getRealPath()) . $date->getTimestamp() . "." . ($type[0] === "video" ? "mp4" : $value['file']->guessClientExtension()),'public');

                    $FileUploads = new FileUploads;
                    $FileUploads->origin_name = $origin_name;
                    $FileUploads->size = $size;
                    $FileUploads->duration = $duration;
                    $FileUploads->extension = $extension;
                    $FileUploads->mime_type = $mimetype;
                    $FileUploads->url = $url;
                    $FileUploads->upload_type = $value['type']; // thumbnail|video|audio|copyright|proofID
                    $FileUploads->media_id = $media->id;
                    $FileUploads->save();
                }

                DB::commit();
            }

            $this->response = [
                'status' => true,
                'message' => "success",
                'error' => false
            ];

            return response()->json($this->response, 200, [], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        }catch (Exception $exception) {
            DB::rollback();
            $this->response = [
                'status' => false,
                'message' => $exception->getMessage(),
                'error' => false
            ];

            return response()->json($this->response, 500); // 500 Internal Server Error
        }
    }
}