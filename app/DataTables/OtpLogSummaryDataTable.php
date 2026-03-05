<?php

/**
 * @author TechVillage <support@techvill.org>
 *
 * @created 05-01-2026
 */

namespace App\DataTables;

use App\Models\OtpLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class OtpLogSummaryDataTable extends DataTable
{
    /**
     * Handle the AJAX request for OTP log summaries.
     *
     * This function queries OTP logs grouped by email/phone and returns the data
     * in a format suitable for DataTables to consume via AJAX.
     */
    public function ajax(): JsonResponse
    {
        $summaries = $this->query();

        $dt = datatables()
            ->of($summaries)
            ->addColumn('contact', function ($summary) {
                return $summary->email ?? $summary->phone ?? '-';
            })
            ->editColumn('provider', function ($summary) {
                return $summary->provider ? ucfirst($summary->provider) : '-';
            })
            ->editColumn('channel', function ($summary) {
                if (!$summary->channel) {
                    return '-';
                }
                $channelLabels = [
                    'email' => 'badge-mv-info',
                    'sms' => 'badge-mv-success',
                ];
                $class = $channelLabels[strtolower($summary->channel)] ?? 'badge-mv-secondary';
                return '<span class="badge ' . $class . '">' . ucfirst($summary->channel) . '</span>';
            })
            ->addColumn('users', function ($summary) {
                // Use precomputed user_names from the main query (format: "id1:name1|id2:name2")
                if (empty($summary->user_names)) {
                    return '-';
                }
                
                // Parse the user_names string (format: "id:name|id:name")
                $userParts = explode('|', $summary->user_names);
                
                if (empty($userParts)) {
                    return '-';
                }
                
                $html = '';
                foreach ($userParts as $userPart) {
                    // Split each part by ':' to get id and name
                    $parts = explode(':', $userPart, 2);
                    if (count($parts) === 2) {
                        [$userId, $userName] = $parts;
                        $html .= '<a href="' . route('users.edit', ['id' => $userId]) . '" class="d-block mb-1">' . htmlspecialchars($userName) . '</a>';
                    }
                }
                
                return $html ?: '-';
            })
            ->editColumn('total_count', function ($summary) {
                return '<strong>' . $summary->total_count . '</strong>';
            })
            ->editColumn('last_sent_at', function ($summary) {
                return timeZoneFormatDate($summary->last_sent_at) . ' ' . timeZoneGetTime($summary->last_sent_at);
            })
            ->addColumn('action', function ($summary) {
                $html = '';
                
                // View details link
                $identifier = $summary->email ? 'email=' . urlencode($summary->email) : 'phone=' . urlencode($summary->phone);
                $html .= '<a title="' . __('View Details') . '" href="' . route('otp-logs.detail', $identifier) . '" class="action-icon">
                    <i class="feather icon-eye"></i>
                </a>';
                
                // Delete button - using custom approach for summary (email/phone based)
                $identifier = $summary->email ? 'email-' . md5($summary->email) : 'phone-' . md5($summary->phone);
                $html .= '<form method="post" action="' . route('otp-logs.summary.delete') . '" id="delete-data-' . $identifier . '" accept-charset="UTF-8" class="display_inline">
                    ' . csrf_field() . '
                    <input type="hidden" name="email" value="' . htmlspecialchars($summary->email ?? '') . '">
                    <input type="hidden" name="phone" value="' . htmlspecialchars($summary->phone ?? '') . '">
                    <a title="' . __('Delete All') . '" class="action-icon confirm-delete" type="button" data-id="' . $identifier . '" data-delete="data" data-label="Delete" data-bs-toggle="modal" data-bs-target="#confirmDelete">
                        <i class="feather icon-trash"></i>
                    </a>
                </form>';
                
                return $html;
            })
            ->rawColumns(['contact', 'provider', 'channel', 'users', 'total_count', 'action']);

        return $dt->make(true);
    }

    /*
     * DataTable Query
     *
     * @return mixed
     */
    public function query()
    {
        // Validate and normalize channel filter
        $channel = null;
        if (request()->has('channel') && !empty(request()->channel) && strtolower(request()->channel) !== 'all') {
            $requestedChannel = strtolower(request()->channel);
            // Whitelist allowed channel values to prevent SQL injection
            $allowedChannels = ['email', 'sms'];
            if (in_array($requestedChannel, $allowedChannels, true)) {
                $channel = $requestedChannel;
            }
        }

        // Validate and parse date filters
        $startDate = null;
        $endDate = null;
        if (request()->has('start_date') && request()->start_date != 'null' && !empty(request()->start_date)) {
            $startDate = DbDateFormat(request()->start_date);
        }
        if (request()->has('end_date') && request()->end_date != 'null' && !empty(request()->end_date)) {
            $endDate = DbDateFormat(request()->end_date);
        }

        // Build channel filter conditions with parameterized bindings
        $channelWhere = '';
        $channelWhereOl3 = '';
        $channelWhereOl4 = '';
        $channelWhereMain = '';
        $channelBindings = [];
        if ($channel !== null) {
            $channelWhere = 'AND ol2.channel = ?';
            $channelWhereOl3 = 'AND ol3.channel = ?';
            $channelWhereOl4 = 'AND ol4.channel = ?';
            $channelWhereMain = 'AND otp_logs.channel = ?';
            $channelBindings = [$channel, $channel, $channel, $channel];
        }

        // Build date range filter conditions with parameterized bindings
        $dateWhere = '';
        $dateWhereOl3 = '';
        $dateWhereOl4 = '';
        $dateWhereMain = '';
        $dateBindings = [];
        if ($startDate !== null) {
            $dateWhere .= 'AND DATE(ol2.created_at) >= ?';
            $dateWhereOl3 .= 'AND DATE(ol3.created_at) >= ?';
            $dateWhereOl4 .= 'AND DATE(ol4.created_at) >= ?';
            $dateWhereMain .= 'AND DATE(otp_logs.created_at) >= ?';
            $dateBindings = array_merge($dateBindings, [$startDate, $startDate, $startDate, $startDate]);
        }
        if ($endDate !== null) {
            $dateWhere .= 'AND DATE(ol2.created_at) <= ?';
            $dateWhereOl3 .= 'AND DATE(ol3.created_at) <= ?';
            $dateWhereOl4 .= 'AND DATE(ol4.created_at) <= ?';
            $dateWhereMain .= 'AND DATE(otp_logs.created_at) <= ?';
            $dateBindings = array_merge($dateBindings, [$endDate, $endDate, $endDate, $endDate]);
        }

        // Build bindings array in the order they appear in the SQL query
        $bindings = [];
        
        // First subquery (provider - ol2): channel, startDate, endDate
        if ($channel !== null) {
            $bindings[] = $channel;
        }
        if ($startDate !== null) {
            $bindings[] = $startDate;
        }
        if ($endDate !== null) {
            $bindings[] = $endDate;
        }
        
        // Second subquery (channel - ol3): channel, startDate, endDate
        if ($channel !== null) {
            $bindings[] = $channel;
        }
        if ($startDate !== null) {
            $bindings[] = $startDate;
        }
        if ($endDate !== null) {
            $bindings[] = $endDate;
        }
        
        // Third subquery (user_names - ol4): channel, startDate, endDate
        if ($channel !== null) {
            $bindings[] = $channel;
        }
        if ($startDate !== null) {
            $bindings[] = $startDate;
        }
        if ($endDate !== null) {
            $bindings[] = $endDate;
        }
        
        // Main query: channel, startDate, endDate
        if ($channel !== null) {
            $bindings[] = $channel;
        }
        if ($startDate !== null) {
            $bindings[] = $startDate;
        }
        if ($endDate !== null) {
            $bindings[] = $endDate;
        }

        // Build the raw SQL query with parameterized placeholders
        $sql = "(
            SELECT 
                COALESCE(email, phone) as identifier,
                MAX(email) as email,
                MAX(phone) as phone,
                (
                    SELECT provider 
                    FROM otp_logs ol2 
                    WHERE COALESCE(ol2.email, ol2.phone) = COALESCE(otp_logs.email, otp_logs.phone)
                    AND ol2.provider IS NOT NULL
                    {$channelWhere}
                    {$dateWhere}
                    GROUP BY provider
                    ORDER BY COUNT(*) DESC
                    LIMIT 1
                ) as provider,
                (
                    SELECT channel 
                    FROM otp_logs ol3 
                    WHERE COALESCE(ol3.email, ol3.phone) = COALESCE(otp_logs.email, otp_logs.phone)
                    AND ol3.channel IS NOT NULL
                    {$channelWhereOl3}
                    {$dateWhereOl3}
                    GROUP BY channel
                    ORDER BY COUNT(*) DESC
                    LIMIT 1
                ) as channel,
                (
                    SELECT GROUP_CONCAT(DISTINCT CONCAT(u.id, ':', u.name) SEPARATOR '|')
                    FROM otp_logs ol4
                    INNER JOIN users u ON ol4.user_id = u.id
                    WHERE COALESCE(ol4.email, ol4.phone) = COALESCE(otp_logs.email, otp_logs.phone)
                    AND ol4.user_id IS NOT NULL
                    {$channelWhereOl4}
                    {$dateWhereOl4}
                ) as user_names,
                COUNT(*) as total_count,
                MAX(created_at) as last_sent_at
            FROM otp_logs
            WHERE ((email IS NOT NULL AND email != '') OR (phone IS NOT NULL AND phone != ''))
            {$channelWhereMain}
            {$dateWhereMain}
            GROUP BY COALESCE(email, phone)
            HAVING (MAX(email) IS NOT NULL AND MAX(email) != '') OR (MAX(phone) IS NOT NULL AND MAX(phone) != '')
        ) as summaries";

        // Use a subquery approach that works with DataTable filtering
        // Build query with parameterized bindings to prevent SQL injection
        // Use DB::table() with raw SQL and set bindings manually
        $summaries = DB::table(DB::raw($sql));
        
        // Apply parameterized bindings to prevent SQL injection
        // The bindings array is ordered to match the ? placeholders in the SQL
        if (!empty($bindings)) {
            $query = $summaries->getQuery();
            // Set bindings for the select part of the query
            // These will be used when the query is executed by the database
            $query->setBindings($bindings, 'select');
        }
        
        $summaries->where(function ($query) {
            $query->where(function ($q) {
                $q->whereNotNull('email')
                  ->where('email', '!=', '');
            })->orWhere(function ($q) {
                $q->whereNotNull('phone')
                  ->where('phone', '!=', '');
            });
        });

        return $this->applyScopes($summaries);
    }

    /*
     * DataTable HTML
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        $builder = $this->builder()
            ->addColumn(['data' => 'contact', 'name' => 'contact', 'title' => __('Contact'), 'width' => '18%', 'orderable' => false, 'searchable' => true, 'className' => 'align-middle'])
            ->addColumn(['data' => 'email', 'name' => 'email', 'title' => '', 'visible' => false, 'searchable' => true])
            ->addColumn(['data' => 'phone', 'name' => 'phone', 'title' => '', 'visible' => false, 'searchable' => true])
            ->addColumn(['data' => 'user_names', 'name' => 'user_names', 'title' => '', 'visible' => false, 'searchable' => true])
            ->addColumn(['data' => 'channel', 'name' => 'channel', 'title' => __('Channel'), 'width' => '10%', 'className' => 'align-middle'])
            ->addColumn(['data' => 'provider', 'name' => 'provider', 'title' => __('Provider'), 'width' => '12%', 'className' => 'align-middle'])
            ->addColumn(['data' => 'users', 'name' => 'users', 'title' => __('Users'), 'width' => '18%', 'orderable' => false, 'searchable' => false, 'className' => 'align-middle'])
            ->addColumn(['data' => 'total_count', 'name' => 'total_count', 'title' => __('Total Sent'), 'width' => '10%', 'className' => 'text-center align-middle'])
            ->addColumn(['data' => 'last_sent_at', 'name' => 'last_sent_at', 'title' => __('Last Sent At'), 'width' => '15%', 'className' => 'align-middle'])
            ->addColumn(['data' => 'action', 'name' => 'action', 'title' => __('Action'), 'width' => '10%', 'orderable' => false, 'searchable' => false, 'className' => 'text-right align-middle'])
            ->parameters(dataTableOptions([
                'dom' => 'Bfrtip',
                'order' => [8, 'desc'], // Order by last_sent_at (column index 8: contact=0, email=1, phone=2, user_names=3, channel=4, provider=5, users=6, total_count=7, last_sent_at=8)
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
        // Count channels (email, sms) from otp_logs
        $channelCounts = DB::table('otp_logs')
            ->selectRaw('channel, COUNT(DISTINCT COALESCE(email, phone)) as count')
            ->whereNotNull('channel')
            ->where(function ($query) {
                $query->where(function ($q) {
                    $q->whereNotNull('email')
                      ->where('email', '!=', '');
                })->orWhere(function ($q) {
                    $q->whereNotNull('phone')
                      ->where('phone', '!=', '');
                });
            })
            ->groupBy('channel')
            ->pluck('count', 'channel')
            ->toArray();

        // Format channel names
        $formattedCounts = [];
        foreach ($channelCounts as $channel => $count) {
            $formattedCounts[ucfirst($channel)] = $count;
        }

        $this->data['groups'] = ['All' => array_sum($channelCounts)] + $formattedCounts;
    }
}

