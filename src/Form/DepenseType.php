<?php

namespace App\Form;

use App\Entity\Depense;
use Mpdf\Tag\Li;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class DepenseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('libelle', null, ['label' => 'Libellé'])
            ->add('datedepense', DateType::class, [
                'label' => 'Date de dépense',
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control'],
            ])
            // ->add('mois', ChoiceType::class, [
            //     'label' => 'Mois',
            //     'mapped' => false, // Non lié directement à l'entité
            //     'choices' => [
            //         'Janvier' => '01',
            //         'Février' => '02',
            //         'Mars' => '03',
            //         'Avril' => '04',
            //         'Mai' => '05',
            //         'Juin' => '06',
            //         'Juillet' => '07',
            //         'Août' => '08',
            //         'Septembre' => '09',
            //         'Octobre' => '10',
            //         'Novembre' => '11',
            //         'Décembre' => '12',
            //     ],
            //     'attr' => [
            //         'class' => 'form-control',
            //         'readonly' => true, // Empêche la modification directe
            //     ],
            // ])
            // ->add('mois', TextType::class, [
            //     'label' => 'Mois',
            //     'attr' => [
            //         'class' => 'form-control',
            //         'readonly' => true, // Empêche l'utilisateur de le modifier
            //     ],
            // ])

            ->add('lignedepenses', CollectionType::class, [
                'entry_type' => LignedepenseType::class,
                'entry_options' => [
                    'label' => false,
                    'doc_options' => $options['doc_options'],
                    'required' => $options['doc_required'] ?? true,
                    'validation_groups' => $options['validation_groups'],
                ],
                'allow_add' => true,
                'label' => false,
                'by_reference' => false,
                'allow_delete' => true,
                'prototype' => true,

            ])
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                $depense = $event->getData();
                $form = $event->getForm();

                // Vérifier si la date de dépense est présente et définir le mois
                if ($depense && $depense->getDatedepense()) {
                    $mois = $depense->getDatedepense()->format('m');
                    $depense->setMois($mois); // Remplir la propriété directement
                    $form->get('mois')->setData($mois); // Remplir le champ du formulaire
                }
            })
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
                $data = $event->getData();

                // Si une date est envoyée, calculer et définir le mois correspondant
                if (isset($data['datedepense'])) {
                    $date = new \DateTime($data['datedepense']);
                    $data['mois'] = $date->format('m'); // Mettre à jour la donnée soumise
                    $event->setData($data);
                }
            });


    //         ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
    //             $form = $event->getForm();
    //             $data = $event->getData();

    //             // Vérifier si une date est présente et définir le mois correspondant
    //             if ($data && $data->getDatedepense()) {
    //                 $mois = $data->getDatedepense()->format('m');
    //                 $form->get('mois')->setData($mois);
    //             }
    //         })
    //         ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
    //             $data = $event->getData();

    //             // Remplir le champ mois dynamiquement à partir de datedepense
    //             if (isset($data['datedepense'])) {
    //                 $date = new \DateTime($data['datedepense']);
    //                 $data['mois'] = $date->format('m');
    //                 $event->setData($data);
    //             }
    //         });
     }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Depense::class,
            'doc_required' => true,
            'doc_options' => [],
            'validation_groups' => [],
        ]);
        $resolver->setRequired('doc_options');
        $resolver->setRequired('doc_required');
        $resolver->setRequired(['validation_groups']);
    }
}
