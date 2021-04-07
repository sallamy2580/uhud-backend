<?php

namespace App\Http\Controllers\Hotels;

use App\Models\Hotel;
use App\Models\HotelImage;
use App\Models\HotelRoom;
use App\Models\HotelRoomReserve;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class HotelController extends Controller
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
   * update hotel
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
      'id' => 'required|exists:hotels,id',
      'en_name' => 'max:100',
      'name' => 'max:100',
      'description' => 'max:10000',
      'address' => 'max:100',
      'lat' => 'max:50',
      'lng' => 'max:50',
      'accomodation' => 'numeric|between:1,6',
      'main_image_url' => 'max:200',
      'main_image_base64' => 'image_base64',
      'price' => 'numeric',
      'price_double' => 'numeric',
      'price_triple' => 'numeric',
      'price_quad' => 'numeric',
      'hotel_images' => 'json',
      'hotel_rooms' => 'json'
    ]);
    if ($validator->fails()) {
      $errors = $validator->errors();
      unset($validator, $request);
      return response()->json(['error' => $errors->all()], 500);
    }
    unset($validator);
    
    $hotel = Hotel::find($request->id);
    
    if (isset($request->en_name) && !empty($request->en_name))
      $hotelUpdateFields['en_name'] = $request->en_name;
    
    if (isset($request->name) && !empty($request->name))
      $hotelUpdateFields['name'] = $request->name;
    
    if (isset($request->description) && !empty($request->description))
      $hotelUpdateFields['description'] = $request->description;
    
    if (isset($request->address) && !empty($request->address))
      $hotelUpdateFields['address'] = $request->address;
    
    if (isset($request->lat) && !empty($request->lat))
      $hotelUpdateFields['lat'] = $request->lat;
    
    if (isset($request->lng) && !empty($request->lng))
      $hotelUpdateFields['lng'] = $request->lng;
    
    if (isset($request->accomodation) && !empty($request->accomodation))
      $hotelUpdateFields['accomodation'] = $request->accomodation;
    
    if (isset($request->main_image_base64) && !empty($request->main_image_base64))
      $hotelUpdateFields['main_image_base64'] = $request->main_image_base64;
    
    if (isset($request->main_image_url) && !empty($request->main_image_url))
      $hotelUpdateFields['main_image_url'] = $request->main_image_url;
    
    if (isset($request->price) && !empty($request->price))
      $hotelUpdateFields['price'] = $request->price;
    
    if (isset($request->price_double) && !empty($request->price_double))
      $hotelUpdateFields['price_double'] = $request->price_double;
    
    if (isset($request->price_triple) && !empty($request->price_triple))
      $hotelUpdateFields['price_triple'] = $request->price_triple;
    
    if (isset($request->price_quad) && !empty($request->price_quad))
      $hotelUpdateFields['price_quad'] = $request->price_quad;
    
    $hotel->update($hotelUpdateFields);
    unset($hotelUpdateFields);
    
    if (isset($request->hotel_images) && !empty($request->hotel_images)) {
      $hotelImages = json_decode($request->hotel_images);
      $hotelImgField = [];
      HotelImage::where('hotel_id', $hotel->id)->delete();
      foreach ($hotelImages as $hotelImage) {
        $hotelImgField = [
          'hotel_id' => $hotel->id,
          'img_base64' => $hotelImage,
          'img_url' => '',
        ];
        HotelImage::create($hotelImgField);
      }
      unset($hotelImgField);
    }
    
    if (isset($request->hotel_images) && !empty($request->hotel_images)) {
      $hotelRooms = json_decode($request->hotel_rooms);
      $hotelRoomFields = [];
      HotelRoom::where('hotel_id', $hotel->id)->delete();
      foreach ($hotelRooms as $hotelRoom) {
        if (!isset($hotelRoom->name) || empty($hotelRoom->name) ||
          !isset($hotelRoom->room_type) || empty($hotelRoom->room_type))
          continue;
        
        $hotelRoomFields = [
          'hotel_id' => $hotel->id,
          'name' => $hotelRoom->name,
          'room_type' => $hotelRoom->room_type,
          'description' => (isset($hotelRoom->description) && !empty($hotelRoom->description)) ?
            $hotelRoom->description : '',
          'view' => (isset($hotelRoom->view) && !empty($hotelRoom->view)) ? $hotelRoom->view : '',
          'extera' => (isset($hotelRoom->extera) && !empty($hotelRoom->extera)) ? $hotelRoom->extera : '',
          'main_img_base64' => (isset($hotelRoom->main_img_base64) && !empty($hotelRoom->main_img_base64)) ?
            $hotelRoom->main_img_base64 : '',
          'main_img_url' => ''
        ];
        HotelRoom::create($hotelRoomFields);
      }
      unset($hotelRoomFields);
    }
    
    return response()->json(['res' => 'hotel information updated successfully.'], 200);
  }
  
  /**
   * add new hotel
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
      'en_name' => 'required|max:100',
      'name' => 'max:100',
      'description' => 'max:10000',
      'address' => 'max:100',
      'lat' => 'max:50',
      'lng' => 'max:50',
      'accomodation' => 'numeric|between:1,6',
      'main_image_url' => 'max:200',
      'price' => 'numeric',
      'price_double' => 'numeric',
      'price_triple' => 'numeric',
      'price_quad' => 'numeric',
      'hotel_images' => 'json',
      'hotel_rooms' => 'json'
    ]);
    if ($validator->fails()) {
      $errors = $validator->errors();
      unset($validator, $request);
      return response()->json(['error' => $errors->all()], 500);
    }
    unset($validator);
    
    $hotelFields = [
      'creator' => $this->user->id,
      'en_name' => $request->en_name,
      'name' => (isset($request->name) && !empty($request->name)) ? $request->name : '',
      'description' => (isset($request->description) && !empty($request->description)) ? $request->description : '',
      'address' => (isset($request->address) && !empty($request->address)) ? $request->address : '',
      'lat' => (isset($request->lat) && !empty($request->lat)) ? $request->lat : '',
      'lng' => (isset($request->lng) && !empty($request->lng)) ? $request->lng : '',
      'accomodation' => (isset($request->accomodation) && !empty($request->accomodation)) ? $request->accomodation : 1,
      'main_image_base64' => (isset($request->main_image_base64) && !empty($request->main_image_base64)) ? $request->main_image_base64 : '',
      'main_image_url' => '',
      'price' => (isset($request->price) && !empty($request->price)) ? $request->price : 0,
      'price_double' => (isset($request->price_double) && !empty($request->price_double)) ? $request->price_double : 0,
      'price_triple' => (isset($request->price_triple) && !empty($request->price_triple)) ? $request->price_triple : 0,
      'price_quad' => (isset($request->price_quad) && !empty($request->price_quad)) ? $request->price_quad : 0
    ];
    
    $createdHotel = Hotel::create($hotelFields);
    unset($hotelFields);
    
    if (isset($request->hotel_images) && !empty($request->hotel_images)) {
      $hotelImages = json_decode($request->hotel_images);
      $hotelImgField = [];
      foreach ($hotelImages as $hotelImage) {
        $hotelImgField = [
          'hotel_id' => $createdHotel->id,
          'img_base64' => $hotelImage,
          'img_url' => '',
        ];
        HotelImage::create($hotelImgField);
      }
      unset($hotelImgField);
    }
    
    if (isset($request->hotel_images) && !empty($request->hotel_images)) {
      $hotelRooms = json_decode($request->hotel_rooms);
      $hotelRoomFields = [];
      foreach ($hotelRooms as $hotelRoom) {
        if (!isset($hotelRoom->name) || empty($hotelRoom->name) ||
          !isset($hotelRoom->room_type) || empty($hotelRoom->room_type))
          continue;
        
        $hotelRoomFields = [
          'hotel_id' => $createdHotel->id,
          'name' => $hotelRoom->name,
          'room_type' => $hotelRoom->room_type,
          'description' => (isset($hotelRoom->description) && !empty($hotelRoom->description)) ?
            $hotelRoom->description : '',
          'view' => (isset($hotelRoom->view) && !empty($hotelRoom->view)) ? $hotelRoom->view : '',
          'extera' => (isset($hotelRoom->extera) && !empty($hotelRoom->extera)) ? $hotelRoom->extera : '',
          'main_img_base64' => (isset($hotelRoom->main_img_base64) && !empty($hotelRoom->main_img_base64)) ?
            $hotelRoom->main_img_base64 : '',
          'main_img_url' => ''
        ];
        HotelRoom::create($hotelRoomFields);
      }
      unset($hotelRoomFields);
    }
    
    return response()->json(['res' => $createdHotel], 200);
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
    $this->me($request, true);
    $hotel = Hotel::find($id);
    
    if (empty($hotel))
      return response()->json(['error' => 'invalid hotel!'], 404);
    
    $hotel->delete();
    unset($hotel);
    return response()->json(['res' => 'selected hotel deleted successfully!'], 200);
    
  }
  
  /**
   * Get all Hotels
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
    $search = $request->query('search');
    $accomodation = $request->query('accomodation');
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
    $hotels = null;
    if (isset($id) && !empty($id))
      $query = Hotel::where('id', intval($id));
    
    if (isset($creator) && !empty($creator))
      $query = Hotel::where('creator', intval($creator));
    
    if (isset($accomodation) && !empty($accomodation))
      $query = Hotel::where('accomodation', intval($accomodation));
    
    if (isset($search) && !empty($search)) {
      $query = Hotel::where(function ($query) use ($search) {
        return $query->orWhere('name', 'like', '%' . $search . '%')
          ->orWhere('description', 'like', '%' . $search . '%')
          ->orWhere('address', 'like', '%' . $search . '%');
      });
    }
    
    if (isset($price) && !empty($price)) {
      if (isset($priceOperation) && !empty($priceOperation)) {
        if ($priceOperation === 1) {
          $query = Hotel::where('price', '>', $price);
        } else if ($priceOperation === 2) {
          $query = Hotel::where('price', '<', $price);
        } else if ($priceOperation === 3) {
          $query = Hotel::where('price', '=', $price);
        } else if ($priceOperation === 4) {
          $query = Hotel::where('price', '>=', $price);
        } else if ($priceOperation === 5) {
          $query = Hotel::where('price', '<=', $price);
        } else {
          $query = Hotel::where('price', '<=', $price);
        }
      } else {
        $query = Hotel::where('price', '<=', $price);
      }
    }
    
    
    $resultType = $request->query('type');
    if (isset($resultType) && !empty($resultType) && trim($resultType) == 'all') {
      if (!empty($query))
        $hotels = $query->orderBy('id', 'DESC')->get();
      else
        $hotels = Hotel::orderBy('id', 'DESC')->get();
      
      
      $res = [
        "current_page" => null,
        "data" => $hotels,
        "first_page_url" => null,
        "from" => null,
        "last_page" => null,
        "last_page_url" => null,
        "next_page_url" => null,
        "path" => null,
        "per_page" => null,
        "prev_page_url" => null,
        "to" => null,
        "total" => null,
      ];
      unset($query, $hotels, $accomodation, $search, $request, $priceOperation, $price, $creator);
      return response()->json(['res' => $res], 200);
      
    } else {
      if (!empty($query))
        $hotels = $query->orderBy('id', 'DESC')->paginate(20);
      else
        $hotels = Hotel::orderBy('id', 'DESC')->paginate(20);
      
      unset($query, $accomodation, $search, $request, $priceOperation, $price, $creator);
      return response()->json(['res' => $hotels], 200);
    }
    
    
  }
  
  /**
   * Get all Hotel Images
   *
   * @param \Illuminate\Http\Request $request
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function hotelImages($hotelId): JsonResponse
  {
    $this->me();
    $this->checkAccessControll(['admin', 'agent', 'user'], $this->user, null, null);
    $images = HotelImage::where('hotel_id', $hotelId)->paginate(50);
    return response()->json(['res' => $images], 200);
  }
  
  /**
   * Get all Hotel Rooms
   *
   * @param \Illuminate\Http\Request $request
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function hotelRooms($hotelId): JsonResponse
  {
    $this->me();
    $this->checkAccessControll(['admin', 'agent', 'user'], $this->user, null, null);
    $rooms = HotelRoom::where('hotel_id', $hotelId)->paginate(50);
    return response()->json(['res' => $rooms], 200);
  }
  
  /**
   * Get all Hotel Rooms
   *
   * @param integer $hotelId
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function hotelRoomReserves($hotelId): JsonResponse
  {
    $this->me();
    $this->checkAccessControll(['admin', 'agent', 'user'], $this->user, null, null);
    $reserveRooms = HotelRoom::where('hotel_id', $hotelId)->get();
    return response()->json(['res' => $reserveRooms], 200);
  }
  
  /**
   * Get all Hotel Rooms
   *
   * @param \Illuminate\Http\Request $request
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function allRooms(Request $request): JsonResponse
  {
    $this->me();
    $this->checkAccessControll(['admin', 'agent', 'user'], $this->user, null, null);
    $id = intval($request->query('id'));
    $hotelId = intval($request->query('hotel_id'));
    $roomType = intval($request->query('room_type'));
    $search = $request->query('search');
    
    $query = null;
    $hotelRooms = null;
    if (isset($id) && !empty($id))
      $query = HotelRoom::where('id', intval($id));
    
    if (isset($hotelId) && !empty($hotelId))
      $query = HotelRoom::where('hotel_id', intval($hotelId));
    
    if (isset($roomType) && !empty($roomType))
      $query = HotelRoom::where('room_type', intval($roomType));
    
    if (isset($search) && !empty($search)) {
      $query = HotelRoom::where(function ($query) use ($search) {
        return $query->orWhere('name', 'like', '%' . $search . '%')
          ->orWhere('description', 'like', '%' . $search . '%')
          ->orWhere('extera', 'like', '%' . $search . '%')
          ->orWhere('view', 'like', '%' . $search . '%');
      });
    }
    
    if (!empty($query))
      $hotelRooms = $query->orderBy('id', 'DESC')->paginate(20);
    else
      $hotelRooms = HotelRoom::orderBy('id', 'DESC')->paginate(20);
    
    unset($query, $roomType, $search, $request, $hotelId, $id);
    return response()->json(['res' => $hotelRooms], 200);
  }
  
  /**
   * Get all Hotel Room Reserves
   *
   * @param \Illuminate\Http\Request $request
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function allReserves(Request $request): JsonResponse
  {
    $this->me();
    $this->checkAccessControll(['admin', 'agent', 'user'], $this->user, null, null);
    $id = intval($request->query('id'));
    $hotelId = intval($request->query('hotel_id'));
    $roomId = intval($request->query('room_id'));
    $userId = intval($request->query('user_id'));
    $transactionId = intval($request->query('transaction_id'));
    $search = $request->query('search');
    $price = intval($request->query('price'));
    $priceOperation = intval($request->query('price_op'));
    
    $query = null;
    $hotelRoomReseves = null;
    if (isset($id) && !empty($id))
      $query = HotelRoomReserve::where('id', intval($id));
    
    if (isset($hotelId) && !empty($hotelId))
      $query = HotelRoomReserve::where('hotel_id', intval($hotelId));
    
    if (isset($roomId) && !empty($roomId))
      $query = HotelRoomReserve::where('room_id', intval($roomId));
    
    if (isset($userId) && !empty($userId))
      $query = HotelRoomReserve::where('user_id', intval($userId));
    
    if (isset($transactionId) && !empty($transactionId))
      $query = HotelRoomReserve::where('transaction_id', intval($transactionId));
    
    if (isset($search) && !empty($search)) {
      $query = HotelRoomReserve::where(function ($query) use ($search) {
        return $query->orWhere('extera', 'like', '%' . $search . '%')
          ->orWhere('description', 'like', '%' . $search . '%');
      });
    }
    
    if (isset($price) && !empty($price)) {
      if (isset($priceOperation) && !empty($priceOperation)) {
        if ($priceOperation === 1) {
          $query = HotelRoomReserve::where('final_price', '>', $price);
        } else if ($priceOperation === 2) {
          $query = HotelRoomReserve::where('final_price', '<', $price);
        } else if ($priceOperation === 3) {
          $query = HotelRoomReserve::where('final_price', '=', $price);
        } else if ($priceOperation === 4) {
          $query = HotelRoomReserve::where('final_price', '>=', $price);
        } else if ($priceOperation === 5) {
          $query = HotelRoomReserve::where('final_price', '<=', $price);
        } else {
          $query = HotelRoomReserve::where('final_price', '<=', $price);
        }
      } else {
        $query = HotelRoomReserve::where('final_price', '<=', $price);
      }
    }
    
    if (!empty($query))
      $hotelRoomReseves = $query->orderBy('id', 'DESC')->paginate(20);
    else
      $hotelRoomReseves = HotelRoomReserve::orderBy('id', 'DESC')->paginate(20);
    
    unset($request, $query, $id, $hotelId, $roomId, $userId, $transactionId, $search, $price, $priceOperation);
    return response()->json(['res' => $hotelRoomReseves], 200);
  }
}
