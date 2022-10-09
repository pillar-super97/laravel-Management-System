<?php
namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreStoresRequest extends FormRequest
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
            'number' => 'required|unique:stores,number',
            'name' => 'required|unique:stores,name',
            //'phone' => 'required|regex:/(01)[0-9]{9}/',
            'client_id' => 'required',
            'state_id' => 'required',
            'address'=> 'required',
            'zip'=>'nullable|numeric',
            'terms' => 'required',
//            'scheduling_contact_phone'=>'nullable|regex:/(01)[0-9]{9}/',
//            'sec_scheduling_contact_phone'=>'nullable|regex:/(01)[0-9]{9}/',
//            'billing_contact_phone'=>'nullable|regex:/(01)[0-9]{9}/',
            'billing' => 'required',
            'store_type' =>'required',
            'rate' => 'nullable|numeric',
            'rate_per' => 'nullable|numeric',
            'start_time' => 'nullable',
            'benchmark' => 'nullable|numeric',
            'frequency' => 'required',
            'spf' => 'required',
            'inv_type' => 'required',
            'alr_disk' => 'required',
            'inventory_level' => 'required',
            'count_stockroom' => 'required',
            'precall' => 'required',
            'qccall' => 'required',
            'pieces_or_dollars' => 'required',
            'fuel_center' => 'required'
        ];
    }
}
