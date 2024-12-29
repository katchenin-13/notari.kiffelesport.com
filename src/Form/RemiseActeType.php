<?php

namespace App\Form;

use App\Entity\RemiseActe;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class RemiseActeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('date', DateType::class, [
            'label' => 'Date'
            , 'html5' => false
            , 'attr' => ['class' => 'has-datepicker no-auto skip-init', 'autocomplete' => 'off']
            , 'widget' => 'single_text'
            , 'format' => 'dd/MM/yyyy'
            , 'empty_data' => date('d/m/Y')
        ])
        ->add('expedition', FichierType::class, ['label' => 'Expédition','label'=>false, 'doc_options' => $options['doc_options'], 'required' => $options['doc_required'] ?? true])
            ->add('copie', FichierType::class, ['label' => 'Copié', 'label' => false,'doc_options' => $options['doc_options'], 'required' => $options['doc_required'] ?? true])
        ->add('grosse', FichierType::class, ['label' => 'Grosse', 'label' => false,'doc_options' => $options['doc_options'], 'required' => $options['doc_required'] ?? true])


        ->add('commentaire', TextareaType::class, ['label' => 'Commentaire', 'attr' => ['class' => 'description']])
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
            'data_class' => RemiseActe::class,
            'doc_required' => true
        ]);

        $resolver->setRequired('doc_options');
        $resolver->setRequired('doc_required');
    }
}
