<?php

namespace App\Controller\Gestionfournisseur\Marche;

use App\Entity\Marche;
use App\Form\MarcheType;
use App\Repository\MarcheRepository;
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
use App\Controller\BaseController;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

#[Route('/ads/gestionfournisseur/marche')]
class MarcheController extends BaseController
{
    const INDEX_ROOT_NAME = 'app_gestionfournisseur_marche_index';

    #[Route('/', name: 'app_gestionfournisseur_marche_index', methods: ['GET', 'POST'])]
    public function index(Request $request, DataTableFactory $dataTableFactory): Response
    {


        $permission = $this->menu->getPermissionIfDifferentNull($this->security->getUser()->getGroupe()->getId(), self::INDEX_ROOT_NAME);

        $table = $dataTableFactory->create()
            ->add('fournisseur', TextColumn::class, ['label' => 'Fournisseur','field' => 'f.nom' ])
            ->add('datecreation', DateTimeColumn::class, ['label' => 'Date de creation', 'format' => 'd/m/Y'])
            ->add('libelle', TextColumn::class, ['label' => 'Libelle'])
            ->add('montanttotal', TextColumn::class, ['label' => 'Montant Total'])
            ->add('solde', TextColumn::class, ['label' => 'Solde'])
            ->add('montantpaye', TextColumn::class, [
                'label' => 'Total payé',
                "searchable" => false,
                'render' => function ($value, Marche $context) {
                    $montantTotal = $context->getMontanttotal() ? floatval(str_replace(',', '.', $context->getMontanttotal())) : 0;
                    $solde = $context->getSolde() ? floatval(str_replace(',', '.', $context->getSolde())) : 0;

                    $montantPaye = $montantTotal - $solde;

                    return number_format($montantPaye, 2, ',', ' ');
                }
            ])

            ->createAdapter(ORMAdapter::class, [
                'entity' => Marche::class,
            'query' => function (QueryBuilder $qb) {
                $qb->select('m, f')
                    ->from(Marche::class, 'm')
                    ->join('m.fournisseur', 'f')
                    ;                
            }
        ])
            ->setName('dt_app_gestionfournisseur_marche');
        if ($permission != null) {

            $renders = [
                'edit' => new ActionRender(function () use ($permission) {
                    if ($permission == 'R') {
                        return false;
                    } elseif ($permission == 'RD') {
                        return false;
                    } elseif ($permission == 'RU') {
                        return true;
                    } elseif ($permission == 'CRUD') {
                        return true;
                    } elseif ($permission == 'CRU') {
                        return true;
                    } elseif ($permission == 'CR') {
                        return false;
                    }
                }),
                'delete' => new ActionRender(function () use ($permission) {
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
                'show' => new ActionRender(function () use ($permission) {
                    if ($permission == 'R') {
                        return true;
                    } elseif ($permission == 'RD') {
                        return true;
                    } elseif ($permission == 'RU') {
                        return true;
                    } elseif ($permission == 'CRUD') {
                        return true;
                    } elseif ($permission == 'CRU') {
                        return true;
                    } elseif ($permission == 'CR') {
                        return true;
                    }
                }),

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
                    'render' => function ($value, Marche $context) use ($renders) {
                        $options = [
                            'default_class' => 'btn btn-xs btn-clean btn-icon mr-2 ',
                            'target' => '#exampleModalSizeLg2',

                            'actions' => [
                                'edit' => [
                                    'url' => $this->generateUrl('app_gestionfournisseur_marche_edit', ['id' => $value]),
                                    'ajax' => true,
                                    'icon' => '%icon% bi bi-pen',
                                    'attrs' => ['class' => 'btn-default'],
                                    'render' => $renders['edit']
                                ],
                                'show' => [
                                    'url' => $this->generateUrl('app_gestionfournisseur_marche_show', ['id' => $value]),
                                    'ajax' => true,
                                    'icon' => '%icon% bi bi-eye',
                                    'attrs' => ['class' => 'btn-primary'],
                                    'render' => $renders['show']
                                ],
                                'delete' => [
                                    'target' => '#exampleModalSizeNormal',
                                    'url' => $this->generateUrl('app_gestionfournisseur_marche_delete', ['id' => $value]),
                                    'ajax' => true,
                                    'icon' => '%icon% bi bi-trash',
                                    'attrs' => ['class' => 'btn-main'],
                                    'render' => $renders['delete']
                                ]
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


        return $this->render('gestionfournisseur/marche/index.html.twig', [
            'datatable' => $table,
            'permition' => $permission
        ]);
    }

    #[Route('/new', name: 'app_gestionfournisseur_marche_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, FormError $formError): Response
    {
        $marche = new Marche();
        
        $form = $this->createForm(MarcheType::class, $marche, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_gestionfournisseur_marche_new')
        ]);
        $form->handleRequest($request);
       
        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();
   
        if ($form->isSubmitted()) {
        
            $response = [];
            $redirect = $this->generateUrl('app_gestionfournisseur_marche_index');


            if ($form->isValid()) {
               if ($marche->getMontanttotal() != null) {
                   $marche->setSolde($marche->getMontanttotal());
                # code...
               }else{
                   throw new \Exception("Le montant total est obligatoire");
               }
                $entityManager->persist($marche);
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

        return $this->renderForm('gestionfournisseur/marche/new.html.twig', [
            'marche' => $marche,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/show', name: 'app_gestionfournisseur_marche_show', methods: ['GET'])]
    public function show(Marche $marche): Response
    {
        return $this->render('gestionfournisseur/marche/show.html.twig', [
            'marche' => $marche,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_gestionfournisseur_marche_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Marche $marche, EntityManagerInterface $entityManager, FormError $formError): Response
    {

        $form = $this->createForm(MarcheType::class, $marche, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_gestionfournisseur_marche_edit', [
                'id' => $marche->getId()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_gestionfournisseur_marche_index');


            if ($form->isValid()) {

                $entityManager->persist($marche);
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

        return $this->renderForm('gestionfournisseur/marche/edit.html.twig', [
            'marche' => $marche,
            'form' => $form,
        ]);
    }

    //la methode pour faire un versement sur la facture du marche

    #[Route('/{id}/edit', name: 'app_gestionfournisseur_marche_edit', methods: ['GET', 'POST'])]
    public function (Request $request, Marche $marche, EntityManagerInterface $entityManager, FormError $formError): Response
    {

        $form = $this->createForm(MarcheType::class, $marche, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_gestionfournisseur_marche_edit', [
                'id' => $marche->getId()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_gestionfournisseur_marche_index');


            if ($form->isValid()) {

                $entityManager->persist($marche);
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

        return $this->renderForm('gestionfournisseur/marche/edit.html.twig', [
            'marche' => $marche,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_gestionfournisseur_marche_delete', methods: ['DELETE', 'GET'])]
    public function delete(Request $request, Marche $marche, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                    'app_gestionfournisseur_marche_delete',
                    [
                        'id' => $marche->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = true;
            $entityManager->remove($marche);
            $entityManager->flush();

            $redirect = $this->generateUrl('app_gestionfournisseur_marche_index');

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

        return $this->renderForm('gestionfournisseur/marche/delete.html.twig', [
            'marche' => $marche,
            'form' => $form,
        ]);
    }
}
