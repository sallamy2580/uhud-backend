<?php

namespace App\Http\Controllers\Transaction;

use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TransactionController extends Controller
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
   * Get all Transactions
   *
   * @param \Illuminate\Http\Request $request
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function all(Request $request): JsonResponse
  {
    $this->me();
    $this->checkAccessControll(['admin', 'agent'], $this->user, null, null);
    $id = intval($request->query('id'));
    $userTransactions = intval($request->query('user'));
    $packageTransactions = intval($request->query('package'));
    /*5 paid - 1 not paid - 2 pending - 3 removed*/
    $status = intval($request->query('status'));
    $startDate = $request->query('start_date');
    $endDate = $request->query('end_date');
    $price = intval($request->query('price'));
    $search = ($request->query('search'));
    
    /*
     * 1 for more than current price
     * 2 for less than current price
     * 3 for equal with current price
     * 4 for more equal
     * 5 for less equal
    */
    $priceOperation = intval($request->query('price_op'));
    $query = null;
    $transactions = null;
    if (isset($id) && !empty($id))
      $query = Transaction::where('id', ($id));
    
    if (isset($userTransactions) && !empty($userTransactions))
      $query = Transaction::where('user_id', ($userTransactions));
    
    if (isset($packageTransactions) && !empty($packageTransactions))
      $query = Transaction::where('package_id', ($packageTransactions));
    
    if (isset($status) && !empty($status)) {
      if (intval($status) === 1)
        $query = Transaction::where('status', 1);
      else if (intval($status) === 2)
        $query = Transaction::where('status', 2);
      else if (intval($status) === 3)
        $query = Transaction::where('status', 3);
      else if (intval($status) === 5) /*all paid transactions*/
        $query = Transaction::where('status', 0);
    }
    
    if (isset($startDate) && !empty($startDate)) {
      try {
        $startDateCarbone = new Carbon($startDate);
        $query = Transaction::where('created_at', '>=', $startDateCarbone);
        unset($startDateCarbone);
      } catch (\Exception $ex) {
      }
    }
    
    if (isset($endDate) && !empty($endDate)) {
      try {
        $endDateCarbone = new Carbon($endDate);
        $query = Transaction::where('created_at', '<=', $endDateCarbone);
        unset($endDateCarbone);
      } catch (\Exception $ex) {
      }
    }
    
    if (isset($search) && !empty($search)) {
      $query = Transaction::where(function ($query) use ($search) {
        return $query->orWhere('title', 'like', '%' . $search . '%');
      });
    }
    
    if (isset($price) && !empty($price)) {
      if (isset($priceOperation) && !empty($priceOperation)) {
        if ($priceOperation === 1) {
          $query = Transaction::where('amount', '>', $price);
        } else if ($priceOperation === 2) {
          $query = Transaction::where('amount', '<', $price);
        } else if ($priceOperation === 3) {
          $query = Transaction::where('amount', '=', $price);
        } else if ($priceOperation === 4) {
          $query = Transaction::where('amount', '>=', $price);
        } else if ($priceOperation === 5) {
          $query = Transaction::where('amount', '<=', $price);
        } else {
          $query = Transaction::where('amount', '<=', $price);
        }
      } else {
        $query = Transaction::where('amount', '<=', $price);
      }
    }
    
    if (!empty($query))
      $transactions = $query->orderBy('id', 'DESC')->paginate(20);
    else
      $transactions = Transaction::orderBy('id', 'DESC')->paginate(20);
    
    unset($query, $status, $request, $priceOperation, $price, $startDate, $endDate,
      $userTransactions, $packageTransactions);
    return response()->json(['res' => $transactions], 200);
  }
}
