<?php

namespace App\Form;

use App\Entity\DocumentClient;
use App\Entity\DocumentTypeActe;
use App\Entity\DocumentTypeClient;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DocumentClientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('fichier', FichierType::class, ['label' => 'Fichier', 'label' => false, 'doc_options' => $options['doc_options'], 'required' => $options['doc_required'] ?? true])
            // ->add('fichier', DocumentTypeClient::class, ['label' => 'Libelle', 'label' => false, 'doc_options' => $options['doc_options'], 'required' => $options['doc_required'] ?? true]);
            ->add('document_type_client', EntityType::class, [
                'label' => false,
                 'class' => DocumentTypeClient::class, 
                 'choice_label' => function(DocumentTypeClient $d) { 
                    $libelle = $d->getTypesdocuments();
                    return  $libelle->getLibelle();
                 },
                  'attr' => [
                    'class' => 'form-control has-select2',
                'readonly' => true,
                    ]
                ]);
            // ->add('libelle', null, ['label' => false, 'empty_data' => '']);;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DocumentClient::class,
            'doc_required' => true
        ]);

        $resolver->setRequired('doc_options');
        $resolver->setRequired('doc_required');
    }
}
