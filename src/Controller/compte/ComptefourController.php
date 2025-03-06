<?php

namespace App\Controller\Compte;


use App\Service\ActionRender;
use App\Service\FormError;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\DateTimeColumn;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTableFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Controller\BaseController;
use App\Controller\FileTrait;
use App\Entity\Comptefour;
use App\Entity\Marche;
use App\Form\ComptefourType;
use App\Form\CompteType;
use App\Repository\ComptefourRepository;
use App\Repository\LignepaiementmarcheRepository;
use App\Repository\MarcheRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

#[Route('/ads/compte/fournisseur')]
class ComptefourController extends BaseController
{
    use FileTrait;
    const INDEX_ROOT_NAME = 'app_compte_fournisseur_index';

    #[Route('/', name: 'app_compte_fournisseur_index', methods: ['GET', 'POST'], options: ['expose' => true])]
    public function index(Request $request, DataTableFactory $dataTableFactory): Response
    {

        $marche = $request->query->get('marche');
        $datedebut = $request->query->get('datedebut');
        $datefin = $request->query->get('datefin');
        
        $builder = $this->createFormBuilder(null, [
            'method' => 'GET',
            'action' => $this->generateUrl('app_compte_fournisseur_index', ['marche' => $marche, 'datedebut' => $datedebut, 'datefin' => $datefin]),
        ])
        
            ->add('marche', EntityType::class, [
                'class' => Marche::class,
                'choice_label' => function (Marche $marche) {
                    return $marche->getLibelle();
                },
                'label' => 'Marché',
                'placeholder' => '---',
                'required' => false,
                'attr' => ['class' => 'form-control-sm has-select2'],
                'choice_attr' => function (Marche $marche) {
                    return ['data-type' => $marche->getId()];
                },
            ])
            ->add('datedebut', DateType::class, [
                'widget' => 'single_text',
                'label'   => 'Date début',
                'format'  => 'dd/MM/yyyy',
                'required' => false,
                'html5' => false,
                'attr'    => ['autocomplete' => 'off', 'class' => 'form-control-sm datepicker no-auto'],
            ])
            ->add('datefin', DateType::class, [
                'widget' => 'single_text',
                'label'   => 'Date fin',
                'format'  => 'dd/MM/yyyy',
                'required' => false,
                'html5' => false,
                'attr'    => ['autocomplete' => 'off', 'class' => 'form-control-sm datepicker no-auto'],
            ])
            ;

        $permission = $this->menu->getPermissionIfDifferentNull($this->security->getUser()->getGroupe()->getId(), self::INDEX_ROOT_NAME);

        $table = $dataTableFactory->create()
            ->add('fournisseurs', TextColumn::class, ['label' => 'Fournisseur', 'field' => 'f.nom'])
            ->add('marches', TextColumn::class, ['label' => 'Marché', 'field' => 'm.libelle'])
            ->add('datecreation', DateTimeColumn::class,  ['label' => 'Date de creation ', 'format' => 'd/m/Y', 'searchable' => false])
            ->add('montant', TextColumn::class,  ['label' => 'Montant dû '])
            ->add('montantpaye', TextColumn::class, ['label' => 'Total payé', "searchable" => false, 'render' => function ($value, Comptefour $context) {
                $montantpaye = (float)$context->getMontant() - (float)$context->getSolde();
                return $montantpaye;
            }])

            ->add('solde', TextColumn::class,  ['label' => 'Solde '])

            ->createAdapter(ORMAdapter::class, [
                'entity' => Comptefour::class,
                'query' => function (QueryBuilder $qb) use ($marche, $datedebut,$datefin) {
                    $qb->select(['c', 'f', 'm'])
                        ->from(Comptefour::class, 'c')
                        ->join('c.fournisseurs', 'f')
                        ->join('c.marches', 'm')
                        ->orderBy('c.id ', 'DESC');

                if ($marche || $datedebut || $datefin) {
                   
                    if ($marche) {
                        $qb->andWhere('m.id = :marche')
                            ->setParameter('marche', $marche);
                    }
                    if ($datedebut != null && $datefin == null) {
                        try {
                            $new_date_debut = (new \DateTime($datedebut))->format('Y-m-d');

                            $qb->andWhere('c.datecreation = :dateDebut')
                            ->setParameter('dateDebut', $new_date_debut);
                        } catch (\Exception $e) {
                            // Gérez l'erreur si la date n'est pas au bon format
                        }
                    }

                    if ($datefin != null && $datedebut == null) {
                        try {
                            $new_date_fin = (new \DateTime($datefin))->format('Y-m-d');

                            $qb->andWhere('c.datecreation = :datefin')
                            ->setParameter('datefin', $new_date_fin);
                        } catch (\Exception $e) {
                            // Gérez l'erreur si la date n'est pas au bon format
                        }
                    }

                    if ($datedebut != null && $datefin != null) {
                        try {
                            $new_date_debut = (new \DateTime($datedebut))->format('Y-m-d');
                            $new_date_fin = (new \DateTime($datefin))->format('Y-m-d');

                            $qb->andWhere('c.datecreation BETWEEN :datedebut AND :datefin')
                            ->setParameter('datedebut', $new_date_debut)
                            ->setParameter('datefin', $new_date_fin);
                        } catch (\Exception $e) {
                            // Gérez l'erreur si la date n'est pas au bon format
                        }
                    }
                }
                }
            ])
            ->setName('dt_app_compte_fournisseur_' . $marche);
        if ($permission != null) {

            $renders = [
                // 'edit' => new ActionRender(function () use ($permission) {
                //     if ($permission == 'R') {
                //         return false;
                //     } elseif ($permission == 'RD') {
                //         return false;
                //     } elseif ($permission == 'RU') {
                //         return true;
                //     } elseif ($permission == 'CRUD') {
                //         return true;
                //     } elseif ($permission == 'CRU') {
                //         return true;
                //     } elseif ($permission == 'CR') {
                //         return false;
                //     }
                // }),
                'payer_load' => new ActionRender(function () use ($permission) {
                    if ($permission == 'R') {
                        return false;
                    } elseif ($permission == 'RD') {
                        return true;
                    } elseif ($permission == 'RU') {
                        return false;
                    } elseif ($permission == 'CRUD') {
                        return true;
                    } elseif ($permission == 'CRU') {
                        return false;
                    } elseif ($permission == 'CR') {
                        return false;
                    }
                }),
                // 'show' => new ActionRender(function () use ($permission) {
                //     if ($permission == 'R') {
                //         return true;
                //     } elseif ($permission == 'RD') {
                //         return true;
                //     } elseif ($permission == 'RU') {
                //         return true;
                //     } elseif ($permission == 'CRUD') {
                //         return true;
                //     } elseif ($permission == 'CRU') {
                //         return true;
                //     } elseif ($permission == 'CR') {
                //         return true;
                //     }
                // }),

            ];

            $gridId = $marche;
            $hasActions = false;

            foreach ($renders as $_ => $cb) {
                if ($cb->execute()) {
                    $hasActions = true;
                    break;
                }
            }

            if ($hasActions) {
                $table->add('id', TextColumn::class, [
                    'label' => 'Actions',
                    'orderable' => false,
                    'globalSearchable' => false,
                    'className' => 'grid_row_actions',
                    'render' => function ($value, Comptefour $context) use ($renders) {
                        $options = [
                            'default_class' => 'btn btn-xs btn-clean btn-icon mr-2 ',
                            'target' => '#exampleModalSizeLg2',


                            'actions' => [
                                // 'edit' => [
                                //     'target' => '#exampleModalSizeSm2',
                                //     'url' => $this->generateUrl('app_compte_fournisseur_edit', ['id' => $value]),
                                //     'ajax' => true,
                                //     'icon' => '%icon% bi bi-pen',
                                //     'attrs' => ['class' => 'btn-default'],
                                //     'render' => $renders['edit']
                                // ],
                                // 'show' => [
                                //     'url' => $this->generateUrl('app_compte_fournisseur_show', ['id' => $value]),
                                //     'ajax' => true,
                                //     'icon' => '%icon% bi bi-eye',
                                //     'attrs' => ['class' => 'btn-primary'],
                                //     'render' => $renders['show']
                                // ],
                                'payer_load' => [
                                    'target' => '#exampleModalSizeSm2',
                                    'url' => $this->generateUrl('app_config_fournisseur_paiement_index', ['id' => $value]),
                                    'ajax' => true,
                                    'stacked' => false,
                                    'icon' => '%icon% bi bi-cash',
                                    'attrs' => ['class' => 'btn-warning'],
                                    'render' => $renders['payer_load']
                                ],
                                // 'delete' => [
                                //     'target' => '#exampleModalSizeNormal',
                                //     'url' => $this->generateUrl('app_compte_fournisseur_delete', ['id' => $value]),
                                //     'ajax' => true,
                                //     'icon' => '%icon% bi bi-trash',
                                //     'attrs' => ['class' => 'btn-main'],
                                //     'render' => $renders['delete']
                                // ]
                            ]

                        ];
                        return $this->renderView('_includes/default_actions.html.twig', compact('options', 'context'));
                    }
                ]);
            }
        }

        $form = $builder->getForm();

        $table->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }


