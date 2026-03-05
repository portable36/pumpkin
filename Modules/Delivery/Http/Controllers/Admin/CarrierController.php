<?php

namespace Modules\Delivery\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\RoleUser;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Modules\Delivery\DataTables\CarriersDataTable;
use Modules\Delivery\Entities\{
    DeliveryMan,
};
use Modules\Delivery\Http\Requests\Admin\StoreCarrierRequest;
use Modules\Delivery\Http\Requests\Admin\UpdateCarrierRequest;
use Modules\Delivery\Services\Mail\RegisterCarrierMailService;

class CarrierController extends Controller
{
    /**
     * Show all carrier
     */
    public function index(CarriersDataTable $dataTable): mixed
    {
        return $dataTable->render('delivery::admin.carrier.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('delivery::admin.carrier.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCarrierRequest $request, RegisterCarrierMailService $mailService): mixed
    {
        $userData = array_merge($request->only('name', 'email', 'gender', 'phone', 'address'), [
            'password' => Hash::make($request->password),
            'status' => 'Active',
        ]);

        try {
            DB::beginTransaction();
            $userId = User::insertGetId($userData);
            $role = Role::where('slug', 'delivery-man')->first();

            (new RoleUser())->store([
                'user_id' => $userId,
                'role_id' => $role->id,
            ]);

            $carrierData['metaData'] = [];
            $carrierData['essentialData'] = [
                'user_id' => $userId,
                'unique_id' => DeliveryMan::getUniqueCarrierId(),
                'license_status' => $request->license_status,
                'is_active' => $request->is_active,
            ];

            if (request()->has('file_id') && is_array(request()->file_id)) {
                $carrierData['metaData']['driving_license_photo'] = $request->file_id['0'];
            }

            (new DeliveryMan())->store($carrierData);

            DB::commit();
            $response = $this->messageArray(__('The :x has been successfully created.', ['x' => __('Delivery Man')]), 'success');
        } catch (\Exception $e) {
            DB::rollBack();
            $response = $this->messageArray($e->getMessage(), 'fail');
        }

        if ($request->send_mail) {
            $mailService->send($request);
        }
        $this->setSessionValue($response);

        return to_route('admin.delivery.carrier.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $carrier = DeliveryMan::where('id', $id)->first();

        return view('delivery::admin.carrier.edit', compact('carrier'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCarrierRequest $request, $id): RedirectResponse
    {
        try {
            DB::beginTransaction();

            $carrier = DeliveryMan::findOrFail($id);
            $userId = $carrier?->user?->id;
            $userData = $request->only('name', 'email', 'phone', 'gender', 'address');
            User::findOrFail($userId)->update($userData);

            $carrierData['metaData'] = [];
            $carrierData['essentialData'] = [
                'license_status' => $request->license_status,
                'is_active' => $request->is_active,
            ];

            $carrierData['metaData']['driving_license_photo'] = null;
            if (request()->has('file_id') && is_array(request()->file_id)) {
                $carrierData['metaData']['driving_license_photo'] = $request->file_id['0'];
            }

            $carrier->updateCarrier($carrierData);

            DB::commit();
            $response = ['status' => 'success', 'message' => __('The :x information has been successfully saved.', ['x' => __('Delivery Man')])];
        } catch (\Exception $e) {
            DB::rollBack();
            $response = ['status' => 'fail', 'message' => $e->getMessage()];
        }

        $this->setSessionValue($response);

        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            DeliveryMan::clearFootprints($id);
            $deliveryMan = DeliveryMan::destroy($id);

            if (! $deliveryMan) {
                throw new \Exception(__('Something went wrong. please try again.'));
            }

            DB::commit();
            $response = ['status' => 'success', 'message' => __('The :x has been successfully deleted.', ['x' => __('Delivery Man')])];
        } catch (\Exception $e) {
            DB::rollBack();
            $response = ['status' => 'fail', 'message' => __('Something went wrong. please try again.')];
        }

        $this->setSessionValue($response);

        return to_route('admin.delivery.carrier.index');
    }
}
