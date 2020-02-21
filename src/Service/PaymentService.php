<?php

/*
 * This file is part of the vseth-semesterly-reports project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Service;

use App\Model\Bill;
use App\Model\PaymentInfo;
use App\Service\Interfaces\PaymentServiceInterface;
use Payrexx\Models\Request\Invoice;
use Payrexx\Payrexx;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class PaymentService implements PaymentServiceInterface
{
    /**
     * @var string
     */
    private $payrexxInstanceName;

    /**
     * @var string
     */
    private $payrexxSecret;

    /**
     * @var string
     */
    private $payrexxPsp;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    private $payrexx;

    public function __construct(ParameterBagInterface $parameterBag, TranslatorInterface $translator)
    {
        $this->payrexxInstanceName = $parameterBag->get('PAYREXX_INSTANCE');
        $this->payrexxSecret = $parameterBag->get('PAYREXX_SECRET');
        $this->payrexxPsp = $parameterBag->get('PAYREXX_PSP');

        $this->translator = $translator;
    }

    /**
     * @throws \Payrexx\PayrexxException
     *
     * @return Payrexx
     */
    private function getPayrexx()
    {
        return new Payrexx($this->payrexxInstanceName, $this->payrexxSecret);
    }

    /**
     * {@inheritdoc}
     */
    public function startPayment(Bill $bill)
    {
        $invoice = new Invoice();
        $invoice->setReferenceId($bill->getId()); // info for payment link (reference id)

        $title = $this->translator->trans('index.title', [], 'payment');
        $description = $this->translator->trans('index.description', [], 'payment');
        $invoice->setTitle($title);
        $invoice->setDescription($description);

        $invoice->setPsp($this->payrexxPsp); // see http://developers.payrexx.com/docs/miscellaneous

        // don't forget to multiply by 100
        $invoice->setAmount($bill->getTotal() * 100);
        $invoice->setVatRate(null);
        $invoice->setCurrency('CHF');

        // add contact information fields which should be filled by customer
        $recipient = $bill->getRecipient();
        $invoice->addField($type = 'email', true, $recipient->getEmail());
        $invoice->addField($type = 'forename', true, $recipient->getGivenName());
        $invoice->addField($type = 'surname', true, $recipient->getFamilyName());
        $invoice->addField($type = 'address_line_1', true, $recipient->getAddressLine1());
        $invoice->addField($type = 'address_line_2', true, $recipient->getAddressLine2());
        $invoice->addField($type = 'city', true, $recipient->getCity());
        $invoice->addField($type = 'country', true, 'Switzerland');

        $invoice->addField($type = 'terms', $mandatory = true);
        $invoice->addField($type = 'privacy_policy', $mandatory = true);
        $invoice->addField($type = 'custom_field_1', $mandatory = true, $defaultValue = 'Value 001', $name = 'Das ist ein Feld');

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
    public function paymentSuccessful(PaymentInfo $paymentInfo)
    {
        $payrexx = $this->getPayrexx();

        $invoice = new Invoice();
        $invoice->setId($paymentInfo->getInvoiceId());

        /** @var \Payrexx\Models\Response\Invoice $response */
        $response = $payrexx->getOne($invoice);

        return $response->getStatus() === 'confirmed';
    }

    /**
     * {@inheritdoc}
     */
    public function closePayment(PaymentInfo $paymentInfo)
    {
        $payrexx = $this->getPayrexx();

        $invoice = new Invoice();
        $invoice->setId($paymentInfo->getInvoiceId());

        /** @var \Payrexx\Models\Response\Invoice $response */
        $response = $payrexx->delete($invoice);

        return $response->getStatus() === 'success';
    }
}
