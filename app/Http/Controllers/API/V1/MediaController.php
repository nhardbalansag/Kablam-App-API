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

    public function getUploadMediaByCalendarId (Request $request, $id){
        try{
            $medias = Medias::with('user', 'file')->where('user_calendar_premiere_id', $id)->get();

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

    public function getAllUserUploadedMedias(Request $request){
        try{
            $medias = Medias::with('user', 'file')->where('user_id', Auth::user()->id)->get();

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

    public function getAllUpload(Request $request){
        try{
            $medias = Medias::with('user', 'file')->paginate(15);

            // $medias = DB::table('medias')
            // ->leftJoin('file_uploads', 'file_uploads.media_id', '=', 'medias.id')
            // ->where('file_uploads.upload_type', 'thumbnail')
            // ->paginate(15);

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
            $uploadMediaResult = "";

            $validator = Validator::make($request->all(), [
                'thumbnail' => [
                    'required',
                    File::image()
                        // ->min(1 * 1024)
                        ->max(1024 * 1024 * 1024), // 1gb
                ],
                'content' => [
                    'required',
                    File::types(['mp4', 'mp3'])
                        // ->min(1 * 1024)
                        ->max(1024 * 1024 * 1024), // 1gb
                ],
                // 'audio' => [
                //     'required',
                //     File::types(['mp3'])
                //         // ->min(1 * 1024)
                //         ->max(1024 * 1024 * 1024), // 1gb
                // ],
                'copyright' => ['required'],
                'proofID' => [
                    'required',
                    File::types(['pdf', 'jpg', 'png'])
                        // ->min(1 * 1024)
                        ->max(1024 * 1024 * 1024), // 1gb
                ],
                'media_title' => ['required', 'string', 'max:100', 'min:2'],
                'media_description' => ['required', 'string', 'max:200', 'min:5'],
                'copyright_media_id' => ['required', 'string', 'max:255', 'min:2'],
                'copyright_owner_information' => ['required', 'string', 'max:255', 'min:5']
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors();

                $this->response = [
                    'data' => $uploadMediaResult,
                    'status' => false,
                    'message' => $errors,
                    'error' => false
                ];

                return response()->json($this->response, 422); // 422 Unprocessable Entity - Validation Error
            }

            $has_thumbnail = $request->hasFile('thumbnail'); // jpeg|png
            $has_content = $request->hasFile('content'); // mp4
            // $has_audio = $request->hasFile('audio'); // mp3
            $has_copyright = $request->hasFile('copyright'); // .txt
            $has_proofID = $request->hasFile('proofID'); // pdf|jpeg|png

            if($has_thumbnail && $has_content && $has_copyright && $has_proofID){

                DB::beginTransaction();

                $media = new Medias;
                $media->media_title = $request->media_title;
                $media->user_calendar_premiere_id = $request->user_calendar_premiere_id;
                $media->calendar_premiere_info = $request->calendar_premiere_info;
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
                    ["file" => $request->file('content'), "type" => 'content'],
                    // ["file" => $request->file('audio'), "type" => 'audio'],
                    ["file" => $request->file('copyright'), "type" => 'copyright'],
                    ["file" => $request->file('proofID'), "type" => 'proofID']
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
                    $FileUploads->medias_id = $media->id;
                    $FileUploads->save();
                }

                DB::commit();

                $uploadMediaResult = Medias::with('user', 'file')->where('id', $media->id)->first();
            }

            $this->response = [
                'data' => $uploadMediaResult,
                'status' => true,
                'message' => "success",
                'error' => false
            ];

            return response()->json($this->response, 200, [], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        }catch (Exception $exception) {
            DB::rollback();
            $this->response = [
                'data' => $uploadMediaResult,
                'status' => false,
                'message' => $exception->getMessage(),
                'error' => false
            ];

            return response()->json($this->response, 500); // 500 Internal Server Error
        }
    }

    public function deleteMedia(Request $request){
        try{
            $result = "";

            $deleted = DB::table('medias')->where('id', '=', $request->id)->delete();

            if($deleted){
                $result = $deleted;
            }

            $this->response = [
                'data' => $result,
                'status' => true,
                'message' => "success",
                'error' => false
            ];

            return response()->json($this->response, 200, [], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        }catch (Exception $exception) {
            $this->response = [
                'data' => $result,
                'status' => false,
                'message' => $exception->getMessage(),
                'error' => false
            ];

            return response()->json($this->response, 500); // 500 Internal Server Error
        }
    }
}
