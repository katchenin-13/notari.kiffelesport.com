<?php

namespace App\Form;

use App\Entity\Calendar;
use App\Entity\Client;
use App\Entity\Dossier;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Mpdf\Tag\TextArea;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NotBlank;

class CalendarType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('dossier', EntityType::class, [
                'required' => false,
                'class' => Dossier::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        // ->where('u.active = :val')
                        // ->setParameter('val', 1)
                        ->orderBy('u.id', 'DESC');
                },
                'label' => 'Acte conserné',
                'placeholder' => "Selectionner l'acte",
                'choice_label' => function ($dossier) {
                 
                        return 'Dossier N°' . $dossier->getNumeroOuverture() ;
                },
                'attr' => ['class' => 'form-control has-select2', 'id' => 'validationCustom05']

            ])
            ->add('start', DateTimeType::class, [
                "required" => false,
                "widget" => 'single_text',
                "input_format" => 'Y-m-d',
                "by_reference" => true,
                "empty_data" => new \DateTime(),
                'attr' => ['class' => 'start'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'La date de début ne doit pas être vide vous de devez renseigner les heures aussi'
                    ]),
                ]
            ])
            ->add('end', DateTimeType::class, [
                "required" => false,
                "widget" => 'single_text',
                "input_format" => 'Y-m-d',
                "by_reference" => true,
                "empty_data" => '',
                'attr' => ['class' => 'dateFin'],
                'constraints' => [
                    new NotBlank(
                        [
                            'message' => 'La date de fin ne doit pas être vide vous de devez renseigner les heures aussi'
                        ]
                    ),

                    new GreaterThan([
                        'propertyPath' => 'parent.all[start].data',
                        'message' => 'La date de fin ne doit pas être inférieure à la date de début',
                    ]),
                ]
            ])
            ->add('description', TextareaType::class);
        /* ->add('all_day', CheckboxType::class, [
                'label' => false,
                'required' => false,
            ])
            ->add('background_color', ColorType::class)
            ->add('border_color', ColorType::class)
            ->add('text_color', ColorType::class);*/
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Calendar::class,
        ]);
    }
}
