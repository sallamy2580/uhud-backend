<?php

namespace App\Http\Controllers\PDF;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use PDF;

class PDFController extends Controller
{
  
  /**
   * pds views base url
   *
   * @var string $basePdsViewsPath
   */
  protected $basePdsViewsPath = "pdfs.";
  
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
   * tst pdf functionality
   *
   *
   * @return mixed
   */
  public function tst()
  {
    $data = ['title' => 'Welcome to test pdf generator fro, laravel blade file'];
    $pdf = PDF::loadView($this->basePdsViewsPath . 'tst', $data);
    
    echo 'dowloading pdf...';
    return $pdf->download('alzuhud.pdf');
  }
  
  /**
   * updtae package
   *
   * @param integer $id
   * @param \Illuminate\Http\Request $request
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function bookingPdf(Request $request, $id)
  {
    
    $this->me($request, true);
    $this->checkAccessControll(['admin', 'agent', 'user'], $this->user, null, null);
    
    $booking = Booking::find($id);
//        return response()->json(['res' => $booking],200);
    if (empty($booking))
      return response()->json(['error' => 'invalid booking!'], 404);
    
    $data = ['booking' => $booking];
    $pdf = PDF::loadView($this->basePdsViewsPath . 'booking', $data);
    
    echo 'dowloading pdf...';
    return $pdf->download('alzuhud.pdf');
  }
}
