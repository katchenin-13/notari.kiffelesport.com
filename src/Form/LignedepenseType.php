<?php

namespace App\Form;

use App\Entity\Lignedepense;
use App\Entity\Type;
use App\Entity\Typedepense;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class LignedepenseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder


            ->add('typedepense', EntityType::class, [
                'label' => false,
                'class' => Typedepense::class,
                'choice_label' => 'libelle',
                'attr' => [
                    'class' => 'form-control has-select2'
                ]
            ])


            // ->add('typedepense', EntityType::class, [
            //     'required' => true,
            //     'class' => Typedepense::class,
            //     'choice_attr' => function (Typedepense $typedepense) {
            //         return ['data-code' => $typedepense->getLibelle()];
            //     },

            //     'label' => 'Type d\'acte',
            //     'attr' => ['class' => 'form-control has-select2'],
            //     'choice_label' => 'libelle',

            // ])

           

        ->add('montant', TextType::class, [
            'label' => false,
            'attr' => ['class' => 'input-money input-mnt'],
            'empty_data' => '0',
            // 'constraints' => [
            //     new Assert\NotBlank(),
            //     new Assert\Type(['type' => 'numeric']),
            // ],
        ])
        ->add('fichier', FichierType::class, [
            'label' => false,
            'doc_options' => $options['doc_options'], // Utilisation correcte
            'required' => $options['doc_required'] ?? true,
        ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Lignedepense::class,
            'doc_required' => true
        ]);
        $resolver->setRequired('doc_options');
        $resolver->setRequired('doc_required');
    }
}
