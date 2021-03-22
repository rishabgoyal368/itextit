<?php

namespace App\Http\Controllers;

use JWTAuth;
use Validator;
use IlluminateHttpRequest;
use AppHttpRequestsRegisterAuthRequest;
use TymonJWTAuthExceptionsJWTException;
use SymfonyComponentHttpFoundationResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\User;
use App\Admin;
use Mail, Hash, Auth, File;

class ApiController extends Controller
{
    /**
     * @OA\Info(
     *   title="Itextit API Documentation",
     *   version="1.0",
     *   @OA\Contact(
     *     email="@.com",
     *     name="Support Team"
     *   )
     * )
     */


    /**
     * @OA\SecurityScheme(
     * type="apiKey",
     * description="Login with email and password to get the authentication token",
     * name="Authorization",
     * in="header",
     * scheme="bearer",
     * bearerFormat="JWT",
     * securityScheme="apiAuth",
     * )
     */


    /**
     *  
     * 
     * @OA\Post(
     *     path="/api/register",
     *     operationId="register",
     *     description="User register",
     *     tags={"User"},
     *     @OA\Parameter(
     *          name="full_name",
     *          in="query",
     *          required=true, 
     *          @OA\Schema(
     *           type="string",
     *          )
     *      ),
     *     @OA\Parameter(
     *          name="email",
     *          in="query",
     *          required=true, 
     *          @OA\Schema(
     *           type="string",
     *          )
     *      ),
     *     @OA\Parameter(
     *          name="mobile_number",
     *          in="query",
     *          required=true, 
     *          @OA\Schema(
     *           type="string",
     *          )
     *      ),
     *     @OA\Parameter(
     *          name="password",
     *          in="query",
     *          required=true, 
     *          @OA\Schema(
     *           type="string",
     *          )
     *      ),
     * 
     *      @OA\Parameter(
     *          name="password_confirmation",
     *          in="query",
     *          required=true, 
     *          @OA\Schema(
     *           type="string",
     *          )
     *      ),
     * 
     *       @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Success response.")
     *          )
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Error: Bad Request",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Parameters Missing")
     *          )
     *      )
     *  )
     */
    public function user_registration(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make(
            $data,
            [
                'full_name' =>  'required',
                'email' => 'required|email|unique:users,email,Null,id,deleted_at,NULL',
                'mobile_number' => 'required|numeric|unique:users,mobile_number',
                'password' => 'required|confirmed',
            ]
        );
        if ($validator->fails()) {
            $response['code'] = 404;
            $response['status'] = $validator->errors()->first();
            $response['message'] = "missing parameters";
            return response()->json($response);
        }
        $hash_password          = Hash::make($data['password']);
        $data['password'] = $hash_password;
        $data['login_type'] = User::EMAILLOGINTYPE;
        $data['status'] = User::ACTIVESTATUS;
        $user = User::addEdit($data);
        if ($user->save()) {
            $project_name = env('App_name');
            $email = $data['email'];
            try {
                if (!filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
                    Mail::send('emails.user_register_success', ['name' => ucfirst($user['first_name']) . ' ' . $user['last_name'], 'email' => $email, 'password' => $user['password']], function ($message) use ($email, $project_name) {
                        $message->to($email, $project_name)->subject('User registered successfully');
                    });
                }
            } catch (Exception $e) {
            }
            return response()->json(['message' => 'User register Successfuly', 'data' => $user, 'code' => 200]);
        } else {
            return response()->json(['message' => 'Something went wrong', 'code' => 400]);
        }
    }


