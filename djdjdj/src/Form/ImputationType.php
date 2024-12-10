<?php

namespace App\Form;

use App\Entity\Employe;
use App\Entity\Imputation;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ImputationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('commentaire')
            ->add('employe', EntityType::class, [
                    'class'        => Employe::class,
                    'label'        => 'Employe',
                    'required' => True,
                    'choice_label' => 'nom',
                
                    'multiple'     => false,
                    'expanded'     => false,
                    'placeholder' => 'Choisir un employe',
                    'attr' => ['class' => 'has-select2'],
                   
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Imputation::class,
        ]);
    }
}
