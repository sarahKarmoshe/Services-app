<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffReservation extends Model
{
    use HasFactory;
    protected $table = 'staff_reservations';
    protected $primaryKey = 'id';
    protected $fillable = [
        'start_time',
        'end_time',
        'staff_id',
        'service_id',
        'date',
    ];


    public function service(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
    public function staff(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }

}
