<?php

namespace Modules\Delivery\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Excel;
use Modules\Delivery\DataTables\WithdrawalListDataTable;
use Modules\Delivery\Exports\WithdrawalListExport;

class WithdrawalController extends Controller
{
    /**
     * Withdrawal List
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index(WithdrawalListDataTable $dataTable)
    {
        return $dataTable->render('delivery::admin.withdrawal.index');
    }

    /**
     * Withdrawal edit
     *
     * @param  Request  $request
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View
     */
    public function edit($id)
    {
        $data['withdrawal'] = Transaction::getAll()->where('id', $id)->first();

        return view('delivery::admin.withdrawal.edit', $data);
    }

    /**
     * Withdrawal update
     *
     * @param  int  $id
     * @return redirect view
     */
    public function update(Request $request, $id)
    {
        $validator = Transaction::updateValidation($request->all());
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        $response = (new Transaction())->updateData($request->only('status'), $id);

        $response['message'] = $response['status'] == 'success' ? __('The :x has been successfully saved.', ['x' => __('Withdrawal')]) : $response['message'];

        $this->setSessionValue($response);

        return redirect()->route('admin.delivery.withdrawal.index');
    }

    /**
     * Withdrawal list pdf
     *
     * @return html static page
     */
    public function pdf()
    {
        $data['transactions'] = Transaction::where('transaction_type', 'Delivery_withdrawal')->get();

        return printPDF(
            $data,
            'withdrawal_list' . time() . '.pdf',
            'delivery::admin.withdrawal.pdf',
            view('delivery::admin.withdrawal.pdf', $data),
            'pdf'
        );
    }

    /**
     * Withdrawal list csv
     *
     * @return html static page
     */
    public function csv()
    {
        return Excel::download(new WithdrawalListExport(), 'withdrawal' . time() . '.csv');
    }
}
