<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;


class User extends Authenticatable
{
    use  Notifiable;

    protected $table = 'users';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','phone','image','role','user_code',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    public function setResponseCode(int $responseCode){
        $this->_responseCode = $responseCode;
    }
    
    public function setStatus(bool $status){
        $this->_status = $status;
    }
    
    public function setMessage(String $message){
        $this->_message = $message;
    }
    
    public function setAccessToken(String $accessToken){
        $this->_accessToken = $accessToken;
    }
    public function listing()
    {
       return $this->hasMany('App\Models\Listing','id');
   }
    
    // public function setUserDetails(User $user){
    //     // $userRole = Config::get('constants.role_name.'.$user->role_id) ?? '';
    //     $image = asset($user->image);
    //     $this->setUserDetailsToArray($user->id, $user->name ?? '', $user->email ?? '', $user->phone ?? '', $user->role_id, $userRole, $image);
    // }
   
}
