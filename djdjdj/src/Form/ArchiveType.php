<?php

namespace App\Form;

use App\Entity\Archive;
use App\Entity\Client;
use App\Entity\Type;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArchiveType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('acheteur', EntityType::class, [
                'required' => false,
                'class' => Client::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->where('u.active = :val')
                        ->setParameter('val', 1)
                        ->orderBy('u.id', 'DESC');
                },
                'label' => false,
                'placeholder' => "Veuillez selectionner un acheteur",
                'choice_label' => function ($client) {
                    if ($client->getRaisonSocial() == "") {
                        return $client->getNom() . ' ' . $client->getPrenom();
                    } else {

                        return $client->getRaisonSocial();
                    }
                },
                'attr' => ['class' => 'form-control has-select2']

            ])
            ->add('vendeur', EntityType::class, [
                'required' => false,
                'class' => Client::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->where('u.active = :val')
                        ->setParameter('val', 1)
                        ->orderBy('u.id', 'DESC');
                },
                'label' => false,
                'placeholder' => 'Veuillez selectionner un vendeur',
                'choice_label' => function ($client) {
                    if ($client->getRaisonSocial() == "") {
                        return $client->getNom() . ' ' . $client->getPrenom();
                    } else {

                        return $client->getRaisonSocial();
                    }
                },
                'attr' => ['class' => 'form-control has-select2']

            ])
            ->add('numeroClassification', null, ['label' => 'Numéro de classification', 'empty_data' =>  ''])
            ->add('numeroOuverture', null, ['label' => 'Numéro d\'ouverture', 'empty_data' =>  ''])
        ->add('typeActe', EntityType::class, [
            'required' => true,
            'class' => Type::class,
            'choice_attr' => function (Type $type) {
                return ['data-code' => $type->getCode()];
            },

            'label' => 'Type d\'acte',
            'attr' => ['class' => 'form-control has-select2'],
            'choice_label' => 'titre',

        ])
            ->add('objet', null, ['label' => 'Objet', 'empty_data' => ''])

            ->add('description', TextareaType::class, ['label' => 'Description', 'required' => false, 'empty_data' => ''])

            ->add('dateOuverture', DateType::class, [
                'label' => 'Date d\'ouverture', 'html5' => false, 'attr' => ['class' => 'has-datepicker no-auto skip-init', 'autocomplete' => 'off'], 'widget' => 'single_text', 'format' => 'dd/MM/yyyy', 'empty_data' => ''
            ])
            ->add('dateClassification', DateType::class, [
                'html5' => false, 'attr' => ['class' => 'has-datepicker no-auto skip-init', 'autocomplete' => 'off'], 'widget' => 'single_text', 'format' => 'dd/MM/yyyy', 'empty_data' => '', 'label' => 'Date de classification'
            ])->add('documents', CollectionType::class, [
                'entry_type' => DocumentArchiveType::class,
                'entry_options' => [
                    'label' => false,
                    'doc_options' => $options['doc_options'],
                    'doc_required' => $options['doc_required'],
                    'validation_groups' => $options['validation_groups'],
                ],
                'allow_add' => true,
                'label' => false,
                'by_reference' => false,
                'allow_delete' => true,
                'prototype' => true,

            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Archive::class,
            'doc_required' => true,
            'doc_options' => [],
            'validation_groups' => [],
        ]);
        $resolver->setRequired('doc_options');
        $resolver->setRequired('doc_required');
        $resolver->setRequired(['validation_groups']);
    }
}
