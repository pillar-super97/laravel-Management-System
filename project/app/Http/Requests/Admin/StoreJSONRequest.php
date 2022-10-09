<?php
namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreJSONRequest extends FormRequest
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
            'data' => 'bail|required|file|mimetypes:application/json,text/plain, application/zip|max:8192'
        ];
    }


    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'data.required' => 'This field is required',
            'data.mimetypes' => 'Incorrect file format',
            'data.max' => 'The file size must be less than 8MB'
        ];
    }
}
