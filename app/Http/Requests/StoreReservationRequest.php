<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreReservationRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        if(Auth::guard('api')){
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
            'services_map'=>['required'],
            'start_time'=>['required'],
            'end_time'=>['required'],
            'Gate_name'=>['required'],
          //  'staffs_id'=>['required'],
        ];
    }
}
