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
use Payrexx\Models\Request\Invoice;

class InvoicePayrexxService extends BasePayrexxService
{
    /**
     * {@inheritdoc}
     */
    public function startPayment(Bill $bill, string $successUrl)
    {
        $invoice = new Invoice();

        $invoice->setReferenceId($bill->getId()); // info for payment link (reference id)

        $title = $this->getTranslator()->trans('index.title', [], 'payment');
        $description = $this->getTranslator()->trans('index.description', [], 'payment');
        $invoice->setTitle($title);
        $invoice->setDescription($description);

        $billingPeriod = $this->getTranslator()->trans('index.billing_period', [], 'payment');
        $purpose = $title . ' ' . $billingPeriod . ' ' . $bill->getPeriodStart()->format('d.m.Y') . ' - ' . $bill->getPeriodEnd()->format('d.m.Y');
        $invoice->setPurpose($purpose);

        $invoice->setPsp($this->getPayrexxPsp()); // see http://developers.payrexx.com/docs/miscellaneous
        $invoice->setSuccessRedirectUrl($successUrl);

        // don't forget to multiply by 100
        $invoice->setAmount($bill->getTotal() * 100);
        $invoice->setVatRate(null);
        $invoice->setCurrency(Invoice::CURRENCY_CHF);

        // add contact information fields which should be filled by customer
        $recipient = $bill->getRecipient();
        $invoice->addField('email', true, $recipient->getEmail());
        $invoice->addField('forename', true, $recipient->getGivenName());
        $invoice->addField('surname', true, $recipient->getFamilyName());
        $invoice->addField('street', true, $recipient->getStreet());
        $invoice->addField('postcode', true, $recipient->getPostcode());
        $invoice->addField('place', true, $recipient->getPlace());
        $invoice->addField('country', true, 'CH');

        $payrexx = $this->getPayrexx();

        /** @var \Payrexx\Models\Response\Invoice $response */
        $response = $payrexx->create($invoice);

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

        $invoice = new Invoice();
        $invoice->setId($paymentInfo->getInvoiceId());

        /** @var \Payrexx\Models\Response\Invoice $response */
        $response = $payrexx->getOne($invoice);
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

        $invoice = new Invoice();
        $invoice->setId($paymentInfo->getInvoiceId());

        $payrexx->delete($invoice);
    }
}
