<?php
namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreClientsRequest extends FormRequest
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
            'cust_no' => 'required|unique:clients,cust_no',
           // 'cust_login_name' => 'required',
          //  'cust_login_email'=>'required|email|unique:users,email',
            // 'cust_login_password' => 'required|same:cust_confirm_password',  
            // 'cust_confirm_password' => 'required',


            'name' => 'required',
           
            //'phone' => 'required|regex:/(01)[0-9]{9}/',
            'scheduling_contact_email'=>'nullable|email',
            'billing_contact_email'=>'nullable|email',
            'sec_scheduling_contact_email'=>'nullable|email',
            'address'=> 'required',
            'zip'=>'nullable|numeric',
//            //'scheduling_contact_phone'=>'nullable',
//            'sec_scheduling_contact_phone'=>'nullable|regex:/(01)[0-9]{9}/',
//            'billing_contact_phone'=>'nullable|regex:/(01)[0-9]{9}/',
            'rate' => 'nullable|numeric'
        ];
    }
}
