<?php

namespace App\Http\Controllers;

use Exception;
use App\DataTables\AdminAddressDataTable;
use App\Http\Requests\Admin\Address\StoreAddressRequest;
use App\Http\Requests\Admin\Address\UpdateAddressRequest;
use App\Models\Customer;
use App\Models\CustomerAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerAddressController extends Controller
{
    public function index(AdminAddressDataTable $dataTable, $customer = null)
    {

        $data = [
            'customer' => Customer::findOrFail($customer),
            'from' => 'admin',
        ];

        return $dataTable->render('admin.customer-addresses.index', $data);
    }

    /**
     * Create
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function create()
    {
        if (empty(request()->customer)) {
            $this->setSessionValue(['status' => 'fail', 'message' => __('Invalid Request')]);

            return redirect()->route('customers.index');
        }

        $data['customer'] = Customer::findOrFail(request()->customer);
        $data['countries'] = \Modules\GeoLocale\Entities\Country::select('id', 'name', 'code')->orderBy('name')->get();

        return view('admin.customer-addresses.create', $data);
    }

    /**
     * Store
     *
     * @param  Request  $request  [description]
     * @return \Illuminate\Routing\Redirector
     */
    public function store(StoreAddressRequest $request)
    {
        $data = ['status' => 'fail', 'message' => __('Invalid Request')];

        try {
            DB::beginTransaction();

            (new CustomerAddress())->storeCustomerAddress($request->all());

            $data['status'] = 'success';
            $data['message'] = __('The :x has been successfully saved.', ['x' => __('Customer Address')]);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            $data['message'] = $e->getMessage();
        }

        $this->setSessionValue($data);

        return redirect()->route('customer.addresses.index', ['customer' => $request->customer_id]);
    }

    /**
     * Edit a customer
     *
     * @param  int  $id  The customer ID
     * @return \Illuminate\Contracts\View\View
     */
    public function edit(CustomerAddress $address)
    {
        $data = (new \App\Services\CustomerService())->getCustomerAddressData($address);

        return view('admin.customer-addresses.edit', $data);
    }

    /**
     * Update a customer
     *
     * @param  Request  $request  The request object
     * @param  int  $id  The customer ID
     * @return \Illuminate\Routing\Redirector
     */
    public function update(UpdateAddressRequest $request, CustomerAddress $address)
    {
        $data = ['status' => 'fail', 'message' => __('Invalid Request')];

        try {
            DB::beginTransaction();

            (new CustomerAddress())->updateCustomerAddress($request->all(), $address);

            $data['status'] = 'success';
            $data['message'] = __('The :x has been successfully saved.', ['x' => __('Customer Info')]);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            $data['message'] = $e->getMessage();
        }

        $this->setSessionValue($data);

        return redirect()->route('customer.addresses.index', ['customer' => $address->customer_id]);
    }

    public function destroy(CustomerAddress $address)
    {
        $data = ['status' => 'fail'];

        try {
            DB::beginTransaction();

            $address->delete();

            $data['status'] = 'success';
            $data['message'] = __('The :x has been successfully deleted.', ['x' => __('Customer address')]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            $data['message'] = $e->getMessage();
        }

        $this->setSessionValue($data);

        return back();
    }
}
