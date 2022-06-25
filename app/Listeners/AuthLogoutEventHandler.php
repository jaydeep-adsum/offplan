<?php

namespace App\Listeners;

use App\Models\Monitorization;
use Carbon\Carbon;
use DateTime;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Auth\Events\Logout;

class AuthLogoutEventHandler
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function handle($event)
    {
        $monitorization = Monitorization::where('user_id',$event->user->id)->latest('created_at')->first();
        $last_login = new DateTime($monitorization->last_login);
        $login_time = $last_login->diff(new DateTime());
        $monitorization->last_logout = Carbon::now();
        $log_time = $login_time->format('%H:%i:%s');
        $monitorization->login_time = $log_time;
        $monitorization->save();
    }
}