        return $this->render('compte/fournisseur/index.html.twig', [
            'datatable' => $table,
            'permition' => $permission,
            'form' => $form->createView(),
            'grid_id' => $gridId,
            'titre' => "Liste des  activités"
        ]);
    }

    #[Route('/new/new', name: 'app_compte_fournisseur_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, FormError $formError): Response
    {

        $titre = "Ajouter un événement";
        $comptefour = new Comptefour();
        $form = $this->createForm(ComptefourType::class, $comptefour, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_compte_fournisseur_new')
        ]);
        $form->handleRequest($request);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();
        // dd($form->getData());
        if ($form->isSubmitted()) {

            $response = [];
            $redirect = $this->generateUrl('app_config_parametre_compte_index');
            //$email = "";
            // if ($form->getData()->getClient()->getRaisonSocial() == "") {
            //     $email = $form->getData()->getClient()->getEmail();
            // } else {

            //     $email = $form->getData()->getClient()->getEmailEntreprise();
            // }

            // $identite = "";
            // //dd($form->getData()->getClient());
            // if ($form->getData()->getClient()->getRaisonSocial() == "") {
            //     $identite = $form->getData()->getClient()->getNom() . " " . $form->getData()->getClient()->getPrenom();
            // } else {

            //     $identite = $form->getData()->getClient()->getRaisonSocial();
            // }

            //$objet = $form->getData()->getDescription();
            if ($form->isValid()) {
                /*     $mailerService->send(
                    'INFORMATION CONCERNANT LE RENDEZ-VOUS',
                    'konatenvaly@gmail.com',
                    $email,
                    "_admin/contact/template.html.twig",
                    [
                        'message' =>  $objet,
                        'entreprise' =>  "Notari",
                        'identite' =>  $identite,
                        'telephone' =>  '0704314164'
                    ]
                );*/

                // $comptefour->setActive(1);

                $entityManager->persist($comptefour);
                $entityManager->flush();
                // 
                $data = true;
                $message = 'Opération effectuée avec succès';
                $statut = 1;
                $this->addFlash('success', $message);
            } else {
                $message = $formError->all($form);
                $statut = 0;
                $statutCode = 500;
                if (!$isAjax) {
                    $this->addFlash('warning', $message);
                }
            }


            if ($isAjax) {
                return $this->json(compact('statut', 'message', 'redirect', 'data'), $statutCode);
            } else {
                if ($statut == 1) {
                    return $this->redirect($redirect, Response::HTTP_OK);
                }
            }
        }

        return $this->renderForm('compte/fournisseur/new.html.twig', [
            'calendar' => $comptefour,
            'form' => $form,
            'titre' => $titre
        ]);
    }

    #[Route('/{id}/show', name: 'app_compte_fournisseur_show', methods: ['GET'])]
    public function show(Comptefour $comptefour): Response
    {
        return $this->render('compte/fournisseur/show.html.twig', [
            'comptefour' => $comptefour,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_compte_fournisseur_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Comptefour $comptefour, EntityManagerInterface $entityManager, FormError $formError): Response
    {

        $form = $this->createForm(CompteType::class, $comptefour, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_compte_fournisseur_edit', [
                'id' => $comptefour->getId()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_config_parametre_compte_index');


            if ($form->isValid()) {

                $entityManager->persist($comptefour);
                $entityManager->flush();

                $data = true;
                $message = 'Opération effectuée avec succès';
                $statut = 1;
                $this->addFlash('success', $message);
            } else {
                $message = $formError->all($form);
                $statut = 0;
                $statutCode = 500;
                if (!$isAjax) {
                    $this->addFlash('warning', $message);
                }
            }

            if ($isAjax) {
                return $this->json(compact('statut', 'message', 'redirect', 'data'), $statutCode);
            } else {
                if ($statut == 1) {
                    return $this->redirect($redirect, Response::HTTP_OK);
                }
            }
        }

        return $this->renderForm('compte/fournisseur/edit.html.twig', [
            'calendar' => $comptefour,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_compte_fournisseur_delete', methods: ['DELETE', 'GET'])]
    public function delete(Request $request, Comptefour $comptefour, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                    'app_compte_fournisseur_delete',
                    [
                        'id' => $comptefour->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = true;
            $entityManager->remove($comptefour);
            $entityManager->flush();

            $redirect = $this->generateUrl('app_config_parametre_compte_index');

            $message = 'Opération effectuée avec succès';

            $response = [
                'statut' => 1,
                'message' => $message,
                'redirect' => $redirect,
                'data' => $data
            ];

            $this->addFlash('success', $message);

            if (!$request->isXmlHttpRequest()) {
                return $this->redirect($redirect);
            } else {
                return $this->json($response);
            }
        }

        return $this->renderForm('compte/fournisseur/delete.html.twig', [
            'calendar' => $comptefour,
            'form' => $form,
        ]);
    }

    #[Route('/imprime/all/{marche}/{datedebut}/{datefin}/point des versements', name: 'app_compte_imprime_marche_all', methods: ['GET', 'POST'])]
    public function imprimeAllResult(Request $request, $marche, $datedebut, $datefin,ComptefourRepository $comptefourRepository,MarcheRepository $marcheRepository, LignepaiementmarcheRepository $compteFo,LignepaiementmarcheRepository $lignepaiementmarcheRepository){

        return $this->renderPdf(
            "compte/fournisseur/imprime.html.twig",[
            'data' => $lignepaiementmarcheRepository->searchResult($marche, $datedebut, $datefin),
            'datas' => $comptefourRepository->searchResultAll($marche),

        ],[
                'orientation' => 'p',
                'protected' => true,
                'file_name' => "point_versments",
                'format' => 'A4',

                'showWaterkText' => true,
                'fontDir' => [
                    $this->getParameter('font_dir') . '/arial',
                    $this->getParameter('font_dir') . '/trebuchet',
                ],
                'watermarkImg' => '',
                'entreprise' => '' 
            ],true);
    }

}
