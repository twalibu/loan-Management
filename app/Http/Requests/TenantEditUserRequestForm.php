<?php

namespace App\Http\Requests;

use Sentinel;
use Illuminate\Foundation\Http\FormRequest;

class TenantEditUserRequestForm extends FormRequest
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
        $id = Sentinel::getUser()->id;

        return [
            'first_name'    => 'required',
            'last_name'     => 'required',
            'email'         => 'required|email|max:255|unique:users,id,'.$id,
            'password'      => 'nullable|confirmed|min:6',
        ];
    }
}
