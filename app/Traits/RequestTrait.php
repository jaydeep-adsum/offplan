<?php


namespace App\Traits;

use Auth;
trait RequestTrait
{
    protected $authToken = null;

    /**
     * This will chek user is superadmin or not
     *
     * @return boolean
     */
    public function isAdmin() :bool
    {
        return (  Auth::user()->role == 1);
    }


    /**
     * Ths will check user is company admin or not
     *
     * @return boolean
     */
    public function isAgentUser() :bool
    {
        return ( Auth::user()->role == 2);
    }

    /**
     * @return bool
     */
    public function isAssociateUser() :bool
    {
        return ( Auth::user()->role == 3);
    }
}
