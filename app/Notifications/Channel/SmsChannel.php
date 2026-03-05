<?php

namespace App\Notifications\Channel;

use App\Contract\SmsInterface;
use App\Services\OtpLogService;
use Exception;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use ReflectionClass;
use ReflectionException;

class SmsChannel
{
    /**
     * The data for the SMS notification.
     *
     * @var array
     */
    private $data;

    /**
     * The default SMS gateway to be used.
     *
     * @var string
     */
    private $defaultSmsGateway;

    /**
     * The available SMS gateways configuration.
     *
     * @var array
     */
    private $smsGateways;

    /**
     * Notification Class.
     *
     * @var string
     */
    private $notificationClass;

    /**
     * The notifiable entity.
     *
     * @var object
     */
    private $notifiable;

    /**
     * The notification instance.
     *
     * @var Notification
     */
    private $notification;

    /**
     * Send the given notification.
     */
    public function send(object $notifiable, Notification $notification): void
    {
        $this->notifiable = $notifiable;
        $this->notification = $notification;
        $this->initialize($notifiable, $notification);

        // Log OTP even if provider is not enabled
        $otpLogId = $this->logOtpBeforeSend();

        if ($this->isValid()) {
            $this->sendSms($otpLogId);
        } else {
            // Provider not enabled or invalid - update log with error
            if ($otpLogId) {
                $errorMessage = $this->getErrorMessage();
                OtpLogService::updateStatus($otpLogId, 'failed', $errorMessage);
            }
        }
    }

    /**
     * Initialize the data and configuration.
     */
    private function initialize($notifiable, Notification $notification): void
    {
        $this->data = $notification->toSms($notifiable);
        $this->defaultSmsGateway = config('notification.default_sms_gateway');
        $this->smsGateways = config('notification.sms_gateways');
        $this->notificationClass = $this->smsGateways[$this->defaultSmsGateway] ?? '';
    }

    /**
     * Check if the data and configuration are valid for sending SMS.
     */
    private function isValid(): bool
    {
        return ! empty($this->data['to'])
            && ! empty($this->data['message'])
            && array_key_exists($this->defaultSmsGateway, $this->smsGateways)
            && class_exists($this->notificationClass)
            && is_subclass_of($this->notificationClass, SmsInterface::class);
    }

    /**
     * Get a property value from the notification using reflection.
     *
     * @param string $propertyName
     * @return object|null
     */
    private function getRequestPropertyFromNotification(string $propertyName): ?object
    {
        try {
            $reflection = new ReflectionClass($this->notification);
            if ($reflection->hasProperty($propertyName)) {
                $property = $reflection->getProperty($propertyName);
                $property->setAccessible(true);
                return $property->getValue($this->notification);
            }
        } catch (ReflectionException $e) {
            // If reflection fails, return null
            Log::warning('Could not access ' . $propertyName . ' property in notification: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Log OTP before sending
     *
     * @return int|null
     */
    private function logOtpBeforeSend(): ?int
    {
        // Check if this is an OTP-related notification
        $notificationClass = get_class($this->notification);
        $isOtpNotification = in_array($notificationClass, [
            \App\Notifications\UserVerificationNotification::class,
            \App\Notifications\ResetPasswordNotification::class,
            \App\Notifications\EmailPhoneChangeOtpNotification::class,
        ]);

        if (!$isOtpNotification) {
            return null;
        }

        // Determine OTP type
        $type = 'password_reset';
        if ($notificationClass === \App\Notifications\UserVerificationNotification::class) {
            $type = 'registration';
        } elseif ($notificationClass === \App\Notifications\EmailPhoneChangeOtpNotification::class) {
            $type = 'change_phone_number';
        }

        // Extract OTP from request if available
        $otp = null;
        $request = $this->getRequestPropertyFromNotification('request');
        if ($request) {
            $otp = $request->otp ?? $request->activation_otp ?? null;
        }

        // Get provider name
        $provider = $this->defaultSmsGateway;
        
        // For email/phone change, extract new phone from request
        $overridePhone = null;
        if ($notificationClass === \App\Notifications\EmailPhoneChangeOtpNotification::class) {
            $request = $this->getRequestPropertyFromNotification('request');
            $overridePhone = $request->phone ?? $this->data['to'] ?? null;
        }
        
        // Log the OTP send attempt with 'pending' status (will be updated to 'sent' after successful send)
        $otpLog = OtpLogService::log(
            $type,
            'sms',
            $provider,
            $this->notifiable,
            $otp,
            'pending',
            null,
            null, // overrideEmail
            $overridePhone // overridePhone - use NEW phone for email/phone change
        );

        return $otpLog->id ?? null;
    }

    /**
     * Send the SMS using the selected gateway.
     */
    private function sendSms(?int $otpLogId = null): void
    {
        try {
            app($this->notificationClass)->send($this->data);
            
            // Update OTP log status to 'sent' after successful send
            if ($otpLogId) {
                OtpLogService::updateStatus($otpLogId, 'sent');
            }
        } catch (Exception $e) {
            // Log the error or handle it gracefully
            Log::error('Error sending SMS: ' . $e->getMessage());
            
            // Update OTP log status to failed if it was logged
            if ($otpLogId) {
                OtpLogService::updateStatus($otpLogId, 'failed', $e->getMessage());
            }
        }
    }

    /**
     * Get error message when SMS provider is not available.
     *
     * @return string
     */
    private function getErrorMessage(): string
    {
        if (empty($this->defaultSmsGateway)) {
            return __('No SMS provider found');
        }

        if (!array_key_exists($this->defaultSmsGateway, $this->smsGateways)) {
            return __('SMS provider :provider not found', ['provider' => $this->defaultSmsGateway]);
        }

        if (empty($this->notificationClass) || !class_exists($this->notificationClass)) {
            return __('SMS provider class not found');
        }

        if (!is_subclass_of($this->notificationClass, SmsInterface::class)) {
            return __('SMS provider does not implement SmsInterface');
        }

        if (empty($this->data['to'])) {
            return __('Phone number is required');
        }

        if (empty($this->data['message'])) {
            return __('SMS message is required');
        }

        return __('SMS provider not enabled');
    }
}
