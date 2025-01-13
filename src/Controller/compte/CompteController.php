<?php

namespace App\Controller\Compte;

use App\Entity\Calendar;
use App\Form\Calendar1Type;
use App\Repository\CalendarRepository;
use App\Service\ActionRender;
use App\Service\FormError;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\BoolColumn;
use Omines\DataTablesBundle\Column\DateTimeColumn;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTableFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Controller\BaseController;
use App\Controller\FileTrait;
use App\Entity\Client;
use App\Entity\Compte;
use App\Entity\Dossier;
use App\Form\CalendarType;
use App\Form\CompteType;
use App\Repository\CompteRepository;
use App\Repository\LigneversementfraisRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

#[Route('/ads/compte/frais')]
class CompteController extends BaseController
{
    use FileTrait;
    const INDEX_ROOT_NAME = 'app_compte_frais_index';

    #[Route('/', name: 'app_compte_frais_index', methods: ['GET', 'POST'], options: ['expose' => true])]
    public function index(Request $request, DataTableFactory $dataTableFactory): Response
    {
        $dossier = $request->query->get('dossier');
        $datedebut = $request->query->get('datedebut');
        $datefin = $request->query->get('datefin');
        $builder = $this->createFormBuilder(null, [
            'method' => 'GET',
            'action' => $this->generateUrl(self::INDEX_ROOT_NAME, ['dossier' => $dossier, 'datedebut' => $datedebut, 'datefin' => $datefin]),
        ])

            ->add('dossier', EntityType::class, [
                'class' => Client::class,
                'choice_label' => function (Client $dossier) {
                //   return 'Dossier N°_' . $dossier->getNumeroOuverture();
                $typeClient = $dossier->getTypeClient(); 
                if ($typeClient && $typeClient->getCode() === 'P') { // Si le type est "P"
                    return $dossier->getNom() . ' ' . $dossier->getPrenom();
                } elseif ($typeClient && $typeClient->getCode() === 'E') { // Si le type est "E"
                    return $dossier->getNom();
                }
                return 'N/A'; 
                },
            'choice_attr' => function (Client $user) {
                return ['data-type' => $user->getId()];
            },
                'label' => 'Client conserné',
                'placeholder' => '---',
                'required' => false,
                'attr' => ['class' => 'form-control-sm has-select2']
            ])

            // ->add('dossier',EntityType::class,[
            //     'class' => Dossier::class,
            //     'choice_label'=> function(Dossier $dossier){
            //         return 'Dossier N°_' . $dossier->getNumeroOuverture();
            //     },
            //     'label'   => 'Dossier',
            //     'placeholder' => '---',
            //     'required' => false,
            //     'attr' => ['class' => 'form-control-sm has-select2']
            // ])

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
            ]);


        $permission = $this->menu->getPermissionIfDifferentNull($this->security->getUser()->getGroupe()->getId(), self::INDEX_ROOT_NAME);

        $table = $dataTableFactory->create()
            ->add('client', TextColumn::class, ['label' => 'Client', 'field' => 'cl.nom'])
            // ->add('dossier', TextColumn::class, ['label' => 'Objet du dossier', 'field' => 'd.objet'])
            ->add('datecreation', DateTimeColumn::class,  ['label' => 'Date de creation ', 'format' => 'd/m/Y', 'searchable' => false])
            ->add('montant', TextColumn::class,  ['label' => 'Montant dû '])
            ->add('montantpaye', TextColumn::class, ['label' => 'Total payé', "searchable" => false, 'render' => function ($value, Compte $context) {
                $montantpaye = (float)$context->getMontant() - (float)$context->getSolde();
                return $montantpaye;
            }])
            ->add('solde', TextColumn::class,  ['label' => 'Solde '])
            ->createAdapter(ORMAdapter::class, [
                'entity' => Compte::class,
                'query' => function (QueryBuilder $qb) use ($dossier, $datedebut, $datefin) {
                    $qb->select(['c','cl',])
                        ->from(Compte::class, 'c')
                        ->join('c.client', 'cl')
                        // ->join('cl.identifications','i')
                        // ->join('i.dossier','d' )
                        ->orderBy('c.id ', 'DESC');
               
               
                    if ($dossier || $datedebut || $datefin) {
                    if ($dossier) {
                        $qb->andWhere('cl.id = :dossier')
                            ->setParameter('dossier', $dossier);
                    }

                    // if ($dossier) {
                    //     $qb ->innerJoin('cl.identification','i')
                    //     ->innerJoin('i.dossier','d' )
                    //     ->andWhere('d = :dossier')
                    //         ->setParameter('dossier', $dossier);
                    // }

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
            ->setName('dt_app_compte_frais_' . $dossier);
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

            $gridId = $dossier;

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
                    'render' => function ($value, Compte $context) use ($renders) {
                        $options = [
                            'default_class' => 'btn btn-xs btn-clean btn-icon mr-2 ',
                            'target' => '#exampleModalSizeLg2',


                            'actions' => [
                                // 'edit' => [
                                //     'target' => '#exampleModalSizeSm2',
                                //     'url' => $this->generateUrl('app_compte_frais_edit', ['id' => $value]),
                                //     'ajax' => true,
                                //     'icon' => '%icon% bi bi-pen',
                                //     'attrs' => ['class' => 'btn-default'],
                                //     'render' => $renders['edit']
                                // ],
                                // 'show' => [
                                //     'url' => $this->generateUrl('app_compte_frais_show', ['id' => $value]),
                                //     'ajax' => true,
                                //     'icon' => '%icon% bi bi-eye',
                                //     'attrs' => ['class' => 'btn-primary'],
                                //     'render' => $renders['show']
                                // ],
                                'payer_load' => [
                                    'target' => '#exampleModalSizeSm2',
                                    'url' => $this->generateUrl('app_config_frais_paiement_index', ['id' => $value]),
                                    'ajax' => true,
                                    'stacked' => false,
                                    'icon' => '%icon% bi bi-cash',
                                    'attrs' => ['class' => 'btn-warning'],
                                    'render' => $renders['payer_load']
                                ],
                                // 'delete' => [
                                //     'target' => '#exampleModalSizeNormal',
                                //     'url' => $this->generateUrl('app_compte_frais_delete', ['id' => $value]),
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
        $form = $builder->getForm()->createView();
        $table->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }


        return $this->render('compte/frais/index.html.twig', [
            'datatable' => $table,
            'permition' => $permission,
            'titre' => "Liste des  activités",
            'form' => $form,
            'grid_id' => $gridId
        ]);
    }

    #[Route('/new/new', name: 'app_compte_frais_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, FormError $formError): Response
    {

        $titre = "Ajouter un événement";
        $compte = new Compte();
        $form = $this->createForm(CompteType::class, $compte, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_compte_frais_new')
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
                // $compte->setActive(1);

                $entityManager->persist($compte);
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

        return $this->renderForm('compte/frais/new.html.twig', [
            'calendar' => $compte,
            'form' => $form,
            'titre' => $titre
        ]);
    }

    #[Route('/{id}/show', name: 'app_compte_frais_show', methods: ['GET'])]
    public function show(Compte $compte): Response
    {
        return $this->render('compte/frais/show.html.twig', [
            'compte' => $compte,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_compte_frais_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Compte $compte, EntityManagerInterface $entityManager, FormError $formError): Response
    {

        $form = $this->createForm(CompteType::class, $compte, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_compte_frais_edit', [
                'id' => $compte->getId()
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

                $entityManager->persist($compte);
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

        return $this->renderForm('compte/frais/edit.html.twig', [
            'calendar' => $compte,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_compte_frais_delete', methods: ['DELETE', 'GET'])]
    public function delete(Request $request, Compte $compte, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                    'app_compte_frais_delete',
                    [
                        'id' => $compte->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = true;
            $entityManager->remove($compte);
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

        return $this->renderForm('compte/frais/delete.html.twig', [
            'calendar' => $compte,
            'form' => $form,
        ]);
    }

    /**
     * @throws MpdfException
     */
    #[Route('/imprime/all/{dossier}/{datedebut}/{datefin}/point des versements', name: 'app_compte_imprime_all1', methods: ['GET', 'POST'])]
    public function imprimerAll(Request $request, $dossier, $datedebut, $datefin, CompteRepository $compteRepository, LigneversementfraisRepository $ligneversementfraisRepository): Response
    {

       

      
$totalImpaye=0;
$total=0;
$totalPayer=0;
        $compteClient = $compteRepository->findOneBy(['client' => $dossier]);

        if ($compteClient) {
            $totalPayer  = $compteClient->getSolde();
            $total  = $compteClient->getMontant();
            $totalImpaye = $total - $totalPayer  ;        
        }

        //dd($totalPayer,$total,$totalImpaye);



        //dd($dateNiveau);
        return $this->renderPdf("compte/frais/imprime.html.twig", [
            'total_payer' => $totalPayer,
            'datas' => $compteRepository->searchResultAll($dossier),
            'data' => $ligneversementfraisRepository->searchResult($dossier, $datedebut, $datefin),
            'total_impaye' => $totalImpaye
            //'data_info'=>$infoPreinscriptionRepository->findOneByPreinscription($preinscription)
        ], [
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
        ], true);
        //return $this->renderForm("stock/sortie/imprime.html.twig");

    }


    // /**
    //  * @throws MpdfException
    //  */

    // // #[Route('/imprime/etat/dossier', name: 'app_actes_dossier_imprime', methods: ['GET', 'POST'], options: ['expose' => true], condition: "request.query.has('filters')")]
    // #[Route('/imprime/all', name: 'app_compte_imprime_all', methods: ['GET'])]
    // public function imprimerEtatDossier(
    //     $etat = null,
    //     $clair = null,
    //     CompteRepository $compteRepository
    // ): Response {

    //     $dossiers = $compteRepository->getListeDossierNative($clair);
    //     $employe = $compteRepository->getEmployeNomPrenom($clair);

    //     $dataArray = [];
    //     foreach ($dossiers as $key => $liste) {
    //         $dataArray[] = [
    //             'numeroOuverture' => $liste['numero_ouverture'],
    //             'numcompte' => $liste['numcompte'],
    //             'dateCreation' => $liste['date_creation'],
    //             'objet' => $liste['objet'],
    //             'employe' => $liste['employe_nom_prenom'],
    //             'nature' => $liste['nature_dossier'],
    //             'typeActe' => $liste['type_acte_nom'],
    //             'etape' => $liste['etape'],
    //         ];
    //     }

    //     return $this->renderPdf('actes/dossier/imprime.html.twig', [
    //         'datas' => $dataArray,
    //         'emploi' => $employe,
    //         'date' => new \DateTime(),
    //         'entreprise' => ' ',
    //         'employe' => $employe
    //     ], [
    //         'orientation' => 'P',
    //         'protected' => true,
    //         'format' => 'A4',
    //         'showWaterkText' => true,
    //         'fontDir' => [
    //             $this->getParameter('font_dir') . '/arial',
    //             $this->getParameter('font_dir') . '/trebuchet',
    //         ],
    //         'watermarkImg' => '',
    //     ]);
    // }
}
