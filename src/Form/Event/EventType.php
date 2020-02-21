<?php

/*
 * This file is part of the vseth-semesterly-reports project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Form\Event;

use App\Entity\Reservation;
use App\Form\Type\SemesterType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('semester', SemesterType::class);
        $builder->add('nameDe', TextType::class, ['required' => false]);
        $builder->add('nameEn', TextType::class, ['required' => false]);
        $builder->add('descriptionDe', TextareaType::class, ['required' => false]);
        $builder->add('descriptionEn', TextareaType::class, ['required' => false]);
        $builder->add('location', TextType::class);

        $builder->add('showInCalender', CheckboxType::class, ['required' => false, 'label_attr' => ['class' => 'checkbox-custom']]);
        $builder->add('startDate', DateTimeType::class, ['widget' => 'single_text', 'required' => false]);
        $builder->add('endDate', DateTimeType::class, ['widget' => 'single_text', 'required' => false]);

        $builder->add('revenue', NumberType::class);
        $builder->add('expenditure', NumberType::class);
        $builder->add('needFinancialSupport', CheckboxType::class, ['required' => false, 'label_attr' => ['class' => 'checkbox-custom']]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
            'translation_domain' => 'entity_event',
        ]);
    }
}
