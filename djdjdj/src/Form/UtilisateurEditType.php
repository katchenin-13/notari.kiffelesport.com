<?php

namespace App\Form;

use App\Entity\Groupe;
use App\Entity\Employe;
use App\Entity\Utilisateur;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Security\Core\Security;

class UtilisateurEditType extends AbstractType
{
    private $groupe;
    private $entreprise;
    public function __construct(Security $security){
        $this->groupe = $security->getUser()->getGroupe()->getCode();
        $this->entreprise = $security->getUser()->getEmploye()->getEntreprise();
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, ['label' => 'Pseudo'])

          ;
        if($this->groupe == "SADM") {
            $builder->add('groupe', EntityType::class, [
                'label'        => 'Groupe',
                'choice_label' => 'name',
                'multiple'     => false,
                'expanded'     => false,
                'placeholder' => 'Choisir un groupe',
                'attr' => ['class' => 'has-select2 form-select element'],
                'class'        => Groupe::class,
            ]);
        }else{
            $builder->add('groupe', EntityType::class, [
                'label'        => 'Groupe',
                'choice_label' => 'name',
                'multiple'     => false,
                'expanded'     => false,
                'placeholder' => 'Choisir un groupe',
                'query_builder' => function (EntityRepository $er)  {

                    return $er->createQueryBuilder('g')
                        /*->innerJoin('u.grouepe', 'g')*/
                        ->andWhere('g.code not in (:code)')
                        ->setParameter('code', 'SADM')
                        ;

                },
                'attr' => ['class' => 'has-select2 form-select element'],
                'class'        => Groupe::class,
            ]);
        }
            $builder->add('password', RepeatedType::class,
                [
                    'type'            => PasswordType::class,
                    'invalid_message' => 'Les mots de passe doivent être identiques.',
                    'required'        => $options['passwordRequired'],
                    'first_options'   => ['label' => 'Mot de passe'],
                    'second_options'  => ['label' => 'Répétez le mot de passe'],
                ]
            )
            /*->add('employe', EntityType::class,
            [
                'class' => Employe::class,
                'choice_label' => 'nomComplet',
                'query_builder' => function (EntityRepository $er) {
                    return $er->withoutAccount();
                }
            ]
        )*/
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
            'passwordRequired' => false
        ]);

        $resolver->setRequired('passwordRequired');
    }
}
