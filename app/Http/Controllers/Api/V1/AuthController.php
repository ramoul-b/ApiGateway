<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use App\Models\Account;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;
use App\Http\Resources\UserResource;
use App\Services\ApiService;
use Illuminate\Support\Facades\Log;


/**
 * @OA\Info(
 *   title="AG BACKEND",
 *   version="1.0.0",
 *   description="AG BACKEND",
 * )
 */
class AuthController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register', 'forgotPassword', 'resetPassword', 'verifyEmail', 'resendVerificationEmail']]);
    }


    
/**
 * @OA\Post(
 *     path="/api/v1/register",
 *     operationId="registerUser",
 *     tags={"Auth"},
 *     summary="Register a new user",
 *     @OA\RequestBody(
 *         required=true,
 *         description="Pass user registration data",
 *         @OA\JsonContent(
 *             required={"name", "surname", "email", "password", "password_confirmation", "username", "role_id"},
 *             @OA\Property(property="name", type="string", example="John"),
 *             @OA\Property(property="surname", type="string", example="Doe"),
 *             @OA\Property(property="email", type="string", format="email", example="johndoe@example.com"),
 *             @OA\Property(property="password", type="string", example="password"),
 *             @OA\Property(property="password_confirmation", type="string", example="password"),
 *             @OA\Property(property="username", type="string", example="johndoe"),
 *             @OA\Property(property="role_id", type="integer", example=1)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="User registered successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="User registered successfully")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Validation errors",
 *         @OA\JsonContent(
 *             @OA\Property(property="errors", type="object")
 *         )
 *     )
 * )
 */
 public function register(Request $request)
 {
     try {
         $validator = Validator::make($request->all(), [
             'name' => 'required|string|max:255',
             'surname' => 'required|string|max:255',
             'email' => 'required|string|email|max:255|unique:users',
             'username' => 'required|string|max:255|unique:users',
             'password' => 'required|string|min:8',
             'role_id' => 'required|exists:roles,id',
         ]);
 
         if ($validator->fails()) {
             return ApiService::response($validator->errors(), 422);
         }
 
         $user = User::create([
             'name' => $request->name,
             'surname' => $request->surname,
             'email' => $request->email,
             'username' => $request->username,
             'password' => Hash::make($request->password),
         ]);
 
         $user->roles()->attach($request->role_id);
 
         return ApiService::response(new UserResource($user), 201);
     } catch (\Exception $e) {
         return response()->json(['error' => __('messages.operation_failed')], 500);
     }
 }

    /**
     * @OA\Post(
     *     path="/api/v1/login",
     *     operationId="loginUser",
     *     tags={"Auth"},
     *     summary="Authenticate a user",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="email", type="string", example="johndoe@example.com"),
     *             @OA\Property(property="password", type="string", example="password")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *     )
     * )
     */
    public function login(Request $request)
    {
        try {
            $credentials = $request->only('email', 'password');
            Log::debug('Login attempt with credentials: ', $credentials);
        
            if (Auth::attempt($credentials)) {
                $user = Auth::user();
                Log::debug('User authenticated: ', ['id' => $user->id]);
                $token = $user->createToken('YourAppName')->accessToken;
                Log::debug('Token generated: ', ['token' => $token]);
                Log::debug('Response data before returning from login: ', ['response' => ['user' => new UserResource($user), 'token' => $token]]);

                return ApiService::response(['user' => new UserResource($user), 'token' => $token]);
            } else {
                Log::debug('Authentication failed for email: ', ['email' => $credentials['email']]);
                return ApiService::response(['message' => __('messages.unauthorized')], 401);
            }
        } catch (\Exception $e) {
            Log::error('Login error: ', ['error' => $e->getMessage()]);
            return response()->json(['error' => __('messages.operation_failed')], 500);
        }
    }
    


    public function logout(Request $request)
    {
        try {
            $token = $request->user()->token();
            $token->revoke();
    
            return ApiService::response(['message' => __('messages.logout_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['error' => __('messages.operation_failed')], 500);
        }
    }


    /**
 * @OA\Post(
 *     path="/api/v1/forgot-password",
 *     operationId="forgotPassword",
 *     tags={"Auth"},
 *     summary="Send a password reset link to the given email",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="email", type="string", format="email", example="user@example.com")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Reset link sent successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Reset link sent successfully")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Validation errors"
 *     )
 * )
 */
public function forgotPassword(Request $request)
{
    try {
        $request->validate(['email' => 'required|email']);
        $status = Password::sendResetLink(
            $request->only('email')
        );
        return ApiService::response(['message' => __('messages.reset_link_sent_successfully')], 200);
    } catch (\Exception $e) {
        return response()->json(['error' => __('messages.operation_failed')], 500);
    }
}

    /**
 * @OA\Post(
 *     path="/api/v1/reset-password",
 *     operationId="resetPassword",
 *     tags={"Auth"},
 *     summary="Reset the user's password using a token",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
 *             @OA\Property(property="token", type="string", example="1"),
 *             @OA\Property(property="password", type="string", format="password", example="newPassword"),
 *             @OA\Property(property="password_confirmation", type="string", format="password", example="newPassword")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Password reset successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Password reset successfully")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Validation errors"
 *     )
 * )
 */
public function resetPassword(Request $request)
{
    try {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));
                $user->save();
                event(new PasswordReset($user));
            }
        );
        return $status === Password::PASSWORD_RESET
                    ? response()->json(['message' => __('messages.password_reset_successfully')])
                    : response()->json(['email' => [__($status)]], 422);
    } catch (\Exception $e) {
        return response()->json(['error' => __('messages.operation_failed')], 500);
    }
}

    /**
 * @OA\Post(
 *     path="/api/v1/refresh-token",
 *     operationId="refreshToken",
 *     tags={"Auth"},
 *     summary="Refresh the authentication token",
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Token refreshed successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="access_token", type="string", example="newlyRefreshedToken"),
 *             @OA\Property(property="token_type", type="string", example="bearer"),
 *             @OA\Property(property="expires_in", type="integer", example=3600),
 *             @OA\Property(property="user", type="object")
 *         )
 *     )
 * )
 */
