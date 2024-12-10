<?php

namespace App\Form;

use App\Entity\Client;
use App\Entity\CourierArrive;
use App\Entity\FichierAccusseReception;
use App\Entity\User;
use App\Entity\Utilisateur;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CourierArriveType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ($options['type'] == 'imputer') {

            $builder->add('imputations', CollectionType::class, [
                'entry_type' => ImputationType::class,
                'entry_options' => [
                    'label' => false,
                ],
                'allow_add' => true,
                'label' => false,
                'by_reference' => false,
                'allow_delete' => true,
                'prototype' => true,

            ]);
        } else if ($options['type'] == 'accuse') {

            $builder
                ->add('dateReception', DateType::class, [
                    /*  "label" => "Date de réception",*/
                    "required" => false,
                    "widget" => 'single_text',
                    "input_format" => 'Y-m-d',
                    "by_reference" => true,
                    "empty_data" => '',
                ])
                ->add('fichierAccusseReceptions', CollectionType::class, [

                    'entry_type' => FichierAccusseReceptionType::class,
                    'entry_options' => [
                        'label' => false,
                        'doc_options' => $options['doc_options'],
                        'doc_required' => $options['doc_required']
                    ],
                    'allow_add' => true,
                    'label' => false,
                    'by_reference' => false,
                    'allow_delete' => true,
                    'prototype' => true,
                ]);
        } else {
            $builder
                ->add('existe', CheckboxType::class, [
                    'label' => false,
                    'required' => false,
                ])
                ->add('numero')
                ->add('etat', CheckboxType::class, [
                    'label' => false,
                    'required' => false,
                ])
                ->add('rangement')
                ->add('dateCreation', DateType::class, [
                    /*  "label" => "Date de réception",*/
                    "required" => false,
                    "widget" => 'single_text',
                    "input_format" => 'Y-m-d',
                    "by_reference" => true,
                    "empty_data" => '',
                ])
                ->add('objet', TextType::class)
                /*  ->add('type', ChoiceType::class,
                [
                    'expanded' => false,
                    'required' => true,
                    // 'attr' => ['class' => 'select2_multiple'],
                    'multiple' => false,
                    //'choices_as_values' => true,

                    'choices' => array_flip([
                        'ARRIVE' => 'ARRIVE',
                        'DEPART' => 'DEPART',
                        'INTERNE' => 'INTERNE',


                    ]),
                ])*/
                ->add('user', EntityType::class, [
                    'required' => false,
                    'class' => Utilisateur::class,
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('u')
                            ->orderBy('u.id', 'DESC');
                    },
                    'label' => 'Réceptionné par',
                    'placeholder' => 'Selectionner un recepteur',
                    'choice_label' => function ($user) {
                        return $user->getNomComplet();
                    },
                    'attr' => ['class' => 'form-control has-select2', 'id' => 'validationCustom05']

                ])

                ->add('expediteur', TextType::class, [
                    'required' => false,
                ])

                ->add('recep', EntityType::class, [
                    'required' => false,
                    'class' => Client::class,
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('u')
                            ->where('u.active = :val')
                            ->setParameter('val', 1)
                            ->orderBy('u.id', 'DESC');
                    },
                    'label' => 'Réceptionné par',
                    'placeholder' => 'Selectionner un recepteur',
                    'choice_label' => function ($client) {
                        if ($client->getRaisonSocial() == "") {
                            return $client->getNom() . ' ' . $client->getPrenom();
                        } else {

                            return $client->getRaisonSocial();
                        }
                    },
                    'attr' => ['class' => 'form-control has-select2', 'id' => 'validationCustom05']

                ])

                ->add('documentCouriers', CollectionType::class, [
                    'entry_type' => DocumentCourierType::class,
                    'entry_options' => [
                        'label' => false,
                        'doc_options' => $options['doc_options'],
                        'doc_required' => $options['doc_required']
                    ],
                    'allow_add' => true,
                    'label' => false,
                    'by_reference' => false,
                    'allow_delete' => true,
                    'prototype' => true,

                ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CourierArrive::class,
            'doc_required' => true,
            'doc_options' => [],
            'validation_groups' => [],
        ]);
        $resolver->setRequired('doc_options');
        $resolver->setRequired('doc_required');
        $resolver->setRequired(['validation_groups',]);
        $resolver->setRequired('type');
    }
}
