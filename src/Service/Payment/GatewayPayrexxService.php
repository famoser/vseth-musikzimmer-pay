<?php

/*
 * This file is part of the vseth-musikzimmer-pay project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Service\Payment;

use App\Model\Bill;
use App\Model\PaymentInfo;
use App\Model\TransactionInfo;
use Payrexx\Models\Request\Gateway;
use Payrexx\Models\Request\Invoice;

class GatewayPayrexxService extends BasePayrexxProviderService
{
    /**
     * {@inheritdoc}
     */
    public function startPayment(Bill $bill, string $successUrl)
    {
        $gateway = new Gateway();

        $gateway->setReferenceId($bill->getId()); // info for payment link (reference id)

        $title = $this->getTranslator()->trans('index.title', [], 'payment');
        $billingPeriod = $this->getTranslator()->trans('index.billing_period', [], 'payment');
        $purpose = $title . ' ' . $billingPeriod . ' ' . $bill->getPeriodStart()->format('d.m.Y') . ' - ' . $bill->getPeriodEnd()->format('d.m.Y');
        $gateway->setPurpose([$purpose]);

        $gateway->setPsp([$this->getPayrexxPsp()]); // see http://developers.payrexx.com/docs/miscellaneous
        $gateway->setSuccessRedirectUrl($successUrl);
        $gateway->setCancelRedirectUrl($successUrl);
        $gateway->setFailedRedirectUrl($successUrl);

        // don't forget to multiply by 100
        $gateway->setAmount($bill->getTotal() * 100);
        $gateway->setVatRate(null);
        $gateway->setCurrency(Invoice::CURRENCY_CHF);

        // add contact information fields which should be filled by customer
        $recipient = $bill->getRecipient();
        $gateway->addField('email', true, $recipient->getEmail());
        $gateway->addField('forename', true, $recipient->getGivenName());
        $gateway->addField('surname', true, $recipient->getFamilyName());
        $gateway->addField('street', true, $recipient->getStreet());
        $gateway->addField('postcode', true, $recipient->getPostcode());
        $gateway->addField('place', true, $recipient->getPlace());
        $gateway->addField('country', true, 'CH');

        $payrexx = $this->getPayrexx();

        /** @var \Payrexx\Models\Response\Gateway $response */
        $response = $payrexx->create($gateway);

        $paymentInfo = new PaymentInfo();
        $paymentInfo->setInvoiceLink($response->getLink());
        $paymentInfo->setInvoiceId($response->getId());

        return $paymentInfo;
    }

    /**
     * {@inheritdoc}
     */
    public function paymentSuccessful(PaymentInfo $paymentInfo, ?TransactionInfo &$transactionInfo)
    {
        $payrexx = $this->getPayrexx();

        $gateway = new Gateway();
        $gateway->setId($paymentInfo->getInvoiceId());

        /** @var \Payrexx\Models\Response\Gateway $response */
        $response = $payrexx->getOne($gateway);
        if ($response->getStatus() !== 'confirmed') {
            return false;
        }

        $payedInvoice = $response->getInvoices()[0];
        $payedAmount = $payedInvoice['products'][0]['price'];

        $payedTransaction = \array_slice($payedInvoice['transactions'], -1)[0];
        $transactionId = $payedTransaction['uuid'];

        $transactionInfo = new TransactionInfo($payedAmount, $transactionId);

        return true;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Exception
     */
    public function closePayment(PaymentInfo $paymentInfo)
    {
        $payrexx = $this->getPayrexx();

        $gateway = new Gateway();
        $gateway->setId($paymentInfo->getInvoiceId());

        $payrexx->delete($gateway);
    }
}
