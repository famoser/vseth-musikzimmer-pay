<?php

/*
 * This file is part of the vseth-musikzimmer-pay project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Extension;

use App\Enum\BooleanType;
use App\Enum\PaymentRemainderStatusType;
use App\Enum\UserCategoryType;
use DateTime;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TwigExtension extends AbstractExtension
{
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * makes the filters available to twig.
     *
     * @return array
     */
    public function getFilters()
    {
        return [
            new TwigFilter('dateFormat', [$this, 'dateFormatFilter']),
            new TwigFilter('timeFormat', [$this, 'timeFormatFilter']),
            new TwigFilter('dateTimeFormat', [$this, 'dateTimeFilter']),
            new TwigFilter('booleanFormat', [$this, 'booleanFilter']),
            new TwigFilter('categoryText', [$this, 'categoryTextFilter']),
            new TwigFilter('paymentRemainderStatusText', [$this, 'paymentRemainderStatusTextFilter']),
            new TwigFilter('camelCaseToUnderscore', [$this, 'camelCaseToUnderscoreFilter']),
        ];
    }

    /**
     * @param string $propertyName
     *
     * @return string
     */
    public function camelCaseToUnderscoreFilter($propertyName)
    {
        return mb_strtolower(preg_replace('/(?<=[a-z])([A-Z])/', '_$1', $propertyName));
    }

    /**
     * @param $date
     *
     * @return string
     */
    public function dateFormatFilter($date)
    {
        if ($date instanceof \DateTime) {
            $dateFormat = $this->translator->trans('time.format.date', [], 'framework');

            return $this->prependDayName($date) . ', ' . $date->format($dateFormat);
        }

        return '-';
    }

    public function paymentRemainderStatusTextFilter($value)
    {
        if (\is_int($value)) {
            return PaymentRemainderStatusType::getTranslation($value, $this->translator);
        }

        return '-';
    }

    /**
     * @param $value
     *
     * @return string
     */
    public function categoryTextFilter($value)
    {
        if (\is_int($value)) {
            return UserCategoryType::getTranslation($value, $this->translator);
        }

        return '-';
    }

    /**
     * @param $date
     *
     * @return string
     */
    public function dateTimeFilter($date)
    {
        if ($date instanceof \DateTime) {
            $dateTimeFormat = $this->translator->trans('time.format.date_time', [], 'framework');

            return $this->prependDayName($date) . ', ' . $date->format($dateTimeFormat);
        }

        return '-';
    }

    /**
     * @param $date
     *
     * @return string
     */
    public function timeFormatFilter($date)
    {
        if (\is_string($date)) {
            return mb_substr($date, 0, 5);
        }

        return '-';
    }

    /**
     * translates the day of the week.
     *
     * @return string
     */
    private function prependDayName(DateTime $date)
    {
        return $this->translator->trans('time.weekdays.' . $date->format('D'), [], 'framework');
    }

    /**
     * @param $value
     *
     * @return string
     */
    public function booleanFilter($value)
    {
        if ($value) {
            return BooleanType::getTranslation(BooleanType::YES, $this->translator);
        }

        return BooleanType::getTranslation(BooleanType::NO, $this->translator);
    }
}
