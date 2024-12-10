<?php

namespace App\Form;

use App\Entity\Archive;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Archive1Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateCreation')
            ->add('numeroClassification')
            ->add('dateClassification')
            ->add('objet')
            ->add('numeroOuverture')
            ->add('dateOuverture')
            ->add('description')
            ->add('acheteur')
            ->add('vendeur')
            ->add('typeActe')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Archive::class,
        ]);
    }
}
