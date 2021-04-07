<?php

namespace App\Http\Controllers\Auth;

use App\Mail\EmailVerificationMail;
use App\Mail\ForgetPasswordMail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use phpDocumentor\Reflection\Types\Object_;
use Validator;
use Socialite;


class AuthController extends Controller
{
  
  /**
   * Create a new AuthController instance.
   *
   * @return void
   */
  public function __construct()
  {
    $this->middleware('auth:api', ['except' =>
      ['login', 'register', 'socialiteRedirect', 'socialiteCallback', 'sendForgetPassEmail', 'changePasswordFromForgetPass', 'verificationEmail', 'sendEmailVerificationLink']
    ]);
  }
  
  /**
   * Create a new AuthController instance.
   *
   * @param \Illuminate\Http\Request $request
   *
   * @return JsonResponse
   */
  public function changePasswordFromForgetPass(Request $request, $encryptUSerId): JsonResponse
  {
    $userId = null;
    try {
      $userId = decrypt($encryptUSerId);
    } catch (\Exception $ex) {
      return response()->json(['error' => 'invalid request info!'], 500);
    }
    
    $user = User::find($userId);
    
    if (empty($user))
      return response()->json(['error' => 'user with this info not found'], 404);
    
    $forgetPassExpireAt = new Carbon($user->forget_pass_expire_at);
    $now = new Carbon();
    
    if ($now->gt($forgetPassExpireAt))
      return response()->json(['error' => 'Your password reset link has expired'], 500);
    
    $password = $request->password;
    $passwordConfirmation = $request->password_confirmation;
    if ($password != $passwordConfirmation)
      return response()->json(['error' => 'password confirmation is wrong!'], 404);
    
    $user->update([
      'password' => $password,
      'forget_pass_expire_at' => null
    ]);
    unset($user, $password, $passwordConfirmation, $userId, $encryptUSerId, $request, $forgetPassExpireAt, $now);
    return response()->json(['res' => 'user password updated successfully!'], 200);
  }
  
  
  /**
   * Create a new AuthController instance.
   *
   * @param \Illuminate\Http\Request $request
   *
   * @return JsonResponse
   */
  public function sendForgetPassEmail(Request $request): JsonResponse
  {
    if (!isset($request->email) || empty($request->email))
      return response()->json(['error' => 'invalid request!'], 404);
    
    $user = User::where('email', $request->email)->first();
    
    if (empty($user))
      return response()->json(['error' => 'user not found for current request infoes!'], 404);
    
    
    try {
      $now = new Carbon();
      $user->update([
        'forget_pass_expire_at' => $now->addMinutes(7)
      ]);
//            Mail::to($user->email)->send(new ForgetPasswordMail($user));
      try {
        Mail::send('emails.forget-password', ['user' => $user], function ($m) use ($user) {
          $m->from('alzuhud.info@gmail.com', 'Alzuhud');
          $m->to($user->email, $user->full_name)->subject('Alzuhud - Reset Your password');
        });
      } catch (\Exception $ex) {
      }
      return response()->json(['res' => 'forget password email sended successfully. check you email address please.'], 200);
    } catch (\Exception $ex) {
      return response()->json(['error' => 'unable to send forget password link to this email address'], 404);
    }
    
  }
  
  /**
   * send email verification link
   *
   * @param \Illuminate\Http\Request $request
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function sendEmailVerificationLink(Request $request): JsonResponse
  {
    if (!isset($request->email) || empty($request->email))
      return response()->json(['error' => 'invalid request!'], 404);
    
    $user = User::where('email', $request->email)->first();
    
    if (empty($user))
      return response()->json(['error' => 'user not found for current request infoes!'], 404);
    
    if ($user->is_email_verified) {
      return response()->json(['msg' => 'this account verified email before!', 'is_verified_before' => 1], 200);
    }
    
    try {
      $now = new Carbon();
      $user->update([
        'email_verification_expired_at' => $now->addMinutes(7)
      ]);
//            Mail::to('farhad.work92@gmail.com')->send(new EmailVerificationMail($this->user));
//            Mail::to($user->email)->send(new EmailVerificationMail($user));
      try {
        Mail::send('emails.verification-email', ['user' => $user], function ($m) use ($user) {
          $m->from('alzuhud.info@gmail.com', 'Alzuhud');
          $m->to($user->email, $user->full_name)->subject('Alzuhud - Active Your account');
        });
      } catch (\Exception $ex) {
      }
      return response()->json(['res' => 'verification link sended successfully. check you email address please.', 'is_verified_before' => 0], 200);
    } catch (\Exception $ex) {
      return response()->json(['error' => 'unable to send email verification link to this email address'], 404);
    }
    
  }
  
  /**
   * send email verification link
   *
   * @param \Illuminate\Http\Request $request
   * @param string $emailVerifyToken
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function verificationEmail($emailVerifyToken): JsonResponse
  {
    $userId = null;
    try {
      $userId = decrypt($emailVerifyToken);
    } catch (\Exception $ex) {
      return response()->json(['error' => 'invalid request info!'], 500);
    }
    
    $user = User::find($userId);
    
    if (empty($user))
      return response()->json(['error' => 'user with this info not found'], 404);
    
    if (intval($user->is_email_verified) === 1)
      return response()->json(['error' => 'this account email address verified before!'], 200);
    
    $verificationEmailExpireAt = new Carbon($user->email_verification_expired_at);
    $now = new Carbon();
    
    if ($now->gt($verificationEmailExpireAt))
      return response()->json(['error' => 'Your password reset link has expired'], 500);
    
    $user->update([
      'email_verification_expired_at' => null,
      'is_email_verified' => 1,
      'email_verified_at' => new Carbon()
    ]);
    unset($user, $verificationEmailExpireAt, $userId, $emailVerifyToken);
    return response()->json(['res' => 'email verified successfully!'], 200);
  }
  
  /**
   * Create a new AuthController instance.
   *
   * @param Illuminate\Http\Request $request
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function register(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'full_name' => 'required|max:50',
      'email' => 'required|email|unique:users,email|max:80|min:5',
      'phone' => 'required|unique:users,phone|max:20',
      'address' => 'max:255',
      'country_id' => 'exists:countries,id',
      'state_id' => 'exists:states,id',
      'city_id' => 'exists:cities,id',
      'password' => 'required|confirmed|between:8,30',
      'status' => 'in:1,2,3,4,5',
      'postal_code' => 'max:100'
    ]);
    if ($validator->fails()) {
      $errors = $validator->errors();
      unset($validator, $request);
      return response()->json(['error' => $errors->all()], 500);
    }
    
    $userCreateFields = $request->all();
//        $roles = [0, 1, 2, 3, 4, 5];
//        if (!in_array(intval($request->role), $roles)) {
//            /*default for user(pool user)*/
//            $userCreateFields['role'] = 5;
//        } else {
//            $userCreateFields['role'] = $request->role;
//        }
    $userCreateFields['role'] = 5;
    $now = new Carbon();
    $userCreateFields['email_verification_expired_at'] = $now->addMinutes(7);
    
