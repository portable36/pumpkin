<?php

namespace App\Notifications;

use App\Notifications\Channel\AdminDatabaseChannel;
use App\Notifications\Channel\SmsChannel;
use App\Services\Mail\EmailPhoneChangeOtpMailService;
use App\Traits\NotificationTrait;
use Illuminate\Bus\Queueable;

class EmailPhoneChangeOtpNotification extends Notification
{
    use NotificationTrait;
    use Queueable;

    private $request;
    private $type; // 'email' or 'phone'

    /**
     * Notification Label
     */
    public static $label = 'Email/Phone Change OTP';

    /**
     * Image
     *
     * @var string
     */
    public static $image = 'public/frontend/img/user.png';

    public function __construct($request, $type = null)
    {
        $this->request = $request;
        $this->type = $type;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function setVia($notifiable)
    {
        switch ($this->type) {
            case 'email':
                return ['mail', 'database', AdminDatabaseChannel::class];
            case 'phone':
                return ['database', SmsChannel::class, AdminDatabaseChannel::class];
            default:
                return ['mail', 'database', SmsChannel::class, AdminDatabaseChannel::class];
        }
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable)
    {
        return (new EmailPhoneChangeOtpMailService())->send($this->request, $this->type);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'id' => $notifiable->id,
            'label' => static::$label,
            'url' => '',
            'message' => __('OTP sent for :x change verification.', ['x' => $this->type === 'email' ? __('Email') : __('Phone')]),
        ];
    }

    /**
     * Get the SMS representation of the notification.
     */
    public function toSms(object $notifiable): array
    {
        $message = $this->getSmsData('email-verification');
        if (empty($message)) {
            $message = __('Your OTP for :x change is :y', ['x' => $this->type === 'email' ? __('email') : __('phone'), 'y' => $this->request->otp]);
        }
        return [
            'to' => $this->request->phone ?? $notifiable->phone,
            'message' => $message,
        ];
    }

    /**
     * Replace SMS variables in the given SMS body.
     *
     * @param  string  $body
     * @return string
     */
    public function replaceSmsVariables($body)
    {
        $data = [
            '{logo}' => '',
            '{company_name}' => preference('company_name'),
            '{verification_otp}' => $this->request->otp,
            '{support_mail}' => preference('company_email'),
            '{otp_active}' => '',
            '{token_active}' => '',
            '{token_otp_active}' => '',
        ];

        return str_replace(array_keys($data), $data, $body);
    }
}
