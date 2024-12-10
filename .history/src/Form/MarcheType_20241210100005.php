<?php

namespace App\Form;

use App\Entity\Marche;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MarcheType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('libelle', null, ['label' => 'Libellé'])
           ->add('datecreation', DateType::class, ['widget' => 'single_text', 'format' => 'yyyy-MM-dd', 'attr' => ['class' => 'datepicker no-auto skip-init']], ['label' => 'Date de creation'])
        ->add('montanttotal', TextType::class, ['label' => 'Montant total', 'mapped' => false, 'attr' => ['class' => 'input-money input-mnt']])

            ->add('solde', null, ['label' => 'Solde'])
         
            
            ->add('fournisseur')
      
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Marche::class,
        ]);
    }
}
