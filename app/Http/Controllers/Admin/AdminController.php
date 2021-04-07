<?php

namespace App\Http\Controllers\Admin;

use App\Models\Booking;
use App\Models\Package;
use App\Models\Ticket;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
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
     * @return void
     */
    public function me()
    {
        $this->user = auth()->user();
        if (empty($this->user))
            abort(401);

        $userInSystem = User::where('is_email_verified', 1)->where('id', $this->user->id)->exists();
        if (empty($userInSystem))
            abort(404,'user not found');

        unset($userInSystem);
    }

    /**
     * get dashboard infoes
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function dashboard(Request $request): JsonResponse
    {
        $this->me();
        $this->checkAccessControll(['admin', 'agent'], $this->user, null, null);
        $now = new Carbon();
        $subDays = 25;
        $querySubDays = intval($request->query('days'));
        if (isset($querySubDays) && !empty($querySubDays) && intval($querySubDays) >= 0)
            $subDays = $querySubDays;

        $now = $now->subDays($subDays);
        $statistics = [
            'tickets' => Ticket::where('created_at', '>=', $now)->count(),
            'orders' => Booking::where('created_at', '>=', $now)->count(),
            'transactions' => Transaction::where('created_at', '>=', $now)->count(),
            'agents' => User::where('role', 2)->where('created_at', '>=', $now)->count(),
            'users' => User::where('role', 5)->where('created_at', '>=', $now)->count(),
            'packages' => Package::where('created_at', '>=', $now)->count(),
        ];
        unset($now);
        return response()->json(['res' => $statistics], 200);
    }

    /**
     * Get all Contacts
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function allContacts(Request $request): JsonResponse
    {
        $this->me();
        $this->checkAccessControll(['admin', 'agent'], $this->user, null, null);
        $contacts = Ticket::orderBy('id', 'asc')->paginate(20);
        unset($request);
        return response()->json(['res' => $contacts], 200);
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
            'password' => 'required|confirmed|between:8,30',
        ]);

        $this->me();
        $this->checkAccessControll(['admin', 'agent'], $this->user, null, null);

        if ($validator->fails()) {
            $errors = $validator->errors();
            unset($validator, $request);
            return response()->json(['error' => $errors->all()], 500);
        }
        $user = User::find($request->id);

        $user->password = $request->password;
        $user->save();
        unset($user, $validator, $request);
        return response()->json(['msg' => 'password updated successfully...'], 201);
    }
}
