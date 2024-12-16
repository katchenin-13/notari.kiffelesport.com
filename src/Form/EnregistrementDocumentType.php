<?php

namespace App\Form;

use App\Entity\EnregistrementDocument;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EnregistrementDocumentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fichier', FichierType::class, ['label' => false, 'doc_options' => $options['doc_options'], 'required' => $options['doc_required'] ?? true])
            ->add('fichierClient', FichierType::class, ['label' => false, 'doc_options' => $options['doc_options'], 'required' => $options['doc_required'] ?? true])
            ->add('fichierCourrier', FichierType::class, ['label' => false, 'doc_options' => $options['doc_options'], 'required' => $options['doc_required'] ?? true])
           
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EnregistrementDocument::class,
            'doc_required' => true,
            'allow_extra_fields' => true
        ]);
        $resolver->setRequired('doc_options');
        $resolver->setRequired('doc_required');
    }
}
