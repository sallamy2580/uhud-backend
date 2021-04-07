<?php

namespace App\Http\Controllers\User;

use App\Mail\EmailVerificationMail;
use App\Mail\ForgetPasswordMail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Validator;

class UserController extends Controller
{
  
  
  /**
   * user logged
   *
   * @var User $user
   */
  protected $user;
  
  /**
   * Get the authenticated User.
   *
   * @param \Illuminate\Http\Request $request
   * @param boolean $isBlockAction
   *
   * @return void
   */
  public function me(Request $request = null, $isBlockAction = false)
  {
    $this->user = auth()->user();
    if (empty($this->user))
      abort(404, 'User not found!');
    
    $userInSystem = User::where('is_email_verified', 1)
      ->where('id', $this->user->id)
      ->whereIn('status', [1, 2, 'active', 'inactive'])
      ->first();
    
    if (empty($userInSystem))
      abort(404, 'user not found');
    
    
    if ((isset($request) && !empty($request) && $userInSystem->status == 'inactive' && $request->isMethod('post')) ||
      ($isBlockAction === true && $userInSystem->status == 'inactive'))
      abort(500, 'your status changed to inactive so you cant doing anything in system, just you can see your account statistics.');
    
    unset($userInSystem);
  }
  
  /**
   * Get all users with role filter (role filter get with query string format)
   *
   * @param \Illuminate\Http\Request $request
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function all(Request $request): JsonResponse
  {
    $this->me();
    $this->checkAccessControll(['admin', 'agent'], $this->user, null, null);
    $id = $request->query('id');
    /*0 or 1 for super admin, 2 for agents - 5 for users*/
    $role = $request->query('role');
    /*1 active - 2 inactive - 3 removed - 4 banned*/
    $status = $request->query('status');
    $search = $request->query('search');
    $userQuery = null;
    $users = null;
    
    if (isset($id) && !empty($id)) {
      $userQuery = User::where('id', $id);
    }
    
    if (isset($role) && !empty($role)) {
      if (intval($role) === 5)
        $userQuery = User::where('role', 5);
      else if (intval($role) === 2)
        $userQuery = User::where('role', 2);
      else if (intval($role) === 1)
        $userQuery = User::where('role', 0)->orWhere('role', 1);
    }
    if (isset($status) && !empty($status)) {
      if (intval($status) === 1)
        $userQuery = User::where('status', 1);
      else if (intval($status) === 2)
        $userQuery = User::where('status', 2);
      else if (intval($status) === 3)
        $userQuery = User::where('status', 3);
      else if (intval($status) === 4)
        $userQuery = User::where('status', 4);
    }
    
    if (isset($search) && !empty($search)) {
      $userQuery = User::where(function ($query) use ($search) {
        return $query->orWhere('full_name', 'like', '%' . $search . '%')
          ->orWhere('email', 'like', '%' . $search . '%')
          ->orWhere('phone', 'like', '%' . $search . '%');
      });
    }
    
    if (!empty($userQuery))
      $users = $userQuery->orderBy('id', 'DESC')->paginate(20);
    else
      $users = User::orderBy('id', 'DESC')->paginate(20);
    
