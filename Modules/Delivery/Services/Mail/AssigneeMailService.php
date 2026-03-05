<?php

namespace Modules\Delivery\Services\Mail;

use App\Models\Order;
use App\Services\Mail\TechVillageMail;
use Modules\Delivery\Entities\DeliveryMan;

class AssigneeMailService extends TechVillageMail
{
    /**
     * Send mail to Asignee
     *
     * @param  object  $request
     * @return array $response
     */
    public function send($request)
    {
        $email = $this->getTemplate(preference('dflt_lang'), 'delivery-boy-assigned');

        if (! $email['status']) {
            return $email;
        }

        $carrier = DeliveryMan::where('id', $request->delivery_man_id)->with('user')->first();
        $order = Order::find($request->order_id);
        $order_link = url('carrier/order/show/' . base64_encode($request->order_id));
        $subject      = str_replace(['{order_no}'], [$order->reference], $email->subject);

        $message = str_replace(
            [
                '{assignee_name}',
                '{order_no}',
                '{details}',
                '{company_name}',
                '{logo}',
            ],
            [
                optional($carrier->user)->name,
                $order->reference,
                $order_link,
                preference('company_name'),
                $this->logo,
            ],
            $email->body
        );

        return $this->email->sendEmail(optional($carrier->user)->email, $subject, $message, null, 'assigned');

    }
}
