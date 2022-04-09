<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    use HasFactory;

    protected $table = 'staff';
    protected $primaryKey = 'id';
    protected $fillable = [
        'staff_name'
    ];
    public $with =['staffReservation'];

    public function staffReservation(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(StaffReservation::class,'staff_id');
    }

}
