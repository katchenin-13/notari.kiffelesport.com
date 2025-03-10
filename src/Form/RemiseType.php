<?php

namespace App\Form;

use App\Entity\Remise;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class RemiseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('date', DateType::class, [
            'label' => "Date de remise"
            , 'html5' => false
            , 'attr' => ['class' => 'has-datepicker no-auto skip-init', 'autocomplete' => 'off']
            , 'widget' => 'single_text'
            , 'format' => 'dd/MM/yyyy'
            , 'empty_data' => date('d/m/Y')
        ])
        ->add('fichier', FichierType::class, ['label' => 'Fichier', 'label' => 'Accusé de réception ', 'doc_options' => $options['doc_options'], 'required' => $options['doc_required'] ?? true])
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
         /*   ->add('dossier')*/
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Remise::class,
            'doc_required' => true
        ]);

        $resolver->setRequired('doc_options');
        $resolver->setRequired('doc_required');
    }
}
