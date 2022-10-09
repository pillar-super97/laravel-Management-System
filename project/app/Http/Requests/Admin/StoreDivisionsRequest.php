<?php
namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreDivisionsRequest extends FormRequest
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
            'name' => 'required',
            //'phone' => 'required|regex:/(01)[0-9]{9}/',
            'address'=> 'required',
            'zip'=>'nullable|numeric',
//            //'scheduling_contact_phone'=>'nullable',
//            'sec_scheduling_contact_phone'=>'nullable|regex:/(01)[0-9]{9}/',
//            'billing_contact_phone'=>'nullable|regex:/(01)[0-9]{9}/',
            'rate' => 'nullable|numeric'
        ];
    }
}
