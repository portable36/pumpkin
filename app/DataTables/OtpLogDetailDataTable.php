<?php

/**
 * @author TechVillage <support@techvill.org>
 *
 * @created 05-01-2026
 */

namespace App\DataTables;

use App\Filters\OtpLogFilter;
use App\Models\OtpLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class OtpLogDetailDataTable extends DataTable
{
    /**
     * Handle the AJAX request for OTP log details.
     *
     * This function queries OTP logs filtered by email/phone and returns the data
     * in a format suitable for DataTables to consume via AJAX.
     */
    public function ajax(): JsonResponse
    {
        $otpLogs = $this->query();

        $dt = datatables()
            ->of($otpLogs)
            ->editColumn('type', function ($otpLogs) {
                return ucfirst(str_replace('_', ' ', $otpLogs->type));
            })
            ->editColumn('channel', function ($otpLogs) {
                return ucfirst($otpLogs->channel);
            })
            ->editColumn('provider', function ($otpLogs) {
                return $otpLogs->provider ? ucfirst($otpLogs->provider) : '-';
            })
            ->editColumn('status', function ($otpLogs) {
                $statusLabels = [
                    'sent' => 'badge-mv-info',
                    'failed' => 'badge-mv-danger',
                    'verified' => 'badge-mv-success',
                    'expired' => 'badge-mv-warning',
                ];

                $class = $statusLabels[strtolower($otpLogs->status)] ?? 'badge-mv-secondary';
                return "<span class='badge $class f-12 f-w-600'>" . __(ucfirst($otpLogs->status)) . '</span>';
            })
            ->editColumn('created_at', function ($otpLogs) {
                return timeZoneFormatDate($otpLogs->created_at) . ' ' . timeZoneGetTime($otpLogs->created_at);
            })
            ->editColumn('error_message', function ($otpLogs) {
                if ($otpLogs->error_message) {
                    return '<span class="text-danger" title="' . htmlspecialchars($otpLogs->error_message) . '">
                        ' . htmlspecialchars($otpLogs->error_message) . '
                    </span>';
                }
                return '-';
            })
            ->addColumn('action', function ($otpLogs) {
                // Delete button using standard component
                return view('components.backend.datatable.delete-button', [
                    'route' => route('otp-logs.detail.delete', ['id' => $otpLogs->id]),
                    'id' => $otpLogs->id,
                    'method' => 'DELETE',
                ])->render();
            })
            ->rawColumns(['status', 'error_message', 'action']);

        return $dt->make(true);
    }

    /*
     * DataTable Query
     *
     * @return mixed
     */
    public function query()
    {
        $otpLogs = OtpLog::select([
            'otp_logs.id',
            'otp_logs.type',
            'otp_logs.channel',
            'otp_logs.provider',
            'otp_logs.email',
            'otp_logs.phone',
            'otp_logs.status',
            'otp_logs.ip_address',
            'otp_logs.error_message',
            'otp_logs.created_at',
        ]);

        // Filter by email or phone if provided
        if (request()->has('email') && !empty(request()->email)) {
            $otpLogs->where('email', request()->email);
        } elseif (request()->has('phone') && !empty(request()->phone)) {
            $phone = request()->phone;
            // Normalize phone: ensure it has '+' prefix for matching
            $normalizedPhone = $phone;
            if (!str_starts_with($normalizedPhone, '+')) {
                $normalizedPhone = '+' . ltrim($normalizedPhone, '+');
            }
            // Match phone with or without '+' prefix
            $otpLogs->where(function ($query) use ($phone, $normalizedPhone) {
                $query->where('phone', $phone)
                    ->orWhere('phone', $normalizedPhone)
                    ->orWhere('phone', ltrim($normalizedPhone, '+'));
            });
        }

        return $this->applyScopes($otpLogs->filter());
    }

    /*
     * DataTable HTML
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        $builder = $this->builder()
            ->addColumn(['data' => 'id', 'name' => 'id', 'title' => __('ID'), 'width' => '5%', 'className' => 'align-middle'])
            ->addColumn(['data' => 'type', 'name' => 'type', 'title' => __('Type'), 'width' => '10%', 'className' => 'align-middle'])
            ->addColumn(['data' => 'channel', 'name' => 'channel', 'title' => __('Channel'), 'width' => '8%', 'className' => 'align-middle'])
            ->addColumn(['data' => 'provider', 'name' => 'provider', 'title' => __('Provider'), 'width' => '10%', 'className' => 'align-middle'])
            ->addColumn(['data' => 'status', 'name' => 'status', 'title' => __('Status'), 'width' => '10%', 'orderable' => false, 'className' => 'text-right align-middle'])
            ->addColumn(['data' => 'ip_address', 'name' => 'ip_address', 'title' => __('IP Address'), 'width' => '10%', 'className' => 'align-middle'])
            ->addColumn(['data' => 'error_message', 'name' => 'error_message', 'title' => __('Error Message'), 'width' => '20%', 'orderable' => false, 'className' => 'align-middle'])
            ->addColumn(['data' => 'created_at', 'name' => 'created_at', 'title' => __('Sent At'), 'width' => '10%', 'className' => 'align-middle'])
            ->addColumn(['data' => 'action', 'name' => 'action', 'title' => __('Action'), 'width' => '5%', 'orderable' => false, 'searchable' => false, 'className' => 'text-right align-middle'])
            ->parameters(dataTableOptions([
                'dom' => 'Bfrtip',
            ]));

        return $builder;
    }

    /**
     * Set view data
     *
     * @return void
     */
    public function setViewData()
    {
        $statusCounts = DB::table('otp_logs')
            ->when(request()->has('email') && !empty(request()->email), function ($query) {
                $query->where('email', request()->email);
            })
            ->when(request()->has('phone') && !empty(request()->phone), function ($query) {
                $phone = request()->phone;
                // Normalize phone: ensure it has '+' prefix for matching
                $normalizedPhone = $phone;
                if (!str_starts_with($normalizedPhone, '+')) {
                    $normalizedPhone = '+' . ltrim($normalizedPhone, '+');
                }
                // Match phone with or without '+' prefix
                $query->where(function ($q) use ($phone, $normalizedPhone) {
                    $q->where('phone', $phone)
                        ->orWhere('phone', $normalizedPhone)
                        ->orWhere('phone', ltrim($normalizedPhone, '+'));
                });
            })
            ->selectRaw('status, COUNT(id) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $this->data['groups'] = ['All' => array_sum($statusCounts)] + $statusCounts;
    }
}

