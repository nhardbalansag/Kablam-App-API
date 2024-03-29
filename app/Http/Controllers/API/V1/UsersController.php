<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\V1\Roles\Query\RolesQueryBuilder;

class UsersController extends Controller
{

    public $response = [];

    public function register(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'first_name' => ['required', 'string', 'max:100'],
                'last_name' => ['required', 'string', 'max:100'],
                'number' => ['required', 'string', 'max:20', 'unique:users'],
                'birthday' => ['required'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'string', 'min:3']
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors();

                $this->response = [
                    'token' => null,
                    'information' => null,
                    'status' => false,
                    'errors' => $errors
                ];

                return response()->json($this->response, 422); // 422 Unprocessable Entity - Validation Error
            }

            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'birthday' => Carbon::parse($request->birthday)->format('Y-m-d'),
                'role_id' => 1,
                'number' => $request->number,
                'login_type' => $request->login_type,
                'user_photo' => $request->user_photo,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);

            $userID = $user->id;

            $user_information = User::where('id', $userID)->first();

            $token = $user_information->createToken('authToken')->accessToken;

            $this->response = [
                'token' => $token,
                'information' => $user_information,
                'status' => true,
                'error' => null
            ];

            return response()->json($this->response, 200, [], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        } catch (Exception $exception) {
            $this->response = [
                'token' => null,
                'information' => null,
                'status' => false,
                'error' => $exception->getMessage()
            ];

            return response()->json($this->response, 500); // 500 Internal Server Error
        }
    }

    public function login(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'email' => ['required', 'string', 'email', 'max:255'],
                'password' => ['required', 'string', 'min:3']
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors();

                $this->response = [
                    'token' => null,
                    'information' => null,
                    'status' => false,
                    'errors' => $errors
                ];

                return response()->json($this->response, 422); // 422 Unprocessable Entity - Validation Error
            }

            $user = DB::table('users')
                    ->where('email', $request->email)
                    ->first();

            if(!empty($user)){
                $enteredPassword = $request->password;
                $enteredEmail = $request->email;

                $DBpassword = $user->password;
                $DBemail = $user->email;

                $password = Hash::check($enteredPassword, $DBpassword);
                $user_info = User::where('email', $request->email)->first();

                if($password && $enteredEmail === $DBemail){
                    $token = $user_info->createToken('authToken')->accessToken;

                    $this->response = [
                        'token' => $token,
                        'information' => $user_info,
                        'status' => true,
                        'error' => null
                    ];
                }else if(!$password){
                    $this->response = [
                        'token' => null,
                        'information' => null,
                        'status' => false,
                        'error' => [
                            "phone_verification" => ["Password is Incorrect."]
                        ]
                    ];
                }else{
                    $this->response = [
                        'token' => null,
                        'information' => null,
                        'status' => false,
                        'error' => [
                            "phone_verification" => ["The phone number is not verified."]
                        ]
                    ];
                }

            }else{
                $this->response = [
                    'token' => null,
                    'information' => null,
                    'status' => false,
                    'error' => [
                        "authentication" => ["The user is not exist."]
                    ]
                ];
            }

            return response()->json($this->response, 200, [], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        } catch (Exception $exception) {
            $this->response = [
                'token' => null,
                'information' => null,
                'status' => false,
                'error' => $exception->getMessage()
            ];

            return response()->json($this->response, 500); // 500 Internal Server Error
        }
    }

    public function GetUserInformation(Request $request){
        try{
            $user_information = RolesQueryBuilder::getUserInformation();

            $this->response = [
                'data' => $user_information,
                'status' => true,
                'error' => null
            ];

            return response()->json($this->response, 200, [], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        }catch(Exception $exception){
            $this->response = [
                'data' => null,
                'status' => false,
                'error' => $exception->getMessage()
            ];

            return response()->json($this->response, 500); // 500 Internal Server Error
        }
    }

    public function socialLoginRegister(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                // 'first_name' => ['required', 'string', 'max:100'],
                // 'last_name' => ['required', 'string', 'max:100'],
                // 'number' => ['required', 'string', 'max:20', 'unique:users'],
                // 'birthday' => ['required'],
                'email' => ['required', 'string', 'email', 'max:255'],
                'social_login_user_id' => ['required', 'string'],
                'password' => ['required', 'string', 'min:3']
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors();

                $this->response = [
                    'token' => null,
                    'information' => null,
                    'status' => false,
                    'errors' => $errors
                ];

                return response()->json($this->response, 422); // 422 Unprocessable Entity - Validation Error
            }

            $user = DB::table('users')
                    ->where('social_login_user_id', $request->social_login_user_id)
                    ->first();

            if(!empty($user)){
                $enteredPassword = $request->password;
                $enteredEmail = $request->email;

                $DBpassword = $user->password;
                $DBemail = $user->email;

                $password = Hash::check($enteredPassword, $DBpassword);
                $user_info = User::where('social_login_user_id', $request->social_login_user_id)->first();

                if($password && $enteredEmail === $DBemail){
                    $token = $user_info->createToken('authToken')->accessToken;

                    $this->response = [
                        'token' => $token,
                        'information' => $user_info,
                        'status' => true,
                        'error' => null
                    ];
                }
            }else{
                $user = User::create([
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'birthday' => Carbon::parse($request->birthday)->format('Y-m-d'),
                    'role_id' => 1,
                    'number' => $request->number,
                    'login_type' => $request->login_type,
                    'social_login_user_id' => $request->social_login_user_id,
                    'user_photo' => $request->user_photo,
                    'email' => $request->email,
                    'password' => Hash::make($request->password)
                ]);

                $userID = $user->id;

                $user_information = User::where('id', $userID)->first();

                $token = $user_information->createToken('authToken')->accessToken;

                $this->response = [
                    'token' => $token,
                    'information' => $user_information,
                    'status' => true,
                    'error' => null
                ];
            }

            return response()->json($this->response, 200, [], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        } catch (Exception $exception) {
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
