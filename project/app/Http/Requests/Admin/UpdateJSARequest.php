<?php
namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateJSARequest extends FormRequest
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
           'title' => 'required',
            //'phone' => 'required|regex:/(01)[0-9]{9}/',
            'address'=> 'required',
            'zip'=>'nullable|numeric'
        ];
    }
}