public function refreshToken()
{
    try {
        return $this->createNewToken(auth()->refresh());
    } catch (\Exception $e) {
        return response()->json(['error' => __('messages.operation_failed')], 500);
    }
}


    /**
 * @OA\Get(
 *     path="/api/v1/email/verify/{id}/{hash}",
 *     operationId="verifyEmail",
 *     tags={"Auth"},
 *     summary="Verify email address",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Parameter(
 *         name="hash",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Email verified successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Email verified successfully")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid link or email already verified",
 *         @OA\JsonContent(
 *             @OA\Property(property="errors", type="object")
 *         )
 *     )
 * )
 */
public function verifyEmail(Request $request)
{
    try {
        $userID = $request->route('id');
        $user = User::findOrFail($userID);
    
        $user->email_verified_at = now();
        $user->save();
    
        return response()->json(['message' => __('messages.email_verified_successfully')]);
    } catch (\Exception $e) {
        return response()->json(['error' => __('messages.operation_failed')], 500);
    }
}
    
/**
 * @OA\Post(
 *     path="/api/v1/email/resend",
 *     operationId="resendVerificationEmail",
 *     tags={"Auth"},
 *     summary="Resend the email verification link",
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Verification email resent successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Verification email resent successfully")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="User already verified or invalid user",
 *         @OA\JsonContent(
 *             @OA\Property(property="errors", type="object")
 *         )
 *     )
 * )
 */

 public function resendVerificationEmail(Request $request)
 {
     try {
         $user = $request->user();
         if ($user->hasVerifiedEmail()) {
             return response()->json(['message' => __('messages.email_already_verified')], 400);
         }
         $user->sendEmailVerificationNotification();
         return response()->json(['message' => __('messages.verification_email_resent')]);
     } catch (\Exception $e) {
         return response()->json(['error' => __('messages.operation_failed')], 500);
     }
 }
    


/**
 * @OA\Get(
 *     path="/api/v1/auth/token",
 *     operationId="createNewToken",
 *     tags={"Auth"},
 *     summary="Get new authentication token",
 *     description="Returns a new JWT for an authenticated user.",
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="New token created successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="access_token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."),
 *             @OA\Property(property="token_type", type="string", example="bearer"),
 *             @OA\Property(property="expires_in", type="integer", example=3600),
 *             @OA\Property(property="user", type="object", 
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="name", type="string", example="John Doe"),
 *                 @OA\Property(property="email", type="string", format="email", example="johndoe@example.com")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Unauthorized")
 *         )
 *     )
 * )
 */
protected function createNewToken($token)
{
    return response()->json([
        'access_token' => $token,
        'token_type' => 'bearer',
        'expires_in' => auth()->factory()->getTTL() * 60,
        'user' => auth()->user()
    ]);
}

}
