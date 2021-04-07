<?php


namespace App\Repositories\GlobalActions;

interface GlobalActionsInterface
{
    /**
     * function for cumpute data time ago created
     *
     * @param string $time
     *
     * @return string
     */
    public function diffTime($time);
}