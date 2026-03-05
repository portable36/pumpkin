<?php

namespace Modules\Delivery\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Modules\Delivery\Http\Resources\EarningResource;
use Modules\Delivery\Http\Resources\WalletResource;

class WalletController extends Controller
{
    /**
     * delivery man earning history
     */
    public function earning(Request $request)
    {
        $configs = $this->initialize([], $request->all());
        $userId = optional(auth()->guard('api')->user())->id;

        $earing = Transaction::where('reference_number', $userId)->where('transaction_type', 'delivery_commission')
            ->where('reference_type', 'delivery_man_user_id')
            ->groupBy('reference_number', 'order_id')
            ->selectRaw('SUM(total_amount) as total_amount, id, reference_number, order_id, transaction_date, created_at')
            ->with('order');

        $startDate = isset($request->startDate) ? $request->startDate : null;
        $endDate = isset($request->endDate) ? $request->endDate : null;

        if (! empty($startDate) && ! empty($endDate)) {
            $earing->whereBetween('transaction_date', [$startDate, $endDate]);
        }

        $earing->orderBy('transaction_date', 'desc');

        return $this->response([
            'data' => EarningResource::collection($earing->paginate($configs['rows_per_page'])),
            'pagination' => $this->toArray($earing->paginate($configs['rows_per_page'])->appends($request->all())),
        ]);
    }

    public function wallet()
    {
        $userId = optional(auth()->guard('api')->user())->id;
        $data['user'] = User::with('deliveryMan.orders', 'transactions')
            ->where('id', $userId)
            ->first();

        $data['total_earned'] = Transaction::where('reference_number', $userId)->where('transaction_type', 'delivery_commission')
            ->where('reference_type', 'delivery_man_user_id')->sum('total_amount');

        return $this->response([
            'data' => new WalletResource($data),
        ]);
    }
}
