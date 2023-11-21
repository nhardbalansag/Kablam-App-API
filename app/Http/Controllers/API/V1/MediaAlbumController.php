<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

use App\Models\V1\MediaAlbum\MediaAlbums;
use App\Models\V1\Album\Albums;

class MediaAlbumController extends Controller
{
    public $response = [];

    public function CreateAlbumAndMoveMediaToAlbum(Request $request){
        try{
            $MediaAlbums = "";

            $validator = Validator::make($request->all(), [
                'media_album_title' => ['required', 'string', 'max:100', 'unique:albums'],
                'media_id' => ['required']
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors();

                $this->response = [
                    'data' => null,
                    'status' => false,
                    'message' => $errors,
                    'error' => false
                ];

                return response()->json($this->response, 422); // 422 Unprocessable Entity - Validation Error
            }

            //create album
            $Albums = Albums::create([
                'media_album_title' => $request->media_album_title,
                'user_id' => Auth::user()->id
            ]);

            //create reference for album and media

            $MediaAlbums = MediaAlbums::create([
                'albums_id' => $Albums->id,
                'medias_id' => $request->media_id,
                'users_id' => Auth::user()->id
            ]);

            $this->response = [
                'data' => $MediaAlbums,
                'status' => true,
                'message' => "success",
                'error' => false
            ];

            return response()->json($this->response, 200, [], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        }catch (Exception $exception) {
            $this->response = [
                'data' => $MediaAlbums,
                'status' => false,
                'message' => $exception->getMessage(),
                'error' => false
            ];

            return response()->json($this->response, 500); // 500 Internal Server Error
        }
    }

    public function MoveToAlbum(Request $request){
        try{
            $MediaAlbums = "";

            $validator = Validator::make($request->all(), [
                'media_id' => ['required'],
                'album_id' => ['required']
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors();

                $this->response = [
                    'data' => null,
                    'status' => false,
                    'message' => $errors,
                    'error' => false
                ];

                return response()->json($this->response, 422); // 422 Unprocessable Entity - Validation Error
            }

            $media_existence_in_album = DB::select(
                'select * from media_albums where albums_id = ? AND medias_id = ?',
                [$request->album_id, $request->media_id]
            );

            if(count($media_existence_in_album) > 0){
                $this->response = [
                    'data' => null,
                    'status' => false,
                    'message' => [
                        'album' => array("media is already exist on this album")
                    ],
                    'error' => false
                ];

                return response()->json($this->response, 422); // 422 Unprocessable Entity - Validation Error
            }

            $MediaAlbums = MediaAlbums::create([
                'albums_id' => $request->album_id,
                'medias_id' => $request->media_id,
                'users_id' => Auth::user()->id
            ]);

            $this->response = [
                'data' => $MediaAlbums,
                'status' => true,
                'message' => "success",
                'error' => false
            ];

            return response()->json($this->response, 200, [], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        }catch (Exception $exception) {
            $this->response = [
                'data' => $MediaAlbums,
                'status' => false,
                'message' => $exception->getMessage(),
                'error' => false
            ];

            return response()->json($this->response, 500); // 500 Internal Server Error
        }
    }

    public function getRelatedAlbums(Request $request, $id){
        try{
            $Albums = Albums::with('albums')->where('user_id', $id)->paginate(15);

            // $medias = DB::table('medias')
            // ->leftJoin('file_uploads', 'file_uploads.media_id', '=', 'medias.id')
            // ->where('file_uploads.upload_type', 'thumbnail')
            // ->paginate(15);

            $this->response = [
                'data' => $Albums,
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

    public function getUserAlbums(Request $request){
        try{
            $Albums = Albums::where('user_id', Auth::user()->id)->paginate(15);

            // $medias = DB::table('medias')
            // ->leftJoin('file_uploads', 'file_uploads.media_id', '=', 'medias.id')
            // ->where('file_uploads.upload_type', 'thumbnail')
            // ->paginate(15);

            $this->response = [
                'data' => $Albums,
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

}
