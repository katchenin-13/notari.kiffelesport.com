<?php

namespace App\Form;

use App\Entity\InfoClassification;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InfoClassificationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('numero', null, ['label' => 'Numéro de classification'])
            ->add('date', DateType::class, [
                'label' => false
                , 'html5' => false
                , 'attr' => ['class' => 'has-datepicker no-auto skip-init', 'autocomplete' => 'off']
                , 'widget' => 'single_text'
                , 'format' => 'dd/MM/yyyy'
                , 'empty_data' => date('d/m/Y')
                , 'label' => 'Date de classification'
            ])
            ->add('description',TextareaType::class,[
                'label'=>'Commentaire'
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
            'data_class' => InfoClassification::class,
        ]);
    }
}
