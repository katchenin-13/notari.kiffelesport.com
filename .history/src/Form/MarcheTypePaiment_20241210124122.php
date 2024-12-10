<?php

namespace App\Form;

use App\Entity\Fournisseur;
use App\Entity\Lignepaiementmarche;
use App\Entity\Marche;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MarcheTypePaiement extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fournisseur', EntityType::class, [
                'required' => true,
                'class' => Fournisseur::class,
                'choice_label' => 'nom',
                'attr' => ['class' => 'input-select']
            ])
            ->add('libelle', null, ['label' => 'Libellé'])
            ->add('datecreation', DateType::class, ['widget' => 'single_text', 'format' => 'yyyy-MM-dd', 'attr' => ['class' => 'datepicker no-auto skip-init']], ['label' => 'Date de creation'])
        ->add('montanttotal', TextType::class, [
            'label' => 'Montant total',
            'attr' => ['class' => 'input-money input-mnt'],
            'empty_data' => '0',
            // 'constraints' => [
            //     new Assert\NotBlank(),
            //     new Assert\Type(['type' => 'numeric']),
            // ],
        ])
            ->add('fichier', FileType::class, [
                'label' => 'Fichier',
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'class' => 'input-file',
                    'accept' => '.pdf,.doc,.docx,.jpg,.jpeg,.png',
                ],
            ])

            ->add('lignedepenses', CollectionType::class, [
                'entry_type' => LignepaiementmarcheType::class,
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
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Marche::class,
            'doc_required' => true,
            'doc_options' => [],
            'validation_groups' => [],
        ]);
        $resolver->setRequired('doc_options');
        $resolver->setRequired('doc_required');
        $resolver->setRequired(['validation_groups']);
    }
    
}
