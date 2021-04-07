<?php

namespace App\Http\Controllers\Ticket;

use App\Models\Ticket;
use App\Models\TicketSection;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;

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
   * Get all Tickets
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
      $tickets = $query->orderBy('id', 'DESC')->paginate(20);
    else
      $tickets = Ticket::orderBy('id', 'DESC')->paginate(20);
    
    unset($status);
    return response()->json(['res' => $tickets], 200);
  }
  
  /**
   * Get all Ticket Sections
   *
   * @param \Illuminate\Http\Request $request
   * @param integer $ticketId
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function ticketSections(Request $request, $ticketId): JsonResponse
  {
    $this->me();
    $this->checkAccessControll(['admin', 'agent', 'user'], $this->user, null, null);
    
    $ticket = Ticket::find($ticketId);
    if (empty($ticket))
      return response()->json(['error' => 'invalid ticket!'], 404);
    
    $sections = TicketSection::where('ticket_id', $ticket->id)->orderBy('id', 'DESC')->paginate(50);
    
    unset($request, $ticketId);
    return response()->json(['ticket' => $ticket, 'sections' => $sections], 200);
  }
  
  /**
   * add new ticket
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
      'subject' => 'required|max:30',
      'body' => 'required|max:150000|min:3',
      'ticket_img' => 'image_base64'
    ], [
      'ticket_img.image_base64' => 'selected image is invalid, true image formats(jpeg, jpg, png, gif, svg)'
    ]);
    if ($validator->fails()) {
      $errors = $validator->errors();
      unset($validator, $request);
      return response()->json(['error' => $errors->all()], 500);
    }
    Ticket::create([
      'user_id' => $this->user->id,
      'subject' => $request->subject,
      'body' => $request->body,
      'status' => 1,
      'ticket_img' => (isset($request->ticket_img) && !empty($request->ticket_img)) ? $request->ticket_img : ''
    ]);
    unset($request, $validator);
    return response()->json(['res' => 'new ticket created successfully'], 201);
  }
  
  /**
   * add new ticket answer
   *
   * @param \Illuminate\Http\Request $request
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function answer(Request $request, $ticketId): JsonResponse
  {
    $this->me($request, false);
    $this->checkAccessControll(['admin', 'agent', 'user'], $this->user, null, null);
    
    $ticket = Ticket::find($ticketId);
    
    if (empty($ticket))
      return response()->json(['res' => 'invalid ticket please try with valid ticket info.'], 404);
    
    $validator = Validator::make($request->all(), [
      'body' => 'required|max:150000|min:3'
    ]);
    if ($validator->fails()) {
      $errors = $validator->errors();
      unset($validator, $request);
      return response()->json(['error' => $errors->all()], 500);
    }
    $userRole = [
      'admin' => 1,
      'agent' => 2,
      'user' => 5
    ];
    TicketSection::create([
      'ticket_id' => $ticket->id,
      'user_id' => $this->user->id,
      'body' => $request->body,
      'user_role' => intval($userRole[$this->user->role]),
      'ticket_img' => null,
      'ticket_img_url' => null
    ]);
    
    $ticketUpdateFields = [];
    if (intval($this->user->role) === 5)
      $ticketUpdateFields['status'] = 1;
    else if (intval($this->user->role) === 0 || intval($this->user->role) === 1 || intval($this->user->role) === 2)
      $ticketUpdateFields['status'] = 2;
    
    $ticket->update($ticketUpdateFields);
    
    unset($ticket, $validator, $ticketId, $request, $ticketUpdateFields);
    return response()->json(['res' => 'ticket answer applied wait for response from supporters...'], 201);
  }
  
  /**
   * closing ticket
   *
   * @param integer $ticketId
   * @param \Illuminate\Http\Request $request
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function close(Request $request, $ticketId): JsonResponse
  {
    $this->me($request, true);
    $ticket = Ticket::find($ticketId);
    if (empty($ticket))
      return response()->json(['res' => 'ticket not found, invalid info.'], 404);
    
    $this->checkAccessControll(['admin', 'agent', 'user'], $this->user, 'user_id_equality', $ticket->user_id);
    
    $ticket->update(['status' => 3]);
    
    unset($ticket, $ticketId);
    return response()->json(['res' => 'ticket closed successfully.'], 203);
  }
  
  /**
   * delete specifiedticket
   *
   * @param integer $ticketId
   * @param \Illuminate\Http\Request $request
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function delete(Request $request, $ticketId): JsonResponse
  {
    $this->me($request, true);
    $ticket = Ticket::find($ticketId);
    if (empty($ticket))
      return response()->json(['res' => 'ticket not found, invalid info.'], 404);
    
    $this->checkAccessControll(['admin', 'agent', 'user'], $this->user, 'user_id_equality', $ticket->user_id);
    
    $ticket->delete();
    
    unset($ticket, $ticketId);
    return response()->json(['res' => 'ticket deleted successfully.'], 203);
  }
}