    unset($role, $userQuery);
    return response()->json(['res' => $users], 200);
  }
  
  /**
   * add new user
   *
   * @param \Illuminate\Http\Request $request
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function add(Request $request): JsonResponse
  {
    $this->me();
    $this->checkAccessControll(['admin', 'agent'], $this->user, null, null);
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
    
    $roles = [0, 1, 2, 3, 4, 5];
    $userCreateFields = $request->all();
    if (!in_array(intval($request->role), $roles)) {
      /*if not set select pool user*/
      $userCreateFields['role'] = 5;
    }
    
    $user = User::create($userCreateFields);
    
    $msg = 'new user created successfully';
    if ($user->role == 'admin')
      $msg = 'new admin created successfully';
    else if ($user->role == 'agent')
      $msg = 'new agent created successfully';
    
    unset($user, $request, $userCreateFields, $roles);
    return response()->json(['msg' => $msg], 201);
  }
  
  /**
   * update user
   *
   * @param \Illuminate\Http\Request $request
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function update(Request $request): JsonResponse
  {
    if (!isset($request->id) || empty($request->id))
      return response()->json(['error' => 'user information not found'], 404);
  
    $this->me($request, false);
    $this->checkAccessControll(['admin', 'agent', 'user'], $this->user, 'user_id_equality', $request->id);
    
    $userForUpdate = User::find($request->id);
    
    if (empty($userForUpdate))
      return response()->json(['error' => 'inavlid user info'], 404);
    
    $validationArray = [
      'full_name' => 'max:50',
      'email' => 'email|max:80|min:5',
      'phone' => 'max:20',
      'address' => 'max:255',
      'status' => 'in:1,2,3,4,5',
      'country_id' => 'exists:countries,id',
      'state_id' => 'exists:states,id',
      'city_id' => 'exists:cities,id',
      'postal_code' => 'max:100'
    ];
    
    if (isset($request->email) && !empty($request->email) && trim($request->email) != trim($userForUpdate->email)) {
      $validationArray['email'] = 'required|email|unique:users,email|max:80|min:5';
    }
    if (isset($request->phone) && !empty($request->phone) && trim($request->phone) != trim($userForUpdate->phone)) {
      $validationArray['phone'] = 'required|unique:users,phone|max:20';
    }
    
    $validator = Validator::make($request->all(), $validationArray);
    if ($validator->fails()) {
      $errors = $validator->errors();
      unset($validator, $request);
      return response()->json(['error' => $errors->all()], 500);
    }
    
    if (isset($request->full_name) && !empty($request->full_name))
      $userForUpdate->full_name = $request->full_name;
    
    if (isset($request->email) && !empty($request->email))
      $userForUpdate->email = $request->email;
    
    if (isset($request->phone) && !empty($request->phone))
      $userForUpdate->phone = $request->phone;
    
    if (isset($request->address) && !empty($request->address))
      $userForUpdate->address = $request->address;
    
    if (isset($request->country_id) && !empty($request->country_id))
      $userForUpdate->country_id = $request->country_id;
    
    if (isset($request->city_id) && !empty($request->city_id))
      $userForUpdate->city_id = $request->city_id;
    
    if (isset($request->state_id) && !empty($request->state_id))
      $userForUpdate->state_id = $request->state_id;
    
    if (isset($request->status) && !empty($request->status))
      $userForUpdate->status = $request->status;
    
    if (isset($request->postal_code) && !empty($request->postal_code))
      $userForUpdate->postal_code = $request->postal_code;
    
    if (isset($request->is_email_verified) && !empty($request->is_email_verified)) {
      if ($this->user->role == 'agent' || $this->user->role == 'admin') {
        $userForUpdate->is_email_verified = ($request->is_email_verified == '1' || $request->is_email_verified === 1) ? 1 : 0;
      }
    }
    
    $roles = [0, 1, 2, 3, 4, 5];
    if (isset($request->role) && !empty($request->role) && in_array(intval($request->role), $roles)) {
      $userForUpdate->role = $request->role;
    }
    
    $userForUpdate->save();
    
    $msg = 'user info updated successfully.';
    if ($userForUpdate->role == 'admin')
      $msg = 'admin info updated successfully';
    else if ($userForUpdate->role == 'agent')
      $msg = 'agent info updated successfully';
    
    unset($userForUpdate, $request, $userCreateFields, $roles);
    return response()->json(['msg' => $msg], 201);
  }
  
  /**
   * change password
   *
   * @param \Illuminate\Http\Request $request
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function changePassword(Request $request): JsonResponse
  {
    
    $validator = Validator::make($request->all(), [
      'id' => 'required|exists:users,id',
      'old_pass' => 'required',
      'password' => 'required|confirmed|between:8,30',
    ]);
  
    $this->me($request, false);
    $this->checkAccessControll(['admin', 'agent', 'user'], $this->user, 'user_id_equality', $request->id);
    
    if ($validator->fails()) {
      $errors = $validator->errors();
      unset($validator, $request);
      return response()->json(['error' => $errors->all()], 500);
    }
    $user = User::find($request->id);
    
    if (!Hash::check($request->old_pass, $user->password))
      return response()->json(['msg' => 'old password is wrong!'], 500);
    
    $user->password = $request->password;
    $user->save();
    unset($user, $validator, $request);
    return response()->json(['msg' => 'password updated successfully...'], 201);
  }
  
  
  /**
   * change password
   *
   * @param \Illuminate\Http\Request $request
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function changeLogedUserPassword(Request $request): JsonResponse
  {
    $validator = Validator::make($request->all(), [
      'password' => 'required|confirmed|between:8,30',
    ]);
  
    $this->me($request, false);
    
    if ($validator->fails()) {
      $errors = $validator->errors();
      unset($validator, $request);
      return response()->json(['error' => $errors->all()], 500);
    }
    $this->user->password = $request->password;
    $this->user->save();
    unset($validator, $request);
    return response()->json(['msg' => 'password updated successfully...'], 201);
  }
  
  /**
   * delete user
   *
   * @param integer $id
   * @param \Illuminate\Http\Request $request
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function delete(Request $request, $id): JsonResponse
  {
    $this->me($request, true);
    $this->checkAccessControll(['admin', 'agent'], $this->user, null, null);
    
    $user = User::find($id);
    if (empty($user))
      return response()->json(['error' => 'inavlid user info'], 404);
    
    try {
      $user->delete();
      return response()->json(['msg' => 'user deleted successfully...'], 200);
    } catch (\Exception $ex) {
      return response()->json(['msg' => 'cant delete user contact with supporters, pleas.'], 500);
    }
  }
}
