<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $table = 'services';
    protected $primaryKey = 'id';
    protected $fillable = ['name', 'street', 'IsActive'];

    public $with = ['reservations'];


    public function reservations(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Reservation::class, 'service_id');
    }

//    public function staffReservations(): \Illuminate\Database\Eloquent\Relations\HasMany
//    {
//        return $this->hasMany(Reservation::class, 'service_id');
//    }

}
