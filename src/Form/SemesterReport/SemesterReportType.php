<?php

/*
 * This file is part of the vseth-semesterly-reports project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Form\SemesterReport;

use App\Entity\SemesterReport;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SemesterReportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('generalConformity', CheckboxType::class, ['mapped' => false, 'label_attr' => ['class' => 'checkbox-custom']]);
        $builder->add('statutesConformity', CheckboxType::class, ['mapped' => false, 'label_attr' => ['class' => 'checkbox-custom']]);
        $builder->add('ciConformity', CheckboxType::class, ['mapped' => false, 'label_attr' => ['class' => 'checkbox-custom']]);
        $builder->add('addedAllEvents', CheckboxType::class, ['mapped' => false, 'label_attr' => ['class' => 'checkbox-custom', 'link' => 'hi mom']]);

        $builder->add('politicalEventsDescription', TextareaType::class, ['required' => false]);
        $builder->add('comments', TextareaType::class, ['required' => false]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SemesterReport::class,
            'translation_domain' => 'entity_semester_report',
        ]);
    }
}
