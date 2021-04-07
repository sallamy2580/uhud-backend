<?php


namespace App\Repositories\GlobalActions;


use Carbon\Carbon;

class GlobalActions implements GlobalActionsInterface
{
    /**
     * function for cumpute data time ago created
     *
     * @param string $time
     *
     * @return string
     */
    public function diffTime($time){
        $now = new Carbon();
        $timeCarbon = new Carbon($time);
        if( $now->diffInSeconds($timeCarbon) >= 0 && $now->diffInSeconds($timeCarbon) <= 29 ){

            if( $now->diffInSeconds($timeCarbon) > 1 ){
                return $now->diffInSeconds($timeCarbon).' seconds ago';
            }else{
                return $now->diffInSeconds($timeCarbon).' second ago';
            }

        }else if( $now->diffInMinutes($timeCarbon) >= 1 && $now->diffInMinutes($timeCarbon) <= 29 ){

            if( $now->diffInMinutes($timeCarbon) > 1 ){
                return $now->diffInMinutes($timeCarbon).' minutes ago';
            }else{
                return $now->diffInMinutes($timeCarbon).' minute ago';
            }

        }else if( $now->diffInHours($timeCarbon) >= 1 && $now->diffInHours($timeCarbon) <= 29 ){

            if( $now->diffInHours($timeCarbon) > 1 ){
                return $now->diffInHours($timeCarbon).' hours ago';
            }else{
                return $now->diffInHours($timeCarbon).' hour ago';
            }

        }else if( $now->diffInDays($timeCarbon) >= 1 && $now->diffInDays($timeCarbon) <= 29 ){

            if( $now->diffInDays($timeCarbon) > 1 ){
                return $now->diffInDays($timeCarbon).' days ago';
            }else{
                return $now->diffInDays($timeCarbon).' day ago';
            }

        }else if( $now->diffInMonths($timeCarbon) >= 1 && $now->diffInMonths($timeCarbon) <= 12 ){

            if( $now->diffInMonths($timeCarbon) > 1 ){
                return $now->diffInMonths($timeCarbon).' months ago';
            }else{
                return $now->diffInMonths($timeCarbon).' month ago';
            }

        }else if( $now->diffInYears($timeCarbon) >= 1 && $now->diffInYears($timeCarbon) <= 3 ){

            if( $now->diffInYears($timeCarbon) > 1 ){
                return $now->diffInYears($timeCarbon).' years ago';
            }else{
                return $now->diffInYears($timeCarbon).' year ago';
            }

        }else{
            return ' long time ago ';
        }
    }
}