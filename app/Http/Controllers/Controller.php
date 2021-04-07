<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use phpDocumentor\Reflection\Types\Boolean;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * check roles accesse for current action
     *
     * @param array $rolesMustAccess
     * @param \App\Models\User $user
     * @param string $condition
     *
     * @return mixed
     */
    public function checkAccessControll($rolesMustAccess, $user, $condition, $identifer)
    {

        if( in_array("guest", $rolesMustAccess) )
            return true;

        if( trim($user->role) === 'admin' )
            return true;

        if (empty($condition)) {
            if (in_array($user->role, $rolesMustAccess))
                return true;
            else
                abort(401,'Access Denied!');
        } else {
            if (trim($condition) === 'user_id_equality') {
                if( trim($user->role) === 'user' ){
                    if( intval($user->id) === intval($identifer) )
                        return true;
                    else
                        abort(401,'Access Denied!');
                }else
                    return true;
            } else {
                return true;
            }
        }
    }
}
