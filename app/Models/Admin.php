<?php

namespace App\Models;

use App\Notifications\ReSetPassword;
use App\Notifications\VerifyApiEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\HasApiTokens;

class Admin extends Authenticatable
{
    use HasFactory , HasApiTokens ,Notifiable;


    public function sendEmailVerificationPasswordAdmin($verification_code)
    {
        $this->notify(new ReSetPassword(Auth::id(),$verification_code)); // my notification
    }



    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',

    ];
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

}
