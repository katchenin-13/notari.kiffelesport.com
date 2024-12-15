<?php

namespace App\Form;

use App\Entity\DocumentTypeClient;
use App\Entity\TypeClient;
use App\Entity\Typedocument;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DocumentTypeClientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            // ->add('libelle', null, ['label' => 'Libellé'])
            // ->add('typesdocuments', EntityType::class, [
            //     'required' => true,
            //     'class' => Typedocument::class,
            //     'choice_label' => 'libelle',
            //     'attr' => ['class' => 'input-select']
            // ])
            ->add('typesdocuments', EntityType::class, [
                'label' => false,
                'class' => Typedocument::class,
                'choice_label' => 'libelle',
                'attr' => [
                    'class' => 'form-control has-select2'
                ]
            ]);
        
        ;
    }
 
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DocumentTypeClient::class,
        ]);
    }
}
