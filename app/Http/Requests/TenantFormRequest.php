<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TenantFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
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
            'name'          => 'required',
            'slug'          => 'required',
            'region'        => 'required',
            'subscription'  => 'required',            
            'smsusername'   => 'required',
            'smspassword'   => 'required',
            'sender'        => 'required',
            'price'         => 'required',
            'balance'       => 'required',
            'first_name'    => 'required',
            'last_name'     => 'required',
            'position'      => 'required',
            'office'        => 'required',
            'phone_number'  => 'required',
            'email'         => 'required',  
        ];
    }
}
