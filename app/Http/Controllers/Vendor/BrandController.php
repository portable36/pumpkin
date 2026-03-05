<?php

namespace App\Http\Controllers\Vendor;

/**
 * @author TechVillage <support@techvill.org>
 *
 * @contributor Ashraful Rasel <[ashraul.techvill@gmail.com]>
 *
 * @created 04-06-2025
 */

use App\DataTables\Vendor\VendorBrandListDataTable;
use App\Exports\BrandListExport;
use App\Models\Brand;
use Illuminate\Http\Request;
use Excel;
use App\Http\Controllers\Controller;
use App\Models\VendorBrand;

class BrandController extends Controller
{

    public function __construct()
    {
        if (!preference('vendor_brand') || !isActive('SaaS')) {
            abort(403);
        } 
    }
    /**
     * Brand List
     *
     * @return mixed
     */

    public function index(VendorBrandListDataTable $dataTable)
    {
        return $dataTable->render('vendor.brands.index');
    }

    /**
     * Brand create
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create()
    {
        return view('vendor.brands.create');
    }

    /**
     * Store Brand
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $response = $this->messageArray(__('Invalid Request'), 'fail');
        if ($request->isMethod('post')) {
            $request->merge(['vendor_id' => auth()->user()->vendor()->vendor_id, 'is_global' => 0]);
            $validator = Brand::storeValidation($request->all());
            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }
            $brandId = (new Brand())->store($request->only('name', 'description', 'status', 'vendor_id', 'is_global'));

            VendorBrand::store(['brand_id' => $brandId]);

            $response = $this->messageArray(__('The :x has been successfully saved.', ['x' => __('Brand')]), 'success');
        }
        $this->setSessionValue($response);

        return redirect()->route('vendor.brands.index');
    }

    /**
     * Edit Brand
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function edit($id)
    {
        $brand = Brand::getAll()->where('id', $id)->first();
        
        if ($brand->vendor_id != auth()->user()->vendor()->vendor_id) {
            abort(403);
        }
        if (empty($brand)) {
            $response = $this->messageArray(__(':x does not exist.', ['x' => __('Brand')]), 'fail');
            $this->setSessionValue($response);

            return redirect()->route('vendor.index');
        }
        $data['brands'] = $brand;

        return view('vendor.brands.edit', $data);
    }

    /**
     * Update Brand
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $response = $this->messageArray(__('Invalid Request'), 'fail');
        if ($request->isMethod('post')) {
            $result = $this->checkExistence($id, 'brands');

            $brand = Brand::where('id', $id)->first();
            if ($brand->is_global == 1) {
                return back()->with('fail', __('This brand is global brand. You can not update it.'));
            }

            if ($result['status'] === true) {
                $request->merge(['vendor_id' => auth()->user()->vendor()->vendor_id]);
                $validator = Brand::updateValidation($request->all(), $id);
                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                }
                if ((new Brand())->updateBrand($request->only('name', 'description', 'status', 'vendor_id'), $id)) {
                    $response = $this->messageArray(__('The :x has been successfully saved.', ['x' => __('Brand')]), 'success');
                }
            } else {
                $response['message'] = $result['message'];
            }
        }
        $this->setSessionValue($response);

        return redirect()->route('vendor.brands.index');
    }

    /**
     * Remove Brand
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, $id)
    {
        $response = $this->messageArray(__('Invalid Request'), 'fail');
        if ($request->isMethod('post')) {
            $result = $this->checkExistence($id, 'brands');
            if ($result['status'] === true) {
                $brand = Brand::where('id', $id)->first();
                if ($brand->is_global == 1) {
                    return back()->with('fail', __('This brand is global brand. You can not delete it.'));
                }
                $response = (new Brand())->remove($id);
            } else {
                $response['message'] = $result['message'];
            }
        }

        $this->setSessionValue($response);

        return redirect()->route('vendor.brands.index');
    }

    /**
     * Brand list pdf
     *
     * @return html static page
     */
    public function pdf()
    {
        $data['brands'] = getVendorProductBrands();

        return printPDF(
            $data,
            'brands_lists' . time() . '.pdf',
            'admin.brands.list_pdf',
            view('vendor.brands.list_pdf', $data),
            'pdf'
        );
    }

    /**
     * Brand list csv
     *
     * @return html static page
     */
    public function csv()
    {
        return Excel::download(new BrandListExport(), 'brands_lists' . time() . '.csv');
    }


     /**
     * suggestion
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function suggestion(Request $request)
    {

        $brand = Brand::whereLike('name', $request->name)->where('is_global', 1)->first();

        if (! empty($brand)) {

            $vendorBrand = VendorBrand::where(['brand_id' => $brand->id, 'vendor_id' => auth()->user()->vendor()->vendor_id])->first();

            if (! $vendorBrand) {
                return response()->json([
                    'status' => 1,
                    'name' => $brand->name,
                    'id' => $brand->id,
                ]);
            }
        }
        
        return response()->json([
            'status' => 0,
        ]);
    }

    /**
     * assign brand
     *
     * @return \Illuminate\Http\JsonResponse|void
     */
    public function assignBrand(Request $request)
    {

        $brand = Brand::getAll()->where('id', $request->brand_id)->first();

        if ($brand->is_global == 1) {
            $vendorBrand = VendorBrand::store(['brand_id' => $request->brand_id]);

            if (! empty($vendorBrand)) {
                return response()->json(['status' => 1]);
            }
        }

        return response()->json(['status' => 0]);
        
    }
}
