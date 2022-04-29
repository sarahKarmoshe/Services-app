<?php

namespace App\Http\Requests;

use App\Models\Reservation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateReservationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(Reservation $reservation)
    {
        if($reservation->user_id=Auth::id()) {
            return true;
        }
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'service_id'=>['required','numeric'],
            'start_time'=>['required'],
            'end_time'=>['required'],
            'Gate_name'=>['required'],
            //  'staffs_id'=>['required'],
        ];
    }
}
