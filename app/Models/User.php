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

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public function sendApiEmailVerificationNotification($verification_code)
    {
        $this->notify(new VerifyApiEmail(Auth::id(),$verification_code)); // my notification
    }

    public function sendEmailVerificationPassword($verification_code)
    {
        $this->notify(new ReSetPassword(Auth::id(),$verification_code)); // my notification
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',

    ];

    public function reservations()
    {
        return $this->hasMany(Reservation::class, 'user_id');
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
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
