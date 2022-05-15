<?php

namespace App\Form;

use App\Entity\Local;
use App\Entity\DataType;
use App\Repository\DataTypeRepository;
use App\Repository\LocalRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class DataSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', EntityType::class , [
                'class' => DataType::class,
                'query_builder' => function (DataTypeRepository $repo) {
                    return $repo->createQueryBuilder('d')
                    ->orderBy('d.value', 'ASC');;
                },
                'required' => false,
                'label' => false,
                'placeholder' => "Type",
            ])
            ->add('local', EntityType::class, [
                'class' => Local::class,
                'query_builder' => function (LocalRepository $repo) {
                    return $repo->createQueryBuilder('l')
                    ->orderBy('l.local', 'ASC');;
                },
                'required' => true,
                'label' => false
            ])
            ->add('frequence', ChoiceType::class, [
                "choices" => [
                    "Dernière semaine" => "Week", 
                    "Dernier mois" => "Month",
                    "Dernier trimestre" => "Trimsestre",
                    'Dernière année' => 'Year'
                  ],
                  'required' => false,
                  'label' => false,
                  'placeholder' => "Fréquence",
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}