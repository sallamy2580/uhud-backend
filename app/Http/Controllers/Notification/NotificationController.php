<?php

namespace App\Http\Controllers\Notification;

use App\Models\Notification;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NotificationController extends Controller
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
   * Get all Notifications
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
    /*0 for never seen 1 for seen*/
    $seen = $request->query('seen');
    /*0 deactive - 1 active - 2 banned - 3 removed*/
    $status = $request->query('status');
    $query = null;
    $notifications = null;
    if (isset($id) && !empty($id)) {
      $query = Notification::where('id', $id);
    }
    if (isset($seen) && !empty($seen)) {
      if (intval($seen) === 1)
        $query = Notification::where('seen', 1);
      else if (intval($seen) === 2)
        $query = Notification::where('seen', 0);
    }
    if (isset($status) && !empty($status)) {
      if (intval($status) === 1)
        $query = Notification::where('status', 1);
      else if (intval($status) === 2)
        $query = Notification::where('status', 2);
      else if (intval($status) === 3)
        $query = Notification::where('status', 3);
      else if (intval($status) === 4)
        $query = Notification::where('status', 4);
      else if (intval($status) === 5) /*de active notif selection*/
        $query = Notification::where('status', 0);
    }
    if (!empty($query))
      $notifications = $query->orderBy('id', 'DESC')->paginate(20);
    else
      $notifications = Notification::orderBy('id', 'DESC')->paginate(20);
    
    unset($seen, $query, $status, $request);
    return response()->json(['res' => $notifications], 200);
  }
  
  /**
   * Get count Notifications
   *
   * @param \Illuminate\Http\Request $request
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function count(Request $request): JsonResponse
  {
    $this->me();
    $this->checkAccessControll(['admin', 'agent'], $this->user, null, null);
    $id = $request->query('id');
    /*0 for never seen 1 for seen*/
    $seen = $request->query('seen');
    /*0 deactive - 1 active - 2 banned - 3 removed*/
    $status = $request->query('status');
    $query = null;
    $notifications = null;
    if (isset($id) && !empty($id)) {
      $query = Notification::where('id', $id);
    }
    
    if ((isset($seen) && !empty($seen)) || ($seen == '2' || $seen == '1')) {
      if ($seen == 1 || $seen == '1')
        $query = Notification::where('seen', 1);
      else if ($seen == 2 || $seen == '2')
        $query = Notification::where('seen', 0);
    }
    if (isset($status) && !empty($status)) {
      if (intval($status) === 1)
        $query = Notification::where('status', 1);
      else if (intval($status) === 2)
        $query = Notification::where('status', 2);
      else if (intval($status) === 3)
        $query = Notification::where('status', 3);
      else if (intval($status) === 4)
        $query = Notification::where('status', 4);
      else if (intval($status) === 5) /*de active notif selection*/
        $query = Notification::where('status', 0);
    }
    if (!empty($query))
      $notifications = $query->count();
    else
      $notifications = Notification::count();
    
    unset($seen, $query, $status, $request);
    return response()->json(['res' => $notifications], 200);
  }
  
  
}
