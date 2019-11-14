<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoanEditFormRequest extends FormRequest
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
            'loan_identity' => 'required',
            'client'        => 'required',
            'type'          => 'required',
            'penalt'        => 'required',
            'amount'        => 'required',
            'date'          => 'required',
            'office'        => 'required',
        ];
    }
}
