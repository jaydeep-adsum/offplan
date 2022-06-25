<?php

namespace App\Providers;
use App\Models\Permission_role_mapping;
use DB;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
    
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {   
        view()->composer(['*'], function ($view) {
            if(!\Auth::check())
            {
                return;
            }
            $permission_menu = Permission_role_mapping::where(['user_id'=>Auth()->user()->id,'read'=>1])->get();
            $view->with('permission_menu', $permission_menu);
        });
    }
}