    /** 
     * @OA\Post(
     *     path="/api/login",
     *     operationId="login",
     *     description="User login",
     *     tags={"User"},
     * 
     *     @OA\Parameter(
     *          name="email",
     *          in="query",
     *          required=true, 
     *          @OA\Schema(
     *           type="string",
     *          )
     *      ),
     *     @OA\Parameter(
     *          name="password",
     *          in="query",
     *          required=true, 
     *          @OA\Schema(
     *           type="string",
     *          )
     *      ),
     * 
     *       @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Success response.")
     *          )
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Error: Bad Request",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Parameters Missing")
     *          )
     *      )
     *  )
     */
    public function user_login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        $validator = Validator::make(
            $request->all(),
            [
                'email'      => 'required|email',
                'password'   => 'required'
            ]
        );
        if ($validator->fails()) {
            $response['code'] = 404;
            $response['status'] = $validator->errors()->first();
            $response['message'] = "missing parameters";
            return response()->json($response);
        }
        $token = auth()->attempt($credentials);
        if ($token) {
            $user = auth()->userOrFail();
            return response()->json(['message' => 'User login Successfuly', 'token' => $token, 'data' => $user, 'code' => 200]);
        } else {
            return response()->json(['message' => 'Something went wrong', 'code' => 400]);
        }
    }

    /** 
     * @OA\Post(
     *     path="/api/get-profile",
     *     operationId="get-profile",
     *     description="User profile",
     *     tags={"User"},
     *     security={{ "apiAuth": {} }},
     *
     * 
     *       @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Success response.")
     *          )
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Error: Bad Request",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Parameters Missing")
     *          )
     *      )
     *  )
     */
    public function profile(Request $request)
    {
        try {
            $user = auth()->userOrFail();
            return response()->json(['message' => 'User Profile', 'data' => $user, 'code' => 200]);
        } catch (\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e) {
            return response()->json(['message' => 'Something went wrong, Please try again later.', 'code' => 400]);
        }
    }

    public function deleteImage($designation, $image)
    {
        if ($image && file_exists(public_path('uploads/' . $image))) {
            unlink($designation . $image);
        }
    }

    /** 
     * @OA\Post(
     *     path="/api/update-profile",
     *     operationId="update-profile",
     *     description="udpate user profile",
     *     tags={"User"},
     *     security={{ "apiAuth": {} }},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(      
     *                 @OA\Property(
     *                     description="file to upload",
     *                     property="profile_image",
     *                     type="file",
     *                     format="file",
     *                 ),
     *             )
     *         ),
     *     ),
     *      @OA\Parameter(
     *          name="full_name",
     *          in="query",
     *          required=true, 
     *          @OA\Schema(
     *           type="string",
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="email",
     *          in="query",
     *          required=true, 
     *          @OA\Schema(
     *           type="string",
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="mobile_number",
     *          in="query",
     *          required=true, 
     *          @OA\Schema(
     *           type="string",
     *          )
     *      ), 
     * 
     *       @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Success response.")
     *          )
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Error: Bad Request",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Parameters Missing")
     *          )
     *      )
     *  )
     */
    public function updateProfile(Request $request)
    {
        $user =   auth()->userOrFail();
        $data = $request->all();
        $data['id'] = $user['id'];
        $validator = Validator::make(
            $data,
            [
                'full_name' =>  'required',
                'email' => 'required|email|unique:users,email,' . @$data['id'] . ',id,deleted_at,NULL',
                'mobile_number' => 'required|unique:users,mobile_number,' . @$data['id'] . ',id,deleted_at,NULL',
                'profile_image' => 'nullable|mimes:jpeg,jpg,png,gif|max:10000',
            ]
        );

        if ($validator->fails()) {
            $response['code'] = 404;
            $response['status'] = $validator->errors()->first();
            $response['message'] = "missing parameters";
            return response()->json($response);
        }
        $old_image = $user->getAttributes()['profile_image'];
        $user->full_name         = $data['full_name'];
        $user->email              = $data['email'];
        $user->mobile_number     = $data['mobile_number'];
        $user->refrence_id     = @$data['refrence_id'];
        $user->calender_id     = @$data['calender_id'];
        if (@$data['profile_image']) {
            $fileName = time() . '.' . $request->profile_image->extension();
            $request->profile_image->move(public_path('uploads'), $fileName);
            $user->profile_image     = $fileName;
            $this->deleteImage('uploads/', $old_image);
        } else {
            $user->profile_image = $old_image;
        }
        $user->save();
        return response()->json(['data' => $user, 'message' => 'Profile updated successfully!', 'code' => 200]);
    }

    /** 
     * @OA\Post(
     *     path="/api/forgot-password",
     *     operationId="forgot-password",
     *     description="user forget password",
     *     tags={"User"},
     * 
     *      @OA\Parameter(
     *          name="email",
     *          in="query",
     *          required=true, 
     *          @OA\Schema(
     *           type="string",
     *          )
     *      ),
     *      
     *       @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Success response.")
     *          )
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Error: Bad Request",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Parameters Missing")
     *          )
     *      )
     *  )
     */
    public function forgot_password(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'email'      => 'required|email',
            ]
        );

        if ($validator->fails()) {
            $response['code'] = 404;
            $response['status'] = $validator->errors()->first();
            $response['message'] = "missing parameters";
            return response()->json($response);
        }


        $check_email_exists = User::where('email', $request['email'])->first();
        if (empty($check_email_exists)) {
            $response['code'] = 404;
            $response['status'] = 'Email not exists';
            $response['message'] = "missing parameters";
            return response()->json($response);
        }


        $check_email_exists->otp =  rand(1111, 9999);
        if ($check_email_exists->save()) {
            $project_name = env('App_name');
            $email = $request['email'];
            try {
                if (!filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
                    Mail::send('emails.user_forgot_password_api', ['name' => ucfirst($check_email_exists['first_name']) . ' ' . $check_email_exists['last_name'], 'otp' => $check_email_exists['otp']], function ($message) use ($email, $project_name) {
                        $message->to($email, $project_name)->subject('User Forgot Password');
                    });
                }
            } catch (Exception $e) {
            }
            return response()->json(['message' => 'OTP Send Successfully', 'code' => 200]);
        } else {
            return response()->json(['message' => 'Something went wrong, Please try again later.', 'code' => 400]);
        }
    }


    /** 
     * @OA\Post(
     *     path="/api/reset-password",
     *     operationId="reset-password",
     *     description="user Reset password",
     *     tags={"User"},
     * 
     *      @OA\Parameter(
     *          name="otp",
     *          in="query",
     *          required=true, 
     *          @OA\Schema(
     *           type="string",
     *          )
     *      ),
     *      
     *       @OA\Parameter(
     *          name="email",
     *          in="query",
     *          required=true, 
     *          @OA\Schema(
     *           type="string",
     *          )
     *      ),
     *       @OA\Parameter(
     *          name="password",
     *          in="query",
     *          required=true, 
     *          @OA\Schema(
     *           type="string",
     *          )
     *      ),
     *       @OA\Parameter(
     *          name="confirm_password",
     *          in="query",
     *          required=true, 
     *          @OA\Schema(
     *           type="string",
     *          )
     *      ),
     * 
     *       @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Success response.")
     *          )
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Error: Bad Request",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Parameters Missing")
     *          )
     *      )
     *  )
     */
    public function reset_password(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make(
            $request->all(),
            [
                'otp'       =>  'required|numeric',
                'email'      => 'required|email',
                'password'   => 'required',
                'confirm_password' => 'required_with:password|same:password'
            ]
        );

        if ($validator->fails()) {
            $response['code'] = 404;
            $response['status'] = $validator->errors()->first();
            $response['message'] = "missing parameters";
            return response()->json($response);
        }


        $email = $data['email'];
        $check_email = User::where('email', $email)->first();
        if (empty($check_email['otp'])) {
            $response['code'] = 404;
            $response['status'] = "Something went wrong, Please try again later";
            $response['message'] = "missing parameters";
            return response()->json($response);
        }
        if (empty($check_email)) {
            return response()->json(['error' => 'This Email-id is not exists.']);
        } else {
            if ($check_email['otp'] == $data['otp']) {
                $hash_password                  = Hash::make($data['password']);
                $check_email->password          = str_replace("$2y$", "$2a$", $hash_password);
                $check_email->otp               = null;
                if ($check_email->save()) {
                    return response()->json(['message' => 'Password changed successfully.', 'code' => 200]);
                } else {
                    return response()->json(['error' => 'Something went wrong, Please try again later.', 'code' => 400]);
                }
            } else {
                return response()->json(['message' => 'OTP mismatch!', 'code' => 400]);
            }
        }
    }


    /** 
     * @OA\Post(
     *     path="/api/logout",
     *     operationId="logout",
     *     description="user logout",
     *     tags={"User"},
     *     security={{ "apiAuth": {} }},
     * 
     *       @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Success response.")
     *          )
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Error: Bad Request",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Parameters Missing")
     *          )
     *      )
     *  )
     */
    public function logout()
    {
        Auth::guard('api')->logout();
        return response()->json(['message' => 'Logout Successfully', 'code' => 200]);
    }


    public function respondWithToken($token)
    {
        return $token;
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'code' => 200,
            'expire_in' => auth()->factory()->getTTL() * 60
        ]);
    }
}