    $user = User::create($userCreateFields);
    
    if (isset($request->needLogin) && !empty($request->needLogin)) {
      $token = auth()->login($user);
      unset($user, $request, $userCreateFields, $roles);
      return $this->respondWithToken($token);
    }

//            Mail::to('farhad.work92@gmail.com')->send(new EmailVerificationMail($user));
//        Mail::to($user->email)->send(new EmailVerificationMail($user));
    try {
      Mail::send('emails.verification-email', ['user' => $user], function ($m) use ($user) {
        $m->from('alzuhud.info@gmail.com', 'Alzuhud');
        $m->to($user->email, $user->full_name)->subject('Alzuhud - Active Your account');
      });
    } catch (\Exception $ex) {
    }
    
    $msg = 'new user created successfully. check your email address for verify email!';
//        if( intval($user->role) === 1 || intval($user->role) === 0 )
//            $msg = 'new admin created successfully.';
//        else if( intval($user->role) === 2 )
//            $msg = 'new agent created successfully.';
    
    unset($user, $request, $userCreateFields, $roles);
    return response()->json(['msg' => $msg], 201);
    
    
  }
  
  /**
   * Get a JWT via given credentials.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function login()
  {
    $credentials = request(['email', 'password']);
    if (!$token = auth()->attempt($credentials)) {
      return response()->json(['error' => 'Unauthorized'], 401);
    }
    $user = auth()->user();
    if (($user->role == '5' || $user->role == 'user') && intval($user->is_email_verified) === 0) {
      auth()->logout();
      return response()->json(['error' => 'please verify your email! for use this system you must verified your email address!'], 401);
    }
    if ($user->status == 'removed' || $user->status == 'banned') {
      auth()->logout();
      return response()->json(['error' => 'your account ' . $user->status . ' from system if you think there is something going be wrong contact with supporters team.'], 401);
    }
    return $this->respondWithToken($token);
  }
  
  /**
   * Get the authenticated User.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function me()
  {
    return response()->json(auth()->user());
  }
  
  /**
   * Log the user out (Invalidate the token).
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function logout()
  {
    auth()->logout();
    
    return response()->json(['message' => 'Successfully logged out']);
  }
  
  /**
   * Refresh a token.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function refresh()
  {
    return $this->respondWithToken(auth()->refresh());
  }
  
  /**
   * Get the token array structure.
   *
   * @param string $token
   *
   * @return \Illuminate\Http\JsonResponse
   */
  protected function respondWithToken($token)
  {
    return response()->json([
      'access_token' => $token,
      'token_type' => 'bearer',
      'expires_in' => auth()->factory()->getTTL() * 60,
      'user' => auth()->user()
    ]);
  }
  
  /**
   * socialtie redirect to provider
   *
   * @param string $provider
   *
   * @return mixed
   */
  public function socialiteRedirect($provider)
  {
    return \Laravel\Socialite\Facades\Socialite::driver($provider)->redirect();
  }
  
  /**
   * socialtie callback from provider and user loging soloution
   *
   * @param string $provider
   * @param string $fromClient
   *
   * @return object
   */
  public function socialiteCallback($provider)
  {
//    if (isset($fromClient) && !empty($fromClient) && $fromClient == 'yes') {
//      return \Laravel\Socialite\Facades\Socialite::driver($provider)->redirect();
//    }
    
    $getInfo = \Laravel\Socialite\Facades\Socialite::driver($provider)->user();
    
    return response()->json($getInfo);
    
    $user = $this->createUserFromSocialite($getInfo);
    
    $token = auth()->login($user);
    
    return $this->respondWithToken($token);
  }
  
  /**
   * create user from callback information from user
   *
   * @param object $getInfo
   *
   * @return \App\Models\User
   */
  public function createUserFromSocialite($getInfo)
  {
    
    $user = User::where('email', $getInfo->email)->first();
    
    if (!empty($user)) return $user;
    
    $user = User::create([
      'full_name' => $getInfo->name,
      'email' => $getInfo->email,
      'provider_id' => $getInfo->id,
      'phone' => '',
      'role' => 5,
      'status' => 1,
      'password' => rand(111111111, 999999999)
    ]);
    
    return $user;
  }
}
