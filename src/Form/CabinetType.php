<?php

namespace App\Form;

use App\Entity\Cabinet;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CabinetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
           

            ->add('raisonSociale', TextType::class, ['label' => 'Raison social'])
            ->add('contacts', TextType::class, ['label' => 'Contact'])
            ->add('email', EmailType::class, ['label' => 'Email'])
            ->add('pb', TextType::class, ['label' => 'Adresse BP'])
            ->add('localisation', TextType::class, ['label' => 'Localisation'])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Cabinet::class,
        ]);
    }
}
