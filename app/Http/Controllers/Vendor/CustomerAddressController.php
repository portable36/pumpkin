<?php

namespace App\Http\Controllers\Vendor;

use Exception;
use App\DataTables\VendorAddressDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Vendor\Address\StoreAddressRequest;
use App\Http\Requests\Vendor\Address\UpdateAddressRequest;
use App\Models\Customer;
use App\Models\CustomerAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerAddressController extends Controller
{
    public function index(VendorAddressDataTable $dataTable)
    {
        $data = [
            'customer' => Customer::findOrFail(request()->customer),
        ];

        return $dataTable->render('vendor.customer-addresses.index', $data);
    }

    /**
     * Create
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function create($customerId)
    {
        if (empty(request()->customer)) {
            $this->setSessionValue(['status' => 'fail', 'message' => __('Invalid Request')]);

            return redirect()->route('vendor.customer');
        }

        $data['countries'] = \Modules\GeoLocale\Entities\Country::select('id', 'name', 'code')->orderBy('name')->get();

        $data['customerId'] = $customerId;

        return view('vendor.customer-addresses.create', $data);
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

        $request['vendor_id'] = session('vendorId') ?? auth()->user()->vendor()->vendor_id;

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

        return redirect()->route('vendor.customer.addresses', ['customer' => $request->customer_id]);
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

        return view('vendor.customer-addresses.edit', $data);
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

        return redirect()->route('vendor.customer.addresses', ['customer' => $address->customer_id]);
    }

    public function destroy(CustomerAddress $address)
    {
        $data = ['status' => 'fail'];
        $vendorId = session('vendorId') ?? auth()->user()->vendor()->vendor_id;

        if ($address->vendor_id != $vendorId) {
            $data['message'] = __('You are not authorized to delete this customer address.');
            $this->setSessionValue($data);

            return back();
        }

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
