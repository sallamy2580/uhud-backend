<?php

namespace App\Http\Controllers\Airline;


use App\Models\Airline;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class AirlineController extends Controller
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
   * add new airline
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
      'country_id' => 'required|exists:countries,id|numeric',
      'name' => 'required|max:100',
      'logo_base64' => 'image_base64',
      'rate_avg' => 'required|numeric|between:1,5'
    ]);
    if ($validator->fails()) {
      $errors = $validator->errors();
      unset($validator, $request);
      return response()->json(['error' => $errors->all()], 500);
    }
    unset($validator);
    
    $airlineFields = $request->all();
    
    $airlineFields['creator'] = $this->user->id;
    
    Airline::create($airlineFields);
    unset($airlineFields);
    
    return response()->json(['res' => 'new airline created successfully'], 200);
  }
  
  /**
   * update exists airline
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
      'id' => 'required|exists:airlines,id|numeric',
      'country_id' => 'exists:countries,id|numeric',
      'name' => 'max:100',
      'logo_base64' => 'image_base64',
      'rate_avg' => 'numeric|between:1,5'
    ]);
    if ($validator->fails()) {
      $errors = $validator->errors();
      unset($validator, $request);
      return response()->json(['error' => $errors->all()], 500);
    }
    unset($validator);
    $updateFields = [];
    $airline = Airline::find($request->id);
    
    if (isset($request->country_id) && !empty($request->country_id))
      $updateFields['country_id'] = $request->country_id;
    
    if (isset($request->name) && !empty($request->name))
      $updateFields['name'] = $request->name;
    
    if (isset($request->logo_base64) && !empty($request->logo_base64))
      $updateFields['logo_base64'] = $request->logo_base64;
    
    if (isset($request->rate_avg) && !empty($request->rate_avg))
      $updateFields['rate_avg'] = $request->rate_avg;
    
    
    $airline->update($updateFields);
    unset($updateFields, $airline);
    
    return response()->json(['res' => 'airline updated successfully!'], 200);
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
    $airline = Airline::find($id);
    
    if (empty($airline))
      return response()->json(['error' => 'invalid airline!'], 404);
    
    $airline->delete();
    unset($airline);
    return response()->json(['res' => 'selected airline deleted successfully!'], 200);
    
  }
  
  /**
   * get all airlines
   *
   * @param \Illuminate\Http\Request $request
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function all(Request $request): JsonResponse
  {
    $this->me();
    $this->checkAccessControll(['admin', 'agent', 'user'], $this->user, null, null);
    
    $id = intval($request->query("id"));
    $creator = intval($request->query("creator"));
    $search = $request->query("search");
    $countryId = intval($request->query("country_id"));
    $rateAvg = intval($request->query("rate"));
    $query = null;
    $airlines = null;
    
    if (isset($id) && !empty($id))
      $query = Airline::where('id', $id);
    
    if (isset($creator) && !empty($creator))
      $query = Airline::where('creator', $creator);
    
    if (isset($countryId) && !empty($countryId))
      $query = Airline::where('country_id', $countryId);
    
    if (isset($rateAvg) && !empty($rateAvg))
      $query = Airline::where('rate_avg', $rateAvg);
    
    if (isset($search) && !empty($search))
      $query = Airline::where('name', 'like', '%' . $search . '%');
    
    $resultType = $request->query('type');
    if (isset($resultType) && !empty($resultType) && trim($resultType) == 'all') {
      if (!empty($query))
        $airlines = $query->orderBy('id', 'DESC')->get();
      else
        $airlines = Airline::orderBy('id', 'DESC')->get();
      
      $res = [
        "current_page" => null,
        "data" => $airlines,
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
      unset($query, $airlines, $creator, $countryId, $search, $rateAvg, $request);
      return response()->json(['res' => $res], 200);
    } else {
      if (!empty($query))
        $airlines = $query->orderBy('id', 'DESC')->paginate(20);
      else
        $airlines = Airline::orderBy('id', 'DESC')->paginate(20);
      
      unset($query, $creator, $countryId, $search, $rateAvg, $request);
      return response()->json(['res' => $airlines], 200);
    }
    
  }
}
