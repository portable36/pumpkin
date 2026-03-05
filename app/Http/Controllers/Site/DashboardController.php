<?php

/**
 * @author TechVillage <support@techvill.org>
 *
 * @contributor Sakawat Hossain Rony <[sakawat.techvill@gmail.com]>
 *
 * @created 21-11-2021
 */

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Cache;
use Modules\CMS\Http\Models\ThemeOption;

class DashboardController extends Controller
{
    public function index()
    {
        return view('site.myaccount.overview');
    }

    /**
     * Set language preference (without echo/exit)
     *
     * @param  Request  $request
     * @return bool
     */
    public function setLanguagePreference(Request $request): bool
    {
        if ($request->lang) {
            Cache::forget('theme_options');
            ThemeOption::forgetCache();

            if (Auth::check()) {
                Cache::put(config('cache.prefix') . '-user-language-' . Auth::guard('user')->user()->id, $request->lang, 5 * 365 * 86400);
                return true;
            } else {
                Cache::put(config('cache.prefix') . '-guest-language-' . request()->server('HTTP_USER_AGENT'), $request->lang, 5 * 365 * 86400);
                return true;
            }
        }
        return false;
    }

    /**
     * Change Language function
     *
     * @return true or false
     */
    public function switchLanguage(Request $request)
    {
        $result = $this->setLanguagePreference($request);
        echo $result ? 1 : 0;
        exit();
    }

    /**
     * change currency
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function switchCurrency(Request $request)
    {
        if ($request->currency_id) {
            if (! empty(Auth::user()->id) && isset(Auth::guard('user')->user()->id)) {
                Cache::put(config('cache.prefix') . '-user-multi_currency-' . Auth::guard('user')->user()->id, $request->currency_id, 5 * 365 * 86400);

                return response()->json([
                    'status' => 1,
                ]);
            } else {
                Cache::put(config('cache.prefix') . '-guest-multi_currency-' . request()->server('HTTP_USER_AGENT'), $request->currency_id, 5 * 365 * 86400);

                return response()->json([
                    'status' => 1,
                ]);
            }
        }

        return response()->json([
            'status' => 'info',
            'message' => __('Please select a currency first.'),
        ]);
    }
}
