<?php

/**
 * @author TechVillage <support@techvill.org>
 *
 * @created 05-01-2026
 */

namespace App\Http\Controllers;

use App\DataTables\OtpLogSummaryDataTable;
use App\DataTables\OtpLogDetailDataTable;
use App\Models\OtpLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OtpLogController extends Controller
{
    /**
     * Get phone variants for matching (original, normalized with '+', and without '+')
     *
     * @param  string  $phone
     * @return array
     */
    private function getPhoneVariants(string $phone): array
    {
        $variants = [$phone]; // Original phone
        
        // Normalize phone: ensure it has '+' prefix for matching
        $normalizedPhone = $phone;
        if (!str_starts_with($normalizedPhone, '+')) {
            $normalizedPhone = '+' . ltrim($normalizedPhone, '+');
        }
        
        // Add normalized variants
        $variants[] = $normalizedPhone; // With '+'
        $variants[] = ltrim($normalizedPhone, '+'); // Without '+'
        
        // Remove duplicates and return
        return array_unique($variants);
    }

    /**
     * Display a listing of OTP log summaries (grouped by email/phone).
     *
     * @param  OtpLogSummaryDataTable  $dataTable
     * @return mixed
     */
    public function index(OtpLogSummaryDataTable $dataTable, Request $request)
    {
        $from = $request->get('from');
        $to = $request->get('to');
        
        return $dataTable->render('admin.otp_logs.index', [
            'from' => $from,
            'to' => $to,
        ]);
    }

    /**
     * Display detailed OTP logs for a specific email or phone.
     *
     * @param  OtpLogDetailDataTable  $dataTable
     * @param  Request  $request
     * @return mixed
     */
    public function detail(OtpLogDetailDataTable $dataTable, Request $request)
    {
        $email = $request->get('email');
        $phone = $request->get('phone');

        if (empty($email) && empty($phone)) {
            return redirect()->route('otp-logs.index')
                ->withErrors(['message' => __('Email or phone number is required.')]);
        }

        // Get contact info for display
        $contact = $email ?: $phone;
        $contactType = $email ? 'Email' : 'SMS';

        // Get user information from the first OTP log for this contact
        $user = null;
        $otpLog = OtpLog::where(function ($query) use ($email, $phone) {
            if ($email) {
                $query->where('email', $email);
            } else {
                // Use phone variants for matching
                $variants = $this->getPhoneVariants($phone);
                $query->whereIn('phone', $variants);
            }
        })
        ->whereNotNull('user_id')
        ->with('user')
        ->first();

        if ($otpLog && $otpLog->user) {
            $user = $otpLog->user;
        }

        $from = $request->get('from');
        $to = $request->get('to');
        
        return $dataTable->render('admin.otp_logs.detail', [
            'contact' => $contact,
            'contactType' => $contactType,
            'email' => $email,
            'phone' => $phone,
            'user' => $user,
            'from' => $from,
            'to' => $to,
        ]);
    }

    /**
     * Delete all OTP logs for a specific email or phone.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function deleteSummary(Request $request)
    {
        $email = $request->input('email');
        $phone = $request->input('phone');

        if (empty($email) && empty($phone)) {
            if ($request->ajax()) {
                return response()->json([
                    'status' => 'fail',
                    'message' => __('Email or phone number is required.'),
                ]);
            }
            return redirect()->route('otp-logs.index')
                ->withErrors(['message' => __('Email or phone number is required.')]);
        }

        try {
            $query = OtpLog::query();
            
            if (!empty($email)) {
                $query->where('email', $email);
            } else {
                // Use phone variants for matching
                $variants = $this->getPhoneVariants($phone);
                $query->whereIn('phone', $variants);
            }

            $deletedCount = $query->delete();

            if ($request->ajax()) {
                return response()->json([
                    'status' => 'success',
                    'message' => __('Successfully deleted :count OTP log(s).', ['count' => $deletedCount]),
                ]);
            }

            return redirect()->route('otp-logs.index')
                ->withSuccess(__('Successfully deleted :count OTP log(s).', ['count' => $deletedCount]));
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'status' => 'fail',
                    'message' => __('Failed to delete OTP logs: :error', ['error' => $e->getMessage()]),
                ]);
            }
            return redirect()->route('otp-logs.index')
                ->withErrors(['message' => __('Failed to delete OTP logs: :error', ['error' => $e->getMessage()])]);
        }
    }

    /**
     * Delete a single OTP log.
     *
     * @param  int  $id
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function deleteDetail($id, Request $request)
    {
        try {
            $otpLog = OtpLog::findOrFail($id);
            $otpLog->delete();

            if ($request->ajax()) {
                return response()->json([
                    'status' => 'success',
                    'message' => __('Successfully deleted OTP log.'),
                ]);
            }

            // Get email/phone for redirect
            $email = $request->input('email');
            $phone = $request->input('phone');

            // Redirect back to detail page if email/phone provided, otherwise to summary
            if (!empty($email) || !empty($phone)) {
                $redirectUrl = route('otp-logs.detail');
                if (!empty($email)) {
                    $redirectUrl .= '?email=' . urlencode($email);
                } else {
                    $redirectUrl .= '?phone=' . urlencode($phone);
                }
                return redirect($redirectUrl)
                    ->withSuccess(__('Successfully deleted OTP log.'));
            }

            return redirect()->route('otp-logs.index')
                ->withSuccess(__('Successfully deleted OTP log.'));
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'status' => 'fail',
                    'message' => __('Failed to delete OTP log: :error', ['error' => $e->getMessage()]),
                ]);
            }

            $email = $request->input('email');
            $phone = $request->input('phone');

            if (!empty($email) || !empty($phone)) {
                $redirectUrl = route('otp-logs.detail');
                if (!empty($email)) {
                    $redirectUrl .= '?email=' . urlencode($email);
                } else {
                    $redirectUrl .= '?phone=' . urlencode($phone);
                }
                return redirect($redirectUrl)
                    ->withErrors(['message' => __('Failed to delete OTP log: :error', ['error' => $e->getMessage()])]);
            }

            return redirect()->route('otp-logs.index')
                ->withErrors(['message' => __('Failed to delete OTP log: :error', ['error' => $e->getMessage()])]);
        }
    }
}

