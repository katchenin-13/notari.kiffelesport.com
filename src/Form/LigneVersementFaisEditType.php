<?php

namespace App\Form;

use App\Entity\Ligneversementfrais;
use App\Form\DataTransformer\ThousandNumberTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LigneVersementFaisEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            /* ->add('dateversementfrais')
            ->add('montantverse') */

            ->add('dateversementfrais', DateType::class, [
                'required' => true,
                'mapped' => true,
                'widget' => 'single_text',
                'label'   => 'Date de paiement',
                'format'  => 'dd/MM/yyyy',
                'html5' => false,
                'attr'    => ['autocomplete' => 'off', 'class' => 'datepicker no-auto'],
            ])
            ->add('montantverse', TextType::class, ['label' => 'Montant', 'mapped' => true, 'attr' => ['class' => 'input-money input-mnt']]);
        $builder->get('montantverse')->addModelTransformer(new ThousandNumberTransformer());
        ;
            /* ->add('compte') */
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ligneversementfrais::class,
        ]);
    }
}
