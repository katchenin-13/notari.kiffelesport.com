<?php

namespace App\Form;

use App\Entity\CommentairePaiement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentairePaiementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('description',TextareaType::class,[
            'label'=>'Commentaire',
            'required' => false,
        ])

            ->add('active', CheckboxType::class, [
                'label' => 'Envoyer un email', // Texte affiché à côté de la checkbox
                'required' => false, // La case doit être cochée pour valider le formulaire
                'attr' => [
                    'class' => 'custom-checkbox', // Classe CSS personnalisée pour la case
                    'id' => 'terms-checkbox',    // ID spécifique pour cibler la case
                ],
                'label_attr' => [
                    'class' => 'custom-label',   // Classe CSS personnalisée pour le libellé
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CommentairePaiement::class,
        ]);
    }
}
