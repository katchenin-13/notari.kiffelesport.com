<?php

namespace App\Form;

use App\Entity\Fournisseur;
use App\Entity\Marche;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MarcheType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fournisseur', EntityType::class, [
                'required' => true,
                'class' => Fournisseur::class,
                'choice_label' => 'nom',
                'attr' => [
                    'label' => ' Choix fournisseur',
                    'class' => 'input-select'
                    ]
            ])
            ->add('libelle', null, ['label' => 'Objet du marché'])
            ->add('datecreation', DateType::class, ['widget' => 'single_text', 'format' => 'yyyy-MM-dd', 'attr' => ['class' => 'datepicker no-auto skip-init']], ['label' => 'Date de creation'])
        ->add('montanttotal', TextType::class, [
            'label' => 'Montant',
            'attr' => ['class' => 'input-money input-mnt'],
            'empty_data' => '0',
            // 'constraints' => [
            //     new Assert\NotBlank(),
            //     new Assert\Type(['type' => 'numeric']),
            // ],
        ])
            ->add('fichier', FichierType::class, ['label' => 'Fichier joint', 'doc_options' => $options['doc_options'], 'required' => $options['doc_required'] ?? true,])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Marche::class,
            'doc_required' => true
        ]);
        $resolver->setRequired('doc_options');
        $resolver->setRequired('doc_required');
    }
}
