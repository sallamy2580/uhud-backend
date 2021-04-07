<?php

namespace App\Http\Controllers\Flight;

use App\Models\Country;
use App\Models\Flight;
use App\Models\FlightSeat;
use App\Models\FlightSeatReserved;
use App\Models\Hotel;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Nexmo\Response;

class FlightController extends Controller
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
   * Get all Flights
   *
   * @param \Illuminate\Http\Request $request
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function all(Request $request): JsonResponse
  {
    $this->me();
    $this->checkAccessControll(['admin', 'agent', 'user'], $this->user, null, null);
    
    $id = intval($request->query('id'));
    $creator = intval($request->query('creator'));
    $rateAvg = intval($request->query('rate'));
    $originId = intval($request->query('origin'));
    $originName = ($request->query('origin_name'));
    $destinationId = intval($request->query('destination'));
    $destinationName = ($request->query('destination_name'));
    $search = $request->query('search');
    $price = intval($request->query('price'));
    /*
     * 1 for more than current price
     * 2 for less than current price
     * 3 for equal with current price
     * 4 for more equal
     * 5 for less equal
    */
    $priceOperation = intval($request->query('price_op'));
    $query = null;
    $flights = null;
    if (isset($id) && !empty($id))
      $query = Flight::where('id', ($id));
    
    if (isset($creator) && !empty($creator))
      $query = Flight::where('creator', ($creator));
    
    if (isset($rateAvg) && !empty($rateAvg))
      $query = Flight::where('rate_avg', ($rateAvg));
    
    if (isset($originId) && !empty($originId))
      $query = Flight::where('origin_id', ($originId));
    
    if (isset($destinationId) && !empty($destinationId))
      $query = Flight::where('destination_id', ($destinationId));
    
    if (isset($originName) && !empty($originName))
      $query = Flight::where('origin_name', 'like', '%' . ($originName) . '%');
    
    if (isset($destinationName) && !empty($destinationName))
      $query = Flight::where('destination_name', 'like', '%' . ($destinationName) . '%');
    
    if (isset($search) && !empty($search)) {
      $query = Flight::where(function ($query) use ($search) {
        return $query->orWhere('name', 'like', '%' . $search . '%')
          ->orWhere('origin_name', 'like', '%' . $search . '%')
          ->orWhere('destination_name', 'like', '%' . $search . '%');
      });
    }
    
    if (isset($price) && !empty($price)) {
      if (isset($priceOperation) && !empty($priceOperation)) {
        if ($priceOperation === 1) {
          $query = Flight::where('price', '>', $price);
        } else if ($priceOperation === 2) {
          $query = Flight::where('price', '<', $price);
        } else if ($priceOperation === 3) {
          $query = Flight::where('price', '=', $price);
        } else if ($priceOperation === 4) {
          $query = Flight::where('price', '>=', $price);
        } else if ($priceOperation === 5) {
          $query = Flight::where('price', '<=', $price);
        } else {
          $query = Flight::where('price', '<=', $price);
        }
      } else {
        $query = Flight::where('price', '<=', $price);
      }
    }
    
    if (!empty($query))
      $flights = $query->orderBy('id', 'DESC')->paginate(20);
    else
      $flights = Flight::orderBy('id', 'DESC')->paginate(20);
    
    unset($query, $destinationName, $originName, $originId, $destinationId, $search, $request, $priceOperation, $price, $creator);
    return response()->json(['res' => $flights], 200);
  }
  
  /**
   * Add new flight
   *
   * @param \Illuminate\Http\Request $request
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function add(Request $request): JsonResponse
  {
    $this->me($request, false);
    $this->checkAccessControll(['admin', 'agent'], $this->user, null, null);
    $validator = Validator::make($request->all(), [
      'airline_id' => 'required|exists:airlines,id',
      'origin_id' => 'required|exists:countries,id',
      'takeoff_date' => 'required|date_format:Y-m-d',
      'return_date' => 'required|date_format:Y-m-d',
      'seat_availability' => 'required|numeric',
      'price' => 'required|numeric|max:9999999999'
    ]);
    if ($validator->fails()) {
      $errors = $validator->errors();
      unset($validator, $request);
      return response()->json(['error' => $errors->all()], 500);
    }
    $flightFields = $request->all();
    $flightFields['creator'] = $this->user->id;
    $flightFields['origin_name'] = Country::find($request->origin_id)->name;
    Flight::create($flightFields);
    unset($validator, $flightFields);
    return response()->json(['res' => 'flight information added successfully.'], 200);
  }
  
  /**
   * update flight
   *
   * @param \Illuminate\Http\Request $request
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function update(Request $request): JsonResponse
  {
    $this->me($request, false);
    $this->checkAccessControll(['admin', 'agent'], $this->user, null, null);
    $validator = Validator::make($request->all(), [
      'id' => 'required|exists:flights,id',
      'airline_id' => 'exists:airlines,id',
      'origin_id' => 'exists:countries,id',
      'takeoff_date' => 'date_format:Y-m-d',
      'return_date' => 'date_format:Y-m-d',
      'seat_availability' => 'numeric',
      'price' => 'numeric|max:9999999999'
    ]);
    if ($validator->fails()) {
      $errors = $validator->errors();
      unset($validator, $request);
      return response()->json(['error' => $errors->all()], 500);
    }
    
    $flight = Flight::find($request->id);
    $updateFields = [];
    
    if (isset($request->airline_id) && !empty($request->airline_id))
      $updateFields['airline_id'] = $request->airline_id;
    
    if (isset($request->origin_id) && !empty($request->origin_id)) {
      $updateFields['origin_id'] = $request->origin_id;
      $updateFields['origin_name'] = Country::find($request->origin_id)->name;
    }
    
    if (isset($request->takeoff_date) && !empty($request->takeoff_date))
      $updateFields['takeoff_date'] = $request->takeoff_date;
    
    if (isset($request->return_date) && !empty($request->return_date))
      $updateFields['return_date'] = $request->return_date;
    
    if (isset($request->seat_availability) && !empty($request->seat_availability))
      $updateFields['seat_availability'] = $request->seat_availability;
    
    if (isset($request->price) && !empty($request->price)){
      if( intval($request->price) != intval($flight->price) )
        $updateFields['is_price_applied'] = 1;
      
      $updateFields['price'] = $request->price;
    }
    
    $flight->update($updateFields);
    
    unset($validator, $updateFields);
    return response()->json(['res' => 'flight information updated successfully.'], 200);
  }
  
  /**
   * delete flight
   *
   * @param \Illuminate\Http\Request $request
   * @param integer $id
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function delete(Request $request, $id): JsonResponse
  {
  
    $this->me($request, true);
    $flight = Flight::find($id);
    
    if (empty($flight))
      return response()->json(['error' => 'invalid flight!'], 404);
    
    $flight->delete();
    unset($flight);
    return response()->json(['res' => 'selected flight deleted successfully!'], 200);
    
  }
  
  /**
   * Get all Flight Seats
   *
   * @param integer $flightId
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function seats($flightId): JsonResponse
  {
    $this->me();
    $this->checkAccessControll(['admin', 'agent', 'user'], $this->user, null, null);
    $seats = FlightSeat::where('flight', $flightId)->get();
    return response()->json(['res' => $seats], 200);
  }
  
  /**
   * Get all Flight Seats
   *
   * @param integer $flightId
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function reservs($flightId): JsonResponse
  {
    $this->me();
    $this->checkAccessControll(['admin', 'agent', 'user'], $this->user, null, null);
    $seats = FlightSeatReserved::where('flight_id', $flightId)->get();
    return response()->json(['res' => $seats], 200);
  }
  
  /**
   * Get all Seats
   *
   * @param \Illuminate\Http\Request $request
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function allSeats(Request $request): JsonResponse
  {
    $this->me();
    $this->checkAccessControll(['admin', 'agent', 'user'], $this->user, null, null);
    $query = null;
    $flight = intval($request->query('flight'));
    $seatNumber = intval($request->query('seat_number'));
    $seatPosition = intval($request->query('seat_position'));
    $seatRow = intval($request->query('seat_row'));
    $seatCode = intval($request->query('seat_code'));
    
    if (isset($flight) && !empty($flight))
      $query = FlightSeat::where('flight', ($flight));
    
    if (isset($seatNumber) && !empty($seatNumber))
      $query = FlightSeat::where('seat_number', ($seatNumber));
    
    if (isset($seatPosition) && !empty($seatPosition))
      $query = FlightSeat::where('seat_position', ($seatPosition));
    
    if (isset($seatRow) && !empty($seatRow))
      $query = FlightSeat::where('seat_row', ($seatRow));
    
    if (isset($seatCode) && !empty($seatCode))
      $query = FlightSeat::where('seat_code', ($seatCode));
    
    $seats = null;
    if (!empty($query))
      $seats = $query->orderBy('id', 'DESC')->paginate(20);
    else
      $seats = FlightSeat::orderBy('id', 'DESC')->paginate(20);
    
    unset($query, $seatPosition, $seatCode, $seatRow, $seatNumber, $flight);
    return response()->json(['res' => $seats], 200);
  }
  
  /**
   * Get all Reservs
   *
   * @param \Illuminate\Http\Request $request
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function allReservs(Request $request): JsonResponse
  {
    $this->me();
    $this->checkAccessControll(['admin', 'agent', 'user'], $this->user, null, null);
    $query = null;
    $flight = intval($request->query('flight_id'));
    $seatId = intval($request->query('seat_id'));
    $userId = intval($request->query('user_id'));
    $transactionId = intval($request->query('transaction_id'));
    $price = intval($request->query('price'));
    /*
     * 1 for more than current price
     * 2 for less than current price
     * 3 for equal with current price
     * 4 for more equal
     * 5 for less equal
    */
    $priceOperation = intval($request->query('price_op'));
    
    if (isset($flight) && !empty($flight))
      $query = FlightSeatReserved::where('flight_id', ($flight));
    
    if (isset($seatId) && !empty($seatId))
      $query = FlightSeatReserved::where('seat_id', ($seatId));
    
    if (isset($userId) && !empty($userId))
      $query = FlightSeatReserved::where('user_id', ($userId));
    
    if (isset($transactionId) && !empty($transactionId))
      $query = FlightSeatReserved::where('transaction_id', ($transactionId));
    
    if (isset($seatCode) && !empty($seatCode))
      $query = FlightSeatReserved::where('seat_code', ($seatCode));
    
    if (isset($price) && !empty($price)) {
      if (isset($priceOperation) && !empty($priceOperation)) {
        if ($priceOperation === 1) {
          $query = FlightSeatReserved::where('final_price', '>', $price);
        } else if ($priceOperation === 2) {
          $query = FlightSeatReserved::where('final_price', '<', $price);
        } else if ($priceOperation === 3) {
          $query = FlightSeatReserved::where('final_price', '=', $price);
        } else if ($priceOperation === 4) {
          $query = FlightSeatReserved::where('final_price', '>=', $price);
        } else if ($priceOperation === 5) {
          $query = FlightSeatReserved::where('final_price', '<=', $price);
        } else {
          $query = FlightSeatReserved::where('final_price', '<=', $price);
        }
      } else {
        $query = FlightSeatReserved::where('final_price', '<=', $price);
      }
    }
    
    $seats = null;
    if (!empty($query))
      $seats = $query->orderBy('id', 'DESC')->paginate(20);
    else
      $seats = FlightSeatReserved::orderBy('id', 'DESC')->paginate(20);
    
    unset($query, $seatPosition, $seatCode, $seatRow, $seatNumber, $flight);
    return response()->json(['res' => $seats], 200);
  }
}
