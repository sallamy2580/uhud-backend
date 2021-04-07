<?php

namespace App\Http\Controllers\User;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TicketController extends Controller
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
      abort(404, 'user not found');
    
    unset($userInSystem);
  }
  
  
  /**
   * Get all Tickets
   *
   * @param \Illuminate\Http\Request $request
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function all(Request $request): JsonResponse
  {
    $this->me();
    $this->checkAccessControll(['admin', 'agent', 'user'], $this->user, null, null);
    
    $id = $request->query('id');
    $status = $request->query('status');
    $search = ($request->query('search'));
    $tickets = null;
    $query = null;
    if (isset($id) && !empty($id)) {
      $query = Ticket::where('id', $id);
    }
    if (isset($status) && !empty($status)) {
      if (intval($status) === 1)
        $query = Ticket::where('status', 1)->orderBy('id', 'DESC');
      else if (intval($status) === 2)
        $query = Ticket::where('status', 2)->orderBy('id', 'DESC');
      else if (intval($status) === 3)
        $query = Ticket::where('status', 3)->orderBy('id', 'DESC');
      else if ($status == '-1' || $status == -1)
        $query = Ticket::orderBy('id', 'DESC');
    } else {
      $query = Ticket::orderBy('id', 'DESC');
    }
    
    if (isset($search) && !empty($search)) {
      $query = Ticket::where(function ($query) use ($search) {
        return $query->orWhere('subject', 'like', '%' . $search . '%')
          ->orWhere('body', 'like', '%' . $search . '%');
      });
    }
    
    if (!empty($query))
      $tickets = $query->where('user_id', $this->user->id)->orderBy('id', 'DESC')->paginate(20);
    else
      $tickets = Ticket::where('user_id', $this->user->id)->orderBy('id', 'DESC')->paginate(20);
    
    unset($status);
    return response()->json(['res' => $tickets], 200);
  }
}
