<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class DiabetesPredictionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pregnancies', IntegerType::class, [
                'label' => 'Nombre de grossesses',
                'attr'  => [
                    'min'         => 0,
                    'max'         => 17,
                    'placeholder' => 'Ex: 2',
                    'class'       => 'form-control',
                ],
                'constraints' => [
                    new Assert\NotBlank(message: 'Ce champ est obligatoire.'),
                    new Assert\Range(min: 0, max: 17,
                        notInRangeMessage: 'Valeur entre 0 et 17.'),
                ],
            ])
            ->add('glucose', NumberType::class, [
                'label' => 'Glycémie (mg/dL)',
                'attr'  => [
                    'min'         => 50,
                    'max'         => 250,
                    'step'        => '0.1',
                    'placeholder' => 'Ex: 120',
                    'class'       => 'form-control',
                ],
                'constraints' => [
                    new Assert\NotBlank(message: 'Ce champ est obligatoire.'),
                    new Assert\Range(min: 50, max: 250,
                        notInRangeMessage: 'Valeur entre 50 et 250 mg/dL.'),
                ],
            ])
            ->add('blood_pressure', NumberType::class, [
                'label' => 'Pression artérielle diastolique (mmHg)',
                'attr'  => [
                    'min'         => 40,
                    'max'         => 130,
                    'step'        => '0.1',
                    'placeholder' => 'Ex: 70',
                    'class'       => 'form-control',
                ],
                'constraints' => [
                    new Assert\NotBlank(message: 'Ce champ est obligatoire.'),
                    new Assert\Range(min: 40, max: 130,
                        notInRangeMessage: 'Valeur entre 40 et 130 mmHg.'),
                ],
            ])
            ->add('skin_thickness', NumberType::class, [
                'label' => 'Épaisseur du pli cutané tricipital (mm)',
                'attr'  => [
                    'min'         => 0,
                    'max'         => 99,
                    'step'        => '0.1',
                    'placeholder' => 'Ex: 20',
                    'class'       => 'form-control',
                ],
                'constraints' => [
                    new Assert\NotBlank(message: 'Ce champ est obligatoire.'),
                    new Assert\Range(min: 0, max: 99,
                        notInRangeMessage: 'Valeur entre 0 et 99 mm.'),
                ],
            ])
            ->add('insulin', NumberType::class, [
                'label' => 'Insuline (mu U/ml)',
                'attr'  => [
                    'min'         => 0,
                    'max'         => 900,
                    'step'        => '0.1',
                    'placeholder' => 'Ex: 80',
                    'class'       => 'form-control',
                ],
                'constraints' => [
                    new Assert\NotBlank(message: 'Ce champ est obligatoire.'),
                    new Assert\Range(min: 0, max: 900,
                        notInRangeMessage: 'Valeur entre 0 et 900 mu U/ml.'),
                ],
            ])
            ->add('bmi', NumberType::class, [
                'label' => 'IMC - Indice de Masse Corporelle (kg/m²)',
                'attr'  => [
                    'min'         => 10,
                    'max'         => 80,
                    'step'        => '0.1',
                    'placeholder' => 'Ex: 25.5',
                    'class'       => 'form-control',
                ],
                'constraints' => [
                    new Assert\NotBlank(message: 'Ce champ est obligatoire.'),
                    new Assert\Range(min: 10, max: 80,
                        notInRangeMessage: 'Valeur entre 10 et 80 kg/m².'),
                ],
            ])
            ->add('diabetes_pedigree', NumberType::class, [
                'label' => 'Antécédents familiaux (Diabetes Pedigree Function)',
                'attr'  => [
                    'min'         => 0.05,
                    'max'         => 2.5,
                    'step'        => '0.001',
                    'placeholder' => 'Ex: 0.627',
                    'class'       => 'form-control',
                ],
                'constraints' => [
                    new Assert\NotBlank(message: 'Ce champ est obligatoire.'),
                    new Assert\Range(min: 0.05, max: 2.5,
                        notInRangeMessage: 'Score entre 0.05 et 2.5.'),
                ],
                'help' => 'Score entre 0.05 et 2.5 basé sur les antécédents familiaux.',
            ])
            ->add('age', IntegerType::class, [
                'label' => 'Âge (années)',
                'attr'  => [
                    'min'         => 1,
                    'max'         => 120,
                    'placeholder' => 'Ex: 35',
                    'class'       => 'form-control',
                ],
                'constraints' => [
                    new Assert\NotBlank(message: 'Ce champ est obligatoire.'),
                    new Assert\Range(min: 1, max: 120,
                        notInRangeMessage: 'Âge entre 1 et 120 ans.'),
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => '🔍 Analyser le Risque',
                'attr'  => ['class' => 'btn btn-primary btn-lg w-100 mt-3'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}
