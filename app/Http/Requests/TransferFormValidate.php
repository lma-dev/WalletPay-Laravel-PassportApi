<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransferFormValidate extends FormRequest
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
            'to_phone' => 'required|min:11|max:11' ,
            'amount' => 'required|integer',
            'hash_value' =>'required'

        ];
    }
        public function messages(){
            return [
                'hash_value.required' => 'The given data is invalid',
                'to_phone.min' => 'Phone Number is not valid',
                'to_phone.max' => 'Phone Number is not valid',
                'to_phone.required' => 'Please Fill the to account Information',
                'amount.required' => 'Please Fill the Amount',
            ];
        }
    }

