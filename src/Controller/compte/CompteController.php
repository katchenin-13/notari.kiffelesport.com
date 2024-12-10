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
use App\Entity\Compte;
use App\Form\CalendarType;
use App\Form\CompteType;
use Doctrine\ORM\QueryBuilder;

#[Route('/ads/compte/frais')]
class CompteController extends BaseController
{
    const INDEX_ROOT_NAME = 'app_compte_frais_index';

    #[Route('/', name: 'app_compte_frais_index', methods: ['GET', 'POST'])]
    public function index(Request $request, DataTableFactory $dataTableFactory): Response
    {


        $permission = $this->menu->getPermissionIfDifferentNull($this->security->getUser()->getGroupe()->getId(), self::INDEX_ROOT_NAME);

        $table = $dataTableFactory->create()
            ->add('client', TextColumn::class, ['label' => 'Client','field'=> 'cl.nom'])
            ->add('dossier', TextColumn::class, ['label' => 'N° Dossier', 'field' => 'd.numeroOuverture'])
            ->add('datecreation', DateTimeColumn::class,  ['label' => 'Date de creation ', 'format' => 'd/m/Y', 'searchable' => false])
            ->add('montant', TextColumn::class,  ['label' => 'Montant dû '])
            ->add('montantpaye', TextColumn::class, ['label' => 'Total payé', "searchable" => false, 'render' => function ($value, Compte $context) {
                $montantpaye = (float)$context->getMontant() - (float)$context->getSolde();
                return $montantpaye ;
            }])

            ->add('solde', TextColumn::class,  ['label' => 'Solde '])

            ->createAdapter(ORMAdapter::class, [
                'entity' => Compte::class,
                'query' => function (QueryBuilder $qb)  {
                    $qb->select(['c',])
                        ->from(Compte::class, 'c')
                        ->join('c.client', 'cl')
                        ->join('c.dossier', 'd')
                        ->orderBy('c.id ', 'DESC');

                    
                }
            ])
            ->setName('dt_app_compte_frais');
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

        $table->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }


        return $this->render('compte/frais/index.html.twig', [
            'datatable' => $table,
            'permition' => $permission,
           
            'titre' => "Liste des  activités"
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
                $compte->setActive(1);

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
}
