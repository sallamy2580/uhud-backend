<?php

namespace App\Http\Controllers\Booking;

use App\Models\Booking;
use App\Models\BookingAdult;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller
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
   * add new booking
   *
   * @param \Illuminate\Http\Request $request
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function add(Request $request): JsonResponse
  {
    $this->me($request, false);
    $this->checkAccessControll(['admin', 'agent', 'user'], $this->user, null, null);
    $validator = Validator::make($request->all(), [
      'country_id' => 'required|exists:countries,id|numeric',
      'package_id' => 'required|exists:packages,id|numeric',
      'total_price' => 'required|max:99999999999|numeric',
      'name' => 'required|max:100',
      'email' => 'email|max:255',
      'phone' => 'max:30',
      'region' => 'max:50',
      'num_childs' => 'required|numeric',
      'num_adults' => 'required|numeric',
      'num_nights' => 'numeric',
      'remarks' => 'max:10000',
      'adults' => 'required|json',
      'childs' => 'required|json',
    ]);
    if ($validator->fails()) {
      $errors = $validator->errors();
      unset($validator, $request);
      return response()->json(['error' => $errors->all()], 500);
    }
    unset($validator);
    
    try {
      $adults = json_decode($request->adults);
    } catch (\Exception $ex) {
      return response()->json(['error' => 'invalid adults information!'], 500);
    }
    
    try {
      $childs = json_decode($request->childs);
    } catch (\Exception $ex) {
      return response()->json(['error' => 'invalid childs information!'], 500);
    }
    
    $bookingFields = [
      'user_id' => $this->user->id,
      'country_id' => $request->country_id,
      'city_id' => (isset($request->city_id) && !empty($request->city_id)) ? $request->city_id : '',
      'package_id' => $request->package_id,
      'total_price' => intval($request->total_price),
      'name' => $request->name,
      'email' => (isset($request->email) && !empty($request->email)) ? $request->email : '',
      'phone' => (isset($request->phone) && !empty($request->phone)) ? $request->phone : '',
      'region' => (isset($request->region) && !empty($request->region)) ? $request->region : '',
      'num_childs' => intval($request->num_childs),
      'num_adults' => intval($request->num_adults),
      'num_nights' => (intval($request->num_nights) > 0) ? intval($request->num_nights) : 1,
      'remarks' => (isset($request->remarks) && !empty($request->remarks)) ? $request->remarks : '',
      'status' => 1
    ];
    
    $booking = Booking::create($bookingFields);
    unset($bookingFields);
    
    $numberOfAdultsCreated = 0;
    for ($i = 0; $i <= intval($request->num_adults); $i++) {
      if (!isset($adults[$i]) || empty($adults[$i]))
        break;
      
      if (!isset($adults[$i]->full_name) || empty($adults[$i]->full_name))
        break;
      
      $gender = intval($adults[$i]->gender);
      if (!isset($gender) || empty($gender) || !in_array($gender, [1, 2, 3]))
        break;
      
      if (!isset($adults[$i]->birth_date) || empty($adults[$i]->birth_date))
        break;
      
      if (!isset($adults[$i]->image) || empty($adults[$i]->image))
        break;
      
      $adultsBirthDate = null;
      try {
        $adultsBirthDate = new Carbon($adults[$i]->birth_date);
      } catch (\Exception $ex) {
      }
      
      $adultsFields = [
        'booking_id' => $booking->id,
        'full_name' => $adults[$i]->full_name,
        'gender' => $gender,
        'birth_date' => $adultsBirthDate,
        'passport_img_url' => '',
        'passport_img_b64' => $adults[$i]->image,
        'passenger_type' => 1
      ];
      BookingAdult::create($adultsFields);
      $numberOfAdultsCreated++;
      unset($adultsFields, $adultsBirthDate, $gender);
    }
    unset($adults);
    
    if (intval($booking->num_adults) !== $numberOfAdultsCreated) {
      BookingAdult::where('booking_id', $booking->id)->delete();
      $booking->delete();
      return response()->json(['error' => 'wrong adult info!'], 500);
    }
    
    $numberOfChildsCreated = 0;
    for ($i = 0; $i <= intval($request->num_childs); $i++) {
      if (!isset($childs[$i]) || empty($childs[$i]))
        break;
      
      if (!isset($childs[$i]->full_name) || empty($childs[$i]->full_name))
        break;
      
      $gender = intval($childs[$i]->gender);
      if (!isset($gender) || empty($gender) || !in_array($gender, [1, 2, 3]))
        break;
      
      if (!isset($childs[$i]->birth_date) || empty($childs[$i]->birth_date))
        break;
      
      if (!isset($childs[$i]->image) || empty($childs[$i]->image))
        break;
      
      $childsBirthDate = null;
      try {
        $childsBirthDate = new Carbon($childs[$i]->birth_date);
      } catch (\Exception $ex) {
      }
      
      $childsFields = [
        'booking_id' => $booking->id,
        'full_name' => $childs[$i]->full_name,
        'gender' => $gender,
        'birth_date' => $childsBirthDate,
        'passport_img_url' => '',
        'passport_img_b64' => $childs[$i]->image,
        'passenger_type' => 2
      ];
      BookingAdult::create($childsFields);
      $numberOfChildsCreated++;
      unset($childsFields, $childsBirthDate, $gender);
    }
    unset($childs);
    
    if (intval($booking->num_childs) !== $numberOfChildsCreated) {
      BookingAdult::where('booking_id', $booking->id)->delete();
      $booking->delete();
      return response()->json(['error' => 'wrong childs info!'], 500);
    }
    
    
    return response()->json(['res' => 'new booking created successfully!', 'id' => $booking->id], 200);
    
  }
  
  /**
   * update booking
   *
   * @param \Illuminate\Http\Request $request
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function update(Request $request): JsonResponse
  {
    $this->me($request, false);
    $this->checkAccessControll(['admin', 'agent', 'user'], $this->user, null, null);
    $validator = Validator::make($request->all(), [
      'id' => 'required|exists:bookings,id|numeric',
      'country_id' => 'exists:countries,id|numeric',
      'package_id' => 'exists:packages,id|numeric',
      'total_price' => 'max:99999999999|numeric',
      'name' => 'max:100',
      'email' => 'email|max:255',
      'phone' => 'max:30',
      'region' => 'max:50',
      'num_childs' => 'numeric',
      'num_adults' => 'numeric',
      'num_nights' => 'numeric',
      'remarks' => 'max:10000',
      'adults' => 'json',
      'childs' => 'json',
    ]);
    if ($validator->fails()) {
      $errors = $validator->errors();
      unset($validator, $request);
      return response()->json(['error' => $errors->all()], 500);
    }
    unset($validator);
    
    $booking = Booking::find($request->id);
    $updateFields = [];
    
    if (isset($request->country_id) && !empty($request->country_id))
      $updateFields['country_id'] = $request->country_id;
    
    if (isset($request->city_id) && !empty($request->city_id))
      $updateFields['city_id'] = $request->city_id;
    
    if (isset($request->package_id) && !empty($request->package_id))
      $updateFields['package_id'] = $request->package_id;
  
    if (isset($request->total_price) && !empty($request->total_price)){
      if( intval($request->total_price) != intval($booking->total_price) )
        $updateFields['is_price_applied'] = 1;
  
      $updateFields['total_price'] = $request->total_price;
    }
    
    if (isset($request->name) && !empty($request->name))
      $updateFields['name'] = $request->name;
    
    if (isset($request->email) && !empty($request->email))
      $updateFields['email'] = $request->email;
    
    if (isset($request->phone) && !empty($request->phone))
      $updateFields['phone'] = $request->phone;
    
    if (isset($request->region) && !empty($request->region))
      $updateFields['region'] = $request->region;
    
    if (isset($request->num_childs) && !empty($request->num_childs))
      $updateFields['num_childs'] = $request->num_childs;
    
    if (isset($request->num_adults) && !empty($request->num_adults))
      $updateFields['num_adults'] = $request->num_adults;
    
    if (isset($request->num_nights) && !empty($request->num_nights))
      $updateFields['num_nights'] = $request->num_nights;
    
    if (isset($request->remarks) && !empty($request->remarks))
      $updateFields['remarks'] = $request->remarks;
    
    if (isset($request->status) && !empty($request->status))
      $updateFields['status'] = $request->status;
    
    $booking->update($updateFields);
    unset($updateFields);
    
    
    try {
      $childs = json_decode($request->childs);
    } catch (\Exception $ex) {
      return response()->json(['error' => 'invalid childs information!'], 500);
    }
    
    if (isset($request->adults) && !empty($request->adults)) {
      BookingAdult::where('booking_id', $booking->id)->where('passenger_type', 1)->delete();
      
      $numberOfAdultsCreated = 0;
      for ($i = 0; $i <= intval($request->num_adults); $i++) {
        if (!isset($adults[$i]) || empty($adults[$i]))
          break;
        
        if (!isset($adults[$i]->full_name) || empty($adults[$i]->full_name))
          break;
        
        $gender = intval($adults[$i]->gender);
        if (!isset($gender) || empty($gender) || !in_array($gender, [1, 2, 3]))
          break;
        
        if (!isset($adults[$i]->birth_date) || empty($adults[$i]->birth_date))
          break;
        
        if (!isset($adults[$i]->image) || empty($adults[$i]->image))
          break;
        
        $adultsBirthDate = null;
        try {
          $adultsBirthDate = new Carbon($adults[$i]->birth_date);
        } catch (\Exception $ex) {
        }
        
        $adultsFields = [
          'booking_id' => $booking->id,
          'full_name' => $adults[$i]->full_name,
          'gender' => $gender,
          'birth_date' => $adultsBirthDate,
          'passport_img_url' => '',
          'passport_img_b64' => $adults[$i]->image,
          'passenger_type' => 1
        ];
        BookingAdult::create($adultsFields);
        $numberOfAdultsCreated++;
        unset($adultsFields, $adultsBirthDate, $gender);
      }
      unset($adults);
      
      if (intval($booking->num_adults) !== $numberOfAdultsCreated) {
        BookingAdult::where('booking_id', $booking->id)->delete();
        $booking->delete();
        return response()->json(['error' => 'wrong adult info!'], 500);
      }
    }
    
    if (isset($request->num_childs) && !empty($request->num_childs)) {
      BookingAdult::where('booking_id', $booking->id)->where('passenger_type', 2)->delete();
      
      $numberOfChildsCreated = 0;
      for ($i = 0; $i <= intval($request->num_childs); $i++) {
        if (!isset($childs[$i]) || empty($childs[$i]))
          break;
        
        if (!isset($childs[$i]->full_name) || empty($childs[$i]->full_name))
          break;
        
        $gender = intval($childs[$i]->gender);
        if (!isset($gender) || empty($gender) || !in_array($gender, [1, 2, 3]))
          break;
        
        if (!isset($childs[$i]->birth_date) || empty($childs[$i]->birth_date))
          break;
        
        if (!isset($childs[$i]->image) || empty($childs[$i]->image))
          break;
        
        $childsBirthDate = null;
        try {
          $childsBirthDate = new Carbon($childs[$i]->birth_date);
        } catch (\Exception $ex) {
        }
        
        $childsFields = [
          'booking_id' => $booking->id,
          'full_name' => $childs[$i]->full_name,
          'gender' => $gender,
          'birth_date' => $childsBirthDate,
          'passport_img_url' => '',
          'passport_img_b64' => $childs[$i]->image,
          'passenger_type' => 2
        ];
        BookingAdult::create($childsFields);
        $numberOfChildsCreated++;
        unset($childsFields, $childsBirthDate, $gender);
      }
      unset($childs);
      if (intval($booking->num_childs) !== $numberOfChildsCreated) {
        BookingAdult::where('booking_id', $booking->id)->delete();
        $booking->delete();
        return response()->json(['error' => 'wrong childs info!'], 500);
      }
    }
    
    
    return response()->json(['error' => 'current booking information updated successfully!', 'id' => $booking->id], 500);
    
  }
  
  /**
   * delete airline
   *
   * @param \Illuminate\Http\Request $request
   * @param integer $id
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function delete(Request $request, $id): JsonResponse
  {
  
    $this->me($request,true);
    $booking = Booking::find($id);
    
    if (empty($booking))
      return response()->json(['error' => 'invalid booking!'], 404);
    
    $booking->delete();
    unset($booking);
    return response()->json(['res' => 'selected booking deleted successfully!'], 200);
    
  }
  
  /**
   * all bookings
   *
   * @param \Illuminate\Http\Request $request
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function all(Request $request): JsonResponse
  {
    $this->me();
    $this->checkAccessControll(['admin', 'agent', 'user'], $this->user, null, null);
    $query = null;
    $bookings = null;
    $id = intval($request->query('id'));
    $userId = intval($request->query('user_id'));
    $countryId = intval($request->query('country_id'));
    $cityId = intval($request->query('city_id'));
    $packageId = intval($request->query('package_id'));
    $search = ($request->query('search'));
    /*1 pending - 2 accepted - 3 rejected */
    $status = intval($request->query('status'));
    $price = intval($request->query('price'));
    /*
    * 1 for more than current price
    * 2 for less than current price
    * 3 for equal with current price
    * 4 for more equal
    * 5 for less equal
   */
    $priceOperation = intval($request->price_op);
    
    if (isset($id) && !empty($id))
      $query = Booking::where('id', intval($id));
    
    if (isset($userId) && !empty($userId))
      $query = Booking::where('user_id', intval($userId));
    
    if (isset($countryId) && !empty($countryId))
      $query = Booking::where('country_id', intval($countryId));
    
    if (isset($cityId) && !empty($cityId))
      $query = Booking::where('city_id', intval($cityId));
    
    if (isset($packageId) && !empty($packageId))
      $query = Booking::where('package_id', intval($packageId));
    
    if (isset($status) && !empty($status)) {
      if (intval($status) === 1)
        $query = Booking::where('status', 1);
      else if (intval($status) === 2)
        $query = Booking::where('status', 2);
      else if (intval($status) === 3)
        $query = Booking::where('status', 3);
    }
    
    if (isset($search) && !empty($search)) {
      $query = Booking::where(function ($query) use ($search) {
        return $query->orWhere('name', 'like', '%' . $search . '%')
          ->orWhere('email', 'like', '%' . $search . '%')
          ->orWhere('remarks', 'like', '%' . $search . '%');
      });
    }
    
    if (isset($price) && !empty($price)) {
      if (isset($priceOperation) && !empty($priceOperation)) {
        if ($priceOperation === 1) {
          $query = Booking::where('total_price', '>', $price);
        } else if ($priceOperation === 2) {
          $query = Booking::where('total_price', '<', $price);
        } else if ($priceOperation === 3) {
          $query = Booking::where('total_price', '=', $price);
        } else if ($priceOperation === 4) {
          $query = Booking::where('total_price', '>=', $price);
        } else if ($priceOperation === 5) {
          $query = Booking::where('total_price', '<=', $price);
        } else {
          $query = Booking::where('total_price', '<=', $price);
        }
      } else {
        $query = Booking::where('total_price', '<=', $price);
      }
    }
    
    if (!empty($query))
      $bookings = $query->orderBy('id', 'DESC')->paginate(20);
    else
      $bookings = Booking::orderBy('id', 'DESC')->paginate(20);
    
    return response()->json(['res' => $bookings], 200);
  }
}
