<?php

namespace App\Form;

<<<<<<< HEAD
=======
use App\Entity\Comptefour;
use App\Form\DataTransformer\ThousandNumberTransformer;
>>>>>>> 11b7eb5 (save pour review)
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LignepaiementmarcheType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('field_name')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
<<<<<<< HEAD
            // Configure your form options here
=======
            'data_class' => Comptefour::class,

>>>>>>> 11b7eb5 (save pour review)
        ]);
    }
}
