<?php

namespace App\Form;

use App\Entity\Fournisseur;
use PHPUnit\TextUI\XmlConfiguration\CodeCoverage\Report\Text;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class FournisseurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom',TextType::class, ['attr' => [
                'label' => 'Raison sociale',
                'class' => 'form-control', 
                'placeholder' => 'Raison sociale'
                ]])
            ->add('adresse',TextType::class, ['attr' => [
                'label' => 'Adresse', 
                'class' => 'form-control', 
                'placeholder' => 'Adresse'
                ]])
            ->add('contact',TextType::class, ['attr' => [
                'label' => 'Contact', 
                'class' => 'form-control', 
                'placeholder' => 'Contact'
                ]])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Fournisseur::class,
        ]);
    }
}
