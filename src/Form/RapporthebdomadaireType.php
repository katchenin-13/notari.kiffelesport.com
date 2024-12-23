<?php

namespace App\Form;

use App\Entity\Rapporthebdomadaire;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RapporthebdomadaireType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('libelle', TextType::class, ['label' => 'Libellé'])
            ->add('daterapports', DateType::class, ['widget' => 'single_text', 'format' => 'yyyy-MM-dd', 'attr' => ['class' => 'datepicker no-auto skip-init']], ['label' => 'Date de creation'])
            ->add('description', TextType::class, ['label' => 'Description'])
            ->add('fichier', FileType::class, [
                'label' => 'Joindre la facture',
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'class' => 'input-file',
                    'accept' => '.pdf,',
                ],
            ])
        ;
            // ->add('utilisateur')
            // ->add('employe')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Rapporthebdomadaire::class,
        ]);
    }
}
