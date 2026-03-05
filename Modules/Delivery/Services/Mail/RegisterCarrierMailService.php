<?php

namespace Modules\Delivery\Services\Mail;

use App\Services\Mail\TechVillageMail;

class RegisterCarrierMailService extends TechVillageMail
{
    /**
     * Send mail to Asignee
     *
     * @param  object  $request
     * @return array $response
     */
    public function send($request)
    {
        $email = $this->getTemplate(preference('dflt_lang'), 'new-delivery-boy-email-notification');

        if (! $email['status']) {
            return $email;
        }

        // Replacing template variable
        $subject = str_replace('{company_name}', preference('company_name'), $email->subject);

        $data = [
            '{user_name}' => $request->name,
            '{user_email}' => $request->email,
            '{user_pass}' => $request->password,
            '{company_url}' => route('site.login'),
            '{company_name}' =>  preference('company_name'),
            '{support_mail}' =>  preference('company_email'),
            '{logo}' =>  $this->logo,
        ];

        $message = str_replace(array_keys($data), $data, $email->body);

        return $this->email->sendEmail($request->email, $subject, $message, null, preference('company_name'));

    }
}
