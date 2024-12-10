<?php

namespace App\Form;

use App\Entity\Marche;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MarcheType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('libelle')
            ->add('montanttotal')
            ->add('solde')
         
            ->add('datecreation')
            ->add('fournisseur')
            ->add('path')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Marche::class,
        ]);
    }
}
