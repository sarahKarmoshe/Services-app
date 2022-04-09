<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReservationRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
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
            'year'=>['required'],
            'month'=>['required'],
            'day'=>['required'],
            'start_time'=>['required'],
            'period'=>['required'],
            'staffs_id'=>['required'],
        ];
    }
}
