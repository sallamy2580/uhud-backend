<?php

namespace App\Http\Controllers\Package;

use App\Models\Notification;
use App\Models\Package;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class PackageController extends Controller
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
   * Get all Packages
   *
   * @param \Illuminate\Http\Request $request
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function all(Request $request): JsonResponse
  {
    $id = intval($request->query('id'));
    $creator = intval($request->query('creator'));
    $role = $request->query('role');
    /*5 deactive - 1 active - 2 banned - 3 expired - 4 removed*/
    $status = intval($request->query('status'));
    $startDate = $request->query('start_date');
    $endDate = $request->query('end_date');
    $price = intval($request->query('price'));
    $search = ($request->query('search'));
    /*
     * 1 for more than current price
     * 2 for less than current price
     * 3 for equal with current price
     * 4 for more equal
     * 5 for less equal
    */
    $priceOperation = intval($request->query('price_op'));
    $query = null;
    $packages = null;
    
    
    if (isset($role) && !empty($role) || $role === '0') {
      if (trim($role) == 'admin') {
        $role = 1;
      } else if (trim($role) == 'agent') {
        $role = 2;
      } else if (trim($role) == 'user') {
        $role = 5;
      } else if (trim($role) === '1' || trim($role) === '0') {
        $role = 1;
      } else if (trim($role) === '2') {
        $role = 2;
      } else if (trim($role) === '5') {
        $role = 5;
      } else {
        $role = 'supporters';
      }
      if ($role === 'supporters') {
        $query = Package::join('users', 'users.id', '=', 'packages.creator')
          ->where(function ($query) {
            return $query = $query->orWhere('users.role', 0)
              ->orWhere('users.role', 1)
              ->orWhere('users.role', 2);
          });
      } else {
        if (intval($role) === 1) {
          $query = Package::join('users', 'users.id', '=', 'packages.creator')->where(function ($query) {
            return $query = $query->orWhere('users.role', 0)
              ->orWhere('users.role', 1);
          });
        } else {
          $query = Package::join('users', 'users.id', '=', 'packages.creator')->where('users.role', $role);
        }
      }
    }
  
    if (isset($search) && !empty($search)) {
      $query = Package::where(function ($query) use ($search) {
        return $query->orWhere('name', 'like', '%' . $search . '%')
          ->orWhere('price', 'like', '%' . $search . '%');
      });
    }
    
    if (isset($id) && !empty($id))
      $query = Package::where('packages.id', intval($id));
    
    if (isset($creator) && !empty($creator))
      $query = Package::where('packages.creator', intval($creator));
    
    if (isset($status) && !empty($status)) {
      if (intval($status) === 1)
        $query = Package::where('packages.status', 1);
      else if (intval($status) === 2)
        $query = Package::where('packages.status', 2);
      else if (intval($status) === 3)
        $query = Package::where('packages.status', 3);
      else if (intval($status) === 4)
        $query = Package::where('packages.status', 4);
      else if (intval($status) === 5) /*de active package selection*/
        $query = Package::where('packages.status', 0);
    }
    
    if (isset($startDate) && !empty($startDate)) {
      try {
        $startDateCarbone = new Carbon($startDate);
        $query = Package::where('packages.start_date', '>=', $startDateCarbone);
        unset($startDateCarbone);
      } catch (\Exception $ex) {
      }
    }
    
    if (isset($endDate) && !empty($endDate)) {
      try {
        $endDateCarbone = new Carbon($endDate);
        $query = Package::where('packages.end_date', '<=', $endDateCarbone);
        unset($endDateCarbone);
      } catch (\Exception $ex) {
      }
    }
    
    if (isset($price) && !empty($price)) {
      if (isset($priceOperation) && !empty($priceOperation)) {
        if ($priceOperation === 1) {
          $query = Package::where('packages.price', '>', $price);
        } else if ($priceOperation === 2) {
          $query = Package::where('packages.price', '<', $price);
        } else if ($priceOperation === 3) {
          $query = Package::where('packages.price', '=', $price);
        } else if ($priceOperation === 4) {
          $query = Package::where('packages.price', '>=', $price);
        } else if ($priceOperation === 5) {
          $query = Package::where('packages.price', '<=', $price);
        } else {
          $query = Package::where('packages.price', '<=', $price);
        }
      } else {
        $query = Package::where('packages.price', '<=', $price);
      }
    }
    
    if (!empty($query))
      $packages = $query->orderBy('packages.id', 'DESC')->paginate(20);
    else
      $packages = Package::orderBy('packages.id', 'DESC')->paginate(20);
    
    unset($query, $status, $request, $priceOperation, $price, $startDate, $endDate, $creator);
    return response()->json(['res' => $packages], 200);
  }
  
  /**
   * add new package
   *
   * @param \Illuminate\Http\Request $request
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function add(Request $request): JsonResponse
  {
    
    $validator = Validator::make($request->all(), [
      'airline_id' => 'required|exists:flights,id',
      'hotel_id' => 'required|exists:hotels,id',
      'name' => 'required|max:100',
      'price' => 'required|numeric|max:99999999999',
      'status' => 'numeric|between:0,4',
      'rate' => 'numeric|between:0,6',
      'code' => 'max:255',
      'start_date' => 'date_format:Y-m-d',
      'end_date' => 'date_format:Y-m-d',
    ]);
    if ($validator->fails()) {
      $errors = $validator->errors();
      unset($validator, $request);
      return response()->json(['error' => $errors->all()], 500);
    }
    unset($validator);
    $this->me($request, false);
    $this->checkAccessControll(['admin', 'agent', 'user'], $this->user, null, null);
    $packageFields = $request->all();
    $packageFields['creator'] = $this->user->id;
    $packageCreated = Package::create($packageFields);
    unset($packageFields);
    return response()->json(['res' => "new package created successfully", 'id' => $packageCreated->id], 201);
    
  }
  
  /**
   * updtae package
   *
   * @param \Illuminate\Http\Request $request
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function update(Request $request): JsonResponse
  {
    
    $validator = Validator::make($request->all(), [
      'id' => 'required|exists:packages,id',
      'airline_id' => 'exists:flights,id',
      'hotel_id' => 'exists:hotels,id',
      'name' => 'max:100',
      'price' => 'numeric|max:99999999999',
      'status' => 'numeric|between:0,4',
      'rate' => 'numeric|between:0,6',
      'code' => 'max:255',
      'start_date' => 'date_format:Y-m-d',
      'end_date' => 'date_format:Y-m-d'
    ]);
    if ($validator->fails()) {
      $errors = $validator->errors();
      unset($validator, $request);
      return response()->json(['error' => $errors->all()], 500);
    }
    unset($validator);
    $this->me($request, false);
    $this->checkAccessControll(['admin', 'agent', 'user'], $this->user, null, null);
    $package = Package::find($request->id);
    $updateFields = [];
    
    if (isset($request->airline_id) && !empty($request->airline_id))
      $updateFields['airline_id'] = $request->airline_id;
    
    if (isset($request->hotel_id) && !empty($request->hotel_id))
      $updateFields['hotel_id'] = $request->hotel_id;
    
    if (isset($request->name) && !empty($request->name))
      $updateFields['name'] = $request->name;
    
    if (isset($request->price) && !empty($request->price))
      $updateFields['price'] = $request->price;
    
    if (isset($request->status) && !empty($request->status))
      $updateFields['status'] = $request->status;
    
    if (isset($request->rate) && !empty($request->rate))
      $updateFields['rate'] = $request->rate;
    
    if (isset($request->code) && !empty($request->code))
      $updateFields['code'] = $request->code;
    
    if (isset($request->start_date) && !empty($request->start_date))
      $updateFields['start_date'] = $request->start_date;
    
    if (isset($request->end_date) && !empty($request->end_date))
      $updateFields['end_date'] = $request->end_date;
    
    $package->update($updateFields);
    unset($updateFields);
    return response()->json(['res' => "new package created successfully", 'id' => $package->id], 201);
    
  }
  
  /**
   * delete package
   *
   * @param \Illuminate\Http\Request $request
   * @param integer $id
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function delete(Request $request, $id): JsonResponse
  {
    $this->me($request,true);
    $package = Package::find($id);
    
    if (empty($package))
      return response()->json(['error' => 'invalid package!'], 404);
    
    $package->delete();
    unset($package);
    return response()->json(['res' => 'selected package deleted successfully!'], 200);
    
  }
}
