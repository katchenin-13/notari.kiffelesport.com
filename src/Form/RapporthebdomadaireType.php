<?php

namespace App\Form;

use App\Entity\Rapporthebdomadaire;
use Mpdf\Tag\TextArea;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
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
            ->add('description', TextareaType::class, ['label' => 'Description'])
             ->add('fichier', FichierType::class, ['label' => 'Fichier joint', 'doc_options' => $options['doc_options'], 'required' => $options['doc_required'] ?? true, ])

        ;
            // ->add('utilisateur')
            // ->add('employe')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Rapporthebdomadaire::class,
            'doc_required' => true
        ]);

        $resolver->setRequired('doc_options');
        $resolver->setRequired('doc_required');
    }
}
