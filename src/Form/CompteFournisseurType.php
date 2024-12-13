<?php

namespace App\Form;

use App\Entity\Compte;
<<<<<<< HEAD
use App\Entity\Comptefour;
=======
use App\Entity\CompteFournisseur;
>>>>>>> b6f1842a7fec2506df675de17037826c2c1327b4
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

<<<<<<< HEAD
class ComptefourType extends AbstractType
=======
class CompteFournisseurType extends AbstractType
>>>>>>> b6f1842a7fec2506df675de17037826c2c1327b4
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('montant')
            ->add('solde')
            ->add('client')
            ->add('dossier')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
<<<<<<< HEAD
            'data_class' => Comptefour::class,
=======
            'data_class' => CompteFournisseur::class,
>>>>>>> b6f1842a7fec2506df675de17037826c2c1327b4
        ]);
    }
}
