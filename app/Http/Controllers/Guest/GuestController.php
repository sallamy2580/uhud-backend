<?php

namespace App\Http\Controllers\Guest;

use App\Models\Contact;
use App\Models\Country;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class GuestController extends Controller
{

    /**
     * user logged
     *
     * @var User $user
     */
    protected $user = null;

    protected $GOOGLE_RECAPCTHA_SECRET = '6LfowrkUAAAAACD1iChigFCLKQuFL9-5mus6oAaj';

    /**
     * Get the authenticated User.
     *
     * @return void
     */
    public function me()
    {
        $this->user = auth()->user();
    }


    /**
     * request for check recaptcha
     *
     * @param string $reCaptcha
     *
     * @return object
     */
    function reCapcthaRequest($reCaptcha)
    {
        $customParams = [
            "secret" => $this->GOOGLE_RECAPCTHA_SECRET,
            "response" => $reCaptcha
        ];
        $buildData = json_encode($customParams, true);
        $ch = curl_init("https://www.google.com/recaptcha/api/siteverify");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $buildData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($buildData))
        );
        $result = curl_exec($ch);
        curl_close($ch);
        unset($customParams, $apiUrl);
        return json_decode($result);
    }

    /**
     * add new contact info
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function addNewContact(Request $request): JsonResponse
    {
        $this->me();
        $this->checkAccessControll(['guest'], null, null, null);
        $validator = Validator::make($request->all(), [
            'subject' => 'required|max:50',
            'message' => 'required|max:150000|min:3',
            'email' => 'email|max:200',
            'name' => 'max:50',
            'recaptcha_token' => 'required'
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            unset($validator, $request);
            return response()->json(['error' => $errors->all()], 500);
        }

//        $recaptchaReqResp = $this->reCapcthaRequest($request->recaptcha_token);
//        if (empty($recaptchaReqResp) || !$recaptchaReqResp->success) {
//            $errorCodeKey = 'error-codes';
//            return response()->json(['res' => $recaptchaReqResp->$errorCodeKey], 500);
//        }


        $fields = $request->all();
        if (isset($this->user) && !empty($this->user))
            $fields['user_id'] = $this->user->id;

        Contact::create($fields);
        unset($fields, $request, $validator);
        return response()->json(['res' => 'your contact information sent ,please wait for supporters answer.'], 201);
    }

    /**
     * Get all Countries
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function countries(Request $request): JsonResponse
    {
        $this->checkAccessControll(['guest'], null, null, null);
        $countries = Country::orderBy('id', 'asc')->groupBy('countries.name')
            ->orderBy('countries.name', 'asc')
            ->get();
        unset($request);
        return response()->json(['res' => $countries], 200);
    }

    /**
     * Get all states
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function states(Request $request): JsonResponse
    {
        $this->checkAccessControll(['guest'], null, null, null);
        $states = null;
        $idetification = false;
        $countryId = null;
        $stateId = null;
        $search = '';
        if (isset($request->country_id) && !empty($request->country_id)) {
            $idetification = true;
            $countryId = $request->country_id;
        }

        if (isset($request->state_id) && !empty($request->state_id)) {
            $idetification = true;
            $stateId = $request->state_id;
        }

        if (isset($request->search) && !empty($request->search)) {
            $idetification = true;
            $search = $request->search;
        }

        if (!$idetification)
            return response()->json(['res' => 'invalid request! please choose one country for get cities info'], 404);

        unset($idetification);

        $citiesQuery = DB::table('states');

        if (isset($countryId) && !empty($countryId))
            $citiesQuery = $citiesQuery->where('states.country_id', $countryId);

        if (isset($stateId) && !empty($stateId))
            $citiesQuery = $citiesQuery->where('states.id', $stateId);

        if (isset($search) && !empty($search)) {
            $citiesQuery = $citiesQuery->where(function ($query) use ($search) {
                return $query->where('states.name', 'like', '%' . $search . '%');
            });
        }
        $states = $citiesQuery->select('states.id as stateId', 'states.name as stateName')
            ->groupBy('states.name')
            ->orderBy('states.name', 'asc')
            ->get();

        return response()->json(['res' => $states, 'count' => count($states)], 200);
    }

    /**
     * Get all Countries
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function cities(Request $request): JsonResponse
    {
        $this->checkAccessControll(['guest'], null, null, null);
        $cities = null;
        $idetification = false;
        $countryId = null;
        $stateId = null;
        $cityId = null;
        $search = '';
        if (isset($request->country_id) && !empty($request->country_id)) {
            $idetification = true;
            $countryId = $request->country_id;
        }

        if (isset($request->state_id) && !empty($request->state_id)) {
            $idetification = true;
            $stateId = $request->state_id;
        }

        if (isset($request->city_id) && !empty($request->city_id)) {
            $idetification = true;
            $cityId = $request->city_id;
        }

        if (isset($request->search) && !empty($request->search)) {
            $idetification = true;
            $search = $request->search;
        }

        if (!$idetification)
            return response()->json(['res' => 'invalid request! please choose one country for get cities info'], 404);

        unset($idetification);

        $citiesQuery = DB::table('cities');

        if (isset($countryId) && !empty($countryId))
            $citiesQuery = $citiesQuery->where('cities.country_id', $countryId);

        if (isset($stateId) && !empty($stateId))
            $citiesQuery = $citiesQuery->where('cities.state_id', $stateId);

        if (isset($cityId) && !empty($cityId))
            $citiesQuery = $citiesQuery->where('cities.id', $cityId);

        if (isset($search) && !empty($search)) {
            $citiesQuery = $citiesQuery->where(function ($query) use ($search) {
                return $query->where('cities.name', 'like', '%' . $search . '%');
            });
        }
        $cities = $citiesQuery->select('cities.id as cityId', 'cities.name as cityName')
            ->groupBy('cities.name', 'cities.id')
            ->orderBy('cities.name', 'asc')
            ->get();

        return response()->json(['res' => $cities, 'count' => count($cities)], 200);
    }
}
