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
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(ParameterBagInterface $parameterBag, TranslatorInterface $translator)
    {
        $this->payrexxInstanceName = $parameterBag->get('PAYREX_INSTANCE');
        $this->payrexxSecret = $parameterBag->get('PAYREX_SECRET');
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function startPayment(Bill $bill)
    {
        $payrexx = new Payrexx($this->payrexxInstanceName, $this->payrexxSecret);

        $invoice = new Invoice();
        $invoice->setReferenceId($bill->getId()); // info for payment link (reference id)

        $title = $this->translator->trans('index.title', [], 'payment');
        $description = $this->translator->trans('index.description', [], 'payment');
        $invoice->setTitle($title);
        $invoice->setDescription($description);

        // administrative information, which provider to use (psp)
        // psp #1 = Payrexx' test mode, see http://developers.payrexx.com/docs/miscellaneous
        $invoice->setPsp(1);

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

        try {
            $response = $payrexx->create($invoice);
            dump($response);
        } catch (\Payrexx\PayrexxException $e) {
            echo $e->getMessage();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function paymentSuccessful(PaymentInfo $paymentInfo)
    {
        // TODO: Implement paymentSuccessful() method.

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function closePayment(PaymentInfo $paymentInfo)
    {
        // TODO: Implement closePayment() method.

        return false;
    }
}
