<?php

/*
 * This file is part of the vseth-semesterly-reports project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SemesterType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        // unix epoch was 1. Januar 1970; which would be FS70
        // hence FS2020 is 50*2 = 100

        $current = self::getCurrentSemester();
        $choices = [];
        $padding = 4;
        for ($i = -$padding; $i <= $padding; ++$i) {
            $semester = $current + $i;
            $choices[self::semesterToString($semester)] = $semester;
        }

        $resolver->setDefaults([
            'choices' => $choices,
            'choice_translation_domain' => false,
        ]);
    }

    public static function getCurrentSemester(): int
    {
        $now = new \DateTime();

        $currentYear = (int)($now)->format('Y');

        $isAutumnSemester = $now > new \DateTime('31.07.' . $currentYear);
        $years = $currentYear - 1970;

        return $years * 2 + 1 * $isAutumnSemester;
    }

    public static function semesterToString(int $semester): string
    {
        $isAutumnSemester = $semester % 2;
        $yearsSince1970 = (int)($semester / 2);
        $year = 1970 + $yearsSince1970 - 2000;

        return ($isAutumnSemester ? 'HS' : 'FS') . $year;
    }

    public function getParent()
    {
        return ChoiceType::class;
    }
}
