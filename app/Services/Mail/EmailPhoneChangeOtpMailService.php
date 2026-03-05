<?php

namespace App\Services\Mail;

use App\Models\User;
use App\Services\OtpLogService;

class EmailPhoneChangeOtpMailService extends TechVillageMail
{
    /**
     * Send mail to user
     *
     * @param  object  $request
     * @param  string  $type
     * @return array $response
     */
    public function send($request, $type = 'email')
    {
        $email = $this->getTemplate(preference('dflt_lang'), 'email-verification');

        if (! $email['status']) {
            return $email;
        }

        // Get user for logging
        $user = User::whereEmailOrPhone($request->email ?? null, $request->phone ?? null)->first();
        if (!$user && auth()->check()) {
            $user = auth()->user();
        }

        // Log OTP send attempt
        // IMPORTANT: Use the NEW email/phone from request, not the old user email/phone
        $otpLogId = null;
        if ($user && isset($request->otp)) {
            // Pass the new email/phone as override parameters
            $newEmail = ($type === 'email' && isset($request->email)) ? $request->email : null;
            $newPhone = ($type === 'phone' && isset($request->phone)) ? $request->phone : null;
            
            $otpLog = OtpLogService::log(
                'email_phone_change',
                $type === 'email' ? 'email' : 'sms',
                'mail',
                $user,
                $request->otp,
                'sent',
                null,
                $newEmail, // Override with NEW email
                $newPhone  // Override with NEW phone
            );
            $otpLogId = $otpLog->id ?? null;
        }

        // Replacing template variable
        $subject = str_replace('{company_name}', preference('company_name'), $email->subject);
        $subject = __('OTP for :x Change', ['x' => $type === 'email' ? __('Email') : __('Phone')]);

        $data = [
            '{logo}' => $this->logo,
            '{company_name}' => preference('company_name'),
            '{verification_otp}' => $request->otp,
            '{support_mail}' => preference('company_email'),
            '{otp_active}' => '',
            '{token_active}' => 'display: none;',
            '{token_otp_active}' => 'display: none;',
            '{verification_url}' => '',
        ];

        $message = str_replace(array_keys($data), $data, $email->body);

        $emailTo = $type === 'email' ? $request->email : ($user->email ?? null);

        if (empty($emailTo)) {
            if ($otpLogId) {
                OtpLogService::updateStatus($otpLogId, 'failed', 'No email address available');
            }
            return ['status' => false, 'message' => __('No email address available.')];
        }

        $result = $this->email->sendEmail($emailTo, $subject, $message, null, preference('company_name'));

        // Update log status if email sending failed
        if ($otpLogId && (!$result || (isset($result['status']) && $result['status'] === false))) {
            OtpLogService::updateStatus(
                $otpLogId,
                'failed',
                $result['message'] ?? 'Email sending failed'
            );
        }

        return $result;
    }
}
