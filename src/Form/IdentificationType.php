<?php

namespace App\Form;

use App\Entity\Identification;
use App\Entity\Client;
use App\Entity\TypeClient;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class IdentificationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('type', EntityType::class, [
                'label' => false,
                'class' => TypeClient::class,
                'choice_label' => 'titre',
                'attr' => ['class' => 'form-control has-select2 type']
            ])
            ->add('clients', EntityType::class, [
                'label' => false,
                'class' => Client::class,
                'choice_label' => 'nom',
                'attr' => ['class' => 'form-control has-select2 client']
            ])
            ->add('attribut', TextType::class, ['label' => 'Attribut', 'label' => false, 'required' => false])
            ->add('montant',TextType::class,['label' => false,'attr' => ['class' => 'input-money input-mnt'],'empty_data' => '0'])
        ;
           
        


       
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Identification::class,
            'doc_required' => true,
            'doc_options' => [],
            'validation_groups' => [],
        ]);
        $resolver->setRequired('doc_options');
        $resolver->setRequired('doc_required');
        $resolver->setRequired(['validation_groups']);
    }
}
