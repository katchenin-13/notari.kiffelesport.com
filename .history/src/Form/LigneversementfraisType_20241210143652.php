<?php

namespace App\Form;

use App\Entity\Compte;
use App\Entity\Ligneversementfrais;
use App\Form\DataTransformer\ThousandNumberTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LigneversementfraisType extends AbstractType
{
/*************  ✨ Codeium Command ⭐  *************/
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     *
     * Build form for versement des frais
     *
     * @return void
     */
/******  bb317c31-d950-4a4a-b3b4-5f6e9e264ee6  *******/
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('datePaiement', DateType::class, [
                'required' => true,
                'mapped' => false,
                'widget' => 'single_text',
                'label'   => 'Date de paiement',
                'format'  => 'dd/MM/yyyy',
                'html5' => false,
                'attr'    => ['autocomplete' => 'off', 'class' => 'datepicker no-auto'],
            ])
            ->add('montant', TextType::class, ['label' => 'Montant', 'mapped' => false, 'attr' => ['class' => 'input-money input-mnt']]);
        $builder->add('annuler', SubmitType::class, ['label' => 'Annuler', 'attr' => ['class' => 'btn btn-default btn-sm', 'data-bs-dismiss' => 'modal']])
        ->add('save', SubmitType::class, ['label' => 'Payer', 'attr' => ['class' => 'btn btn-primary btn-ajax btn-sm']]);
        $builder->get('montant')->addModelTransformer(new ThousandNumberTransformer());

            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Compte::class,
        ]);
    }
}
