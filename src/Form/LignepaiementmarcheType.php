<?php

namespace App\Form;

<<<<<<< HEAD
<<<<<<< HEAD
=======
use App\Entity\Comptefour;
use App\Form\DataTransformer\ThousandNumberTransformer;
>>>>>>> 11b7eb5 (save pour review)
=======
use App\Entity\CompteFournisseur;
use App\Form\DataTransformer\ThousandNumberTransformer;
>>>>>>> b6f1842a7fec2506df675de17037826c2c1327b4
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class LignepaiementmarcheType extends AbstractType
{
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
        $builder->get('montant')->addModelTransformer(new ThousandNumberTransformer());;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
<<<<<<< HEAD
<<<<<<< HEAD
            // Configure your form options here
=======
            'data_class' => Comptefour::class,

>>>>>>> 11b7eb5 (save pour review)
=======
            'data_class' => CompteFournisseur::class,

>>>>>>> b6f1842a7fec2506df675de17037826c2c1327b4
        ]);
    }
}
