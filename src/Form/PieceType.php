<?php

namespace App\Form;

use App\Entity\Client;
use App\Entity\DocumentTypeActe;
use App\Entity\DocumentTypeClient;
use App\Entity\Piece;
use App\Form\DataTransformer\ThousandNumberTransformer;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class PieceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
           
        ->add('client', EntityType::class, [
            'label' => false,
            'class' => Client::class,
            'choice_label' => 'nom',
            'attr' => ['class' => 'form-control has-select2']
        ])
        ->add('type', EntityType::class, [
            'label' => false,
            'class' => DocumentTypeClient::class,
            'choice_label' => 'getLibelle',
           'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->innerJoin("c.typesdocuments","t")
                      ;
                },
            'attr' => ['class' => 'form-control has-select2']
        ])
        ->add('path', FichierType::class, ['label' => false,  'doc_options' => $options['doc_options'], 'required' => $options['doc_required'] ?? true])
       /*  ->add(
            'montant',
            TextType::class,
            [
                'label' => false,
                'attr' => ['class' => 'input-money input-mnt'], 'empty_data' => '0',
            ]
        ) */
        ->add('attribut', TextType::class, ['label' => 'Attribut', 'label' => false, 'required' => false]);
        //$builder->get('montant')->addModelTransformer(new ThousandNumberTransformer());
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Piece::class,
            'doc_required' => true
        ]);
        $resolver->setRequired('doc_options');
        $resolver->setRequired('doc_required');
    }
}
