<?php
namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAssociationsRequest extends FormRequest
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
            'phone' => 'required',
            'address'=> 'required',
            'zip' => 'nullable|numeric',
            'primary_contact_email'=>'nullable|email',
            'secondary_contact_email'=>'nullable|email',
            'alternate_contact_email'=>'nullable|email',
            'primary_contact_phone' => 'nullable|regex:/(01)[0-9]{9}/',
            'secondary_contact_phone' => 'nullable|regex:/(01)[0-9]{9}/',
            'alternate_contact_phone' => 'nullable|regex:/(01)[0-9]{9}/',
            'rebate' => 'nullable|numeric'
        ];
    }
}
