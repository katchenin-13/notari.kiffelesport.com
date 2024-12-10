<?php

namespace App\Form;

use App\Entity\Workflow;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Workflow1Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('numeroEtape')
            ->add('libelleEtape')
            ->add('NombreJours')
            ->add('active')
            ->add('propriete')
            ->add('route')
            ->add('typeActe')
            ->add('gestionWorkflow')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Workflow::class,
        ]);
    }
}
