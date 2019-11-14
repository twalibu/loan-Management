<?php

namespace App\Http\Requests;

use App\Staff;
use Illuminate\Foundation\Http\FormRequest;

class StaffEditFormRequest extends FormRequest
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
        $id = $this->route('staff');
        $staff = Staff::findorfail($id);

        return [
            'first_name'        => 'required',
            'last_name'         => 'required',
            'phone_number'      => 'required',
            'type'              => 'required',
            'office'            => 'required',
            'email'             => 'required|email|max:255|unique:users,id,'.$staff->user->id,
            'password'          => 'nullable|confirmed|min:8',
        ];
    }
}
