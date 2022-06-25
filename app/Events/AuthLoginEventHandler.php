<?php

namespace App\Events;

use App\Models\Monitorization;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Auth\Events\Login;

class AuthLoginEventHandler
{
    use InteractsWithSockets;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function handle(Login $event)
    {
          Monitorization::create([
            'user_id'=>$event->user->id,
            'last_login'=> Carbon::now(),
        ]);
    }
}
