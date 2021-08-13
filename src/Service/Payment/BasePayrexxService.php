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

use App\Service\Payment\Interfaces\PaymentProviderServiceInterface;
use Payrexx\Payrexx;
use Payrexx\PayrexxException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class BasePayrexxService implements PaymentProviderServiceInterface
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
     * @var int
     */
    private $payrexxPsp;

    /**
     * @var float|null
     */
    private $payrexxVatRate;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(ParameterBagInterface $parameterBag, TranslatorInterface $translator)
    {
        $this->payrexxInstanceName = $parameterBag->get('PAYREXX_INSTANCE');
        $this->payrexxSecret = $parameterBag->get('PAYREXX_SECRET');
        $this->payrexxPsp = (int)$parameterBag->get('PAYREXX_PSP');
        $this->payrexxVatRate = mb_strlen($parameterBag->get('PAYREXX_VAT_RATE')) > 0 ? (float)$parameterBag->get('PAYREXX_VAT_RATE') : null;

        $this->translator = $translator;
    }

    protected function getPayrexxVatRate(): ?float
    {
        return $this->payrexxVatRate;
    }

    /**
     * @throws PayrexxException
     *
     * @return Payrexx
     */
    protected function getPayrexx()
    {
        return new Payrexx($this->payrexxInstanceName, $this->payrexxSecret);
    }

    protected function getTranslator(): TranslatorInterface
    {
        return $this->translator;
    }

    /**
     * @return TranslatorInterface
     */
    protected function getPayrexxPsp(): int
    {
        return $this->payrexxPsp;
    }
}
