<?php

namespace App\Controller\Gestionfournisseur\Marche;

use App\Entity\Ligneversementfrais;
use App\Form\LigneversementfraisType;
use App\Repository\LigneversementfraisRepository;
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
use App\Entity\Compte;
<<<<<<< HEAD:src/Controller/Gestionfournisseur/Marche/LigneversementmarcheController.php
=======
use App\Entity\Comptefour;
use App\Entity\Lignepaiementmarche;
use App\Form\LignepaiementmarcheEditType;
use App\Form\LignepaiementmarcheEditTypeEditType;
use App\Form\LignepaiementmarcheType;
>>>>>>> 11b7eb5 (save pour review):src/Controller/Comptabilte/LignepaiementmarcheController.php
use App\Form\LigneVersementFaisEditType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
<<<<<<< HEAD:src/Controller/Gestionfournisseur/Marche/LigneversementmarcheController.php
#[Route('/ads/Gestionfournisseur/marche')]
class LigneversementmarcheController extends BaseController
=======
use Mpdf\Tag\Li;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

#[Route('/ads/comptabilte/lignepaiementmarche')]
class LignepaiementmarcheController extends BaseController
>>>>>>> 11b7eb5 (save pour review):src/Controller/Comptabilte/LignepaiementmarcheController.php
{
    const INDEX_ROOT_NAME = 'app_comptabilte_ligneversementfrais_index';

<<<<<<< HEAD:src/Controller/Gestionfournisseur/Marche/LigneversementmarcheController.php
 const TAB_ID = 'parametre-tabs';

   
public function getdata($idR){
        
}

 #[Route('/liste/paiement/{idR}', name: 'app_comptabilte_ligneversementfrais_index', methods: ['GET', 'POST'])]
    public function indexgg(Request $request, DataTableFactory $dataTableFactory, int $idR): Response
=======
    const TAB_ID = 'parametre-tabs';



    public function getdata($idR) {
        dd($idR);
    }


    #[Route('/liste/paiement/{idR}', name: 'app_comptabilte_lignepaiementmarche_index', methods: ['GET', 'POST'])]
    public function indexComptefour(Request $request, DataTableFactory $dataTableFactory, int $idR): Response
>>>>>>> 11b7eb5 (save pour review):src/Controller/Comptabilte/LignepaiementmarcheController.php
    {


        $table = $dataTableFactory->create()
<<<<<<< HEAD:src/Controller/Gestionfournisseur/Marche/LigneversementmarcheController.php
            ->add('compte', TextColumn::class, ['label' => 'N° Compte', 'field' => 'c.id'])
            ->add('dateversementfrais', DateTimeColumn::class, ['label' => 'Date de paiement', 'format' => 'd/m/Y'])
            ->add('montantverse', TextColumn::class, ['label' => 'Montant'])
            ->createAdapter(ORMAdapter::class, [
                'entity' => Ligneversementfrais::class,
                'query' => function (QueryBuilder $qb) use ($idR) {
                    $qb->select('l, c')
                    ->from(Ligneversementfrais::class, 'l')
                        ->join('l.compte', 'c')
=======
            ->add('comptefour', TextColumn::class, ['label' => 'N° Compte', 'field' => 'c.id'])
            ->add('datepaiement', DateTimeColumn::class, ['label' => 'Date de paiement', 'format' => 'd/m/Y'])
            ->add('montantverse', TextColumn::class, ['label' => 'Montant'])
            ->createAdapter(ORMAdapter::class, [
                'entity' => Lignepaiementmarche::class,
                'query' => function (QueryBuilder $qb) use ($idR) {
                    $qb->select('c,l')
                        ->from(Lignepaiementmarche::class, 'l')
                        ->join('l.comptefour', 'c')
                        // ->join('c.fournisseurs', 'f')
>>>>>>> 11b7eb5 (save pour review):src/Controller/Comptabilte/LignepaiementmarcheController.php
                        ->andWhere('c.id = :id')
                        ->setParameter('id', $idR);
                }
            ])
<<<<<<< HEAD:src/Controller/Gestionfournisseur/Marche/LigneversementmarcheController.php
            ->setName('dt_app_comptabilte_ligneversementfrais' . $idR);
=======
            ->setName('dt_app_comptabilte_lignepaiementmarche' . $idR);
>>>>>>> 11b7eb5 (save pour review):src/Controller/Comptabilte/LignepaiementmarcheController.php

        $renders = [
            'edit' =>  new ActionRender(function () {
                return true;
            }),
            'show' => new ActionRender(function () {
                return true;
            }),
            'delete' => new ActionRender(function () {
                return true;
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
                'render' => function ($value, Ligneversementfrais $context) use ($renders) {
                    $options = [
                        'default_class' => 'btn btn-xs btn-clean btn-icon mr-2 ',
                        'target' => '#modal-xl2',

                        'actions' => [
                            'edit' => [
                                'url' => $this->generateUrl('app_comptabilte_ligneversementfrais_edit', ['id' => $value]),
                                'ajax' => true,
                                'stacked' => true,
                                'icon' => '%icon% bi bi-pen',
                                'attrs' => ['class' => 'btn-default'],
                                'render' => $renders['edit']
                            ],
                            'show' => [
                                'url' => $this->generateUrl('app_comptabilte_ligneversementfrais_show', ['id' => $value]),
                                'ajax' => true,
                                   'stacked' => true,
                                'icon' => '%icon% bi bi-eye',
                                'attrs' => ['class' => 'btn-primary'],
                                'render' => $renders['show']
                            ],
                            'delete' => [
                                'target' => '#exampleModalSizeNormal',
                                'url' => $this->generateUrl('app_comptabilte_ligneversementfrais_delete', ['id' => $value]),
                                'ajax' => true,
                                   'stacked' => true,
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
        $table->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }

        return $this->render('comptabilte/ligneversementfrais/index.html.twig', [
            'datatable' => $table,
            'id' => $idR
        ]);
    }


<<<<<<< HEAD:src/Controller/Gestionfournisseur/Marche/LigneversementmarcheController.php
    
    #[Route('/{id}/new', name: 'app_comptabilte_ligneversementfrais_new', methods: ['GET', 'POST'])]
    public function new(Request $request, Compte $compte, LigneversementfraisRepository $ligneversementfraisRepository, EntityManagerInterface $entityManager, FormError $formError): Response
    {

        $form = $this->createForm(LigneversementfraisType::class, $compte, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_comptabilte_ligneversementfrais_new', [
                'id' => $compte->getId()
=======

    #[Route('/{id}/lignepaiementmarche/new', name: 'app_comptabilte_lignepaiementmarche_new', methods: ['GET', 'POST'])]
   // #[ParamConverter('comptefour', class: Comptefour::class, options: ['id' => 'id'])]
    public function new(Request $request, Comptefour $comptefour, LignepaiementmarcheRepository $lignepaiementmarcheRepository, EntityManagerInterface $entityManager, FormError $formError): Response
    {
       
        $form = $this->createForm(LignepaiementmarcheType::class, $comptefour, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_comptabilte_lignepaiementmarche_new', [
                'id' => $comptefour->getId()
>>>>>>> 11b7eb5 (save pour review):src/Controller/Comptabilte/LignepaiementmarcheController.php
            ])
        ]);

        $data = null;
        $url = null;
        $tabId = null;
        $statut = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = '';

            $montant = (int) $form->get('montant')->getData();
            $date = $form->get('datePaiement')->getData();
            $somme = 0;


<<<<<<< HEAD:src/Controller/Gestionfournisseur/Marche/LigneversementmarcheController.php
            $lignes = $ligneversementfraisRepository->findBy(['compte' => $compte->getId()]);
=======
            $lignes = $lignepaiementmarcheRepository->findBy(['comptefour' => $comptefour->getId()]);
>>>>>>> 11b7eb5 (save pour review):src/Controller/Comptabilte/LignepaiementmarcheController.php

            if ($lignes) {

                foreach ($lignes as $key => $info) {
                    $somme += (int)$info->getMontantverse();
<<<<<<< HEAD:src/Controller/Gestionfournisseur/Marche/LigneversementmarcheController.php
                    $resteAPayer = abs((int)$compte->getMontant() - $somme);
                }
            } else {
                $resteAPayer = abs((int)$compte->getMontant());
=======
                    $resteAPayer = abs((int)$comptefour->getMontant() - $somme);
                }
            } else {
                $resteAPayer = abs((int)$comptefour->getMontant());
>>>>>>> 11b7eb5 (save pour review):src/Controller/Comptabilte/LignepaiementmarcheController.php
            }


            if ($form->isValid()) {

                if ($resteAPayer >= $montant) {

<<<<<<< HEAD:src/Controller/Gestionfournisseur/Marche/LigneversementmarcheController.php
                    $ligneversementfrai = new Ligneversementfrais();
                    $ligneversementfrai->setDateversementfrais($date);
                    $ligneversementfrai->setCompte($compte);
                    $ligneversementfrai->setMontantverse($montant);
                    $entityManager->persist($ligneversementfrai);
                    $entityManager->flush();

                    $compte->setSolde($resteAPayer);

                    $entityManager->persist($compte);
=======
                    $lignepaiementmarche = new Lignepaiementmarche();
                    $lignepaiementmarche->setDatepaiement($date);
                    $lignepaiementmarche->setComptefour($comptefour);
                    $lignepaiementmarche->setMontantverse($montant);
                    $entityManager->persist($lignepaiementmarche);
                    $entityManager->flush();

                    $comptefour->setSolde($resteAPayer);

                    $entityManager->persist($comptefour);
>>>>>>> 11b7eb5 (save pour review):src/Controller/Comptabilte/LignepaiementmarcheController.php
                    $entityManager->flush();

                    $load_tab = true;
                    $statut = 1;

                    $message = sprintf('Opération effectuée avec succès');
                    $this->addFlash('success', $message);
                } else {

                    $statut = 0;

                    $message = sprintf('Désole échec de paiement car le montant  saisi est superieur au montant  [%s] qui reste à payer pour un montant total de  %s', $montant, $resteAPayer);
                    $this->addFlash('danger', $message);
                }


                $url = [
                    'url' => $this->generateUrl('app_config_frais_paiement_index', [
<<<<<<< HEAD:src/Controller/Gestionfournisseur/Marche/LigneversementmarcheController.php
                        'id' => $compte->getId()
=======
                        'id' => $comptefour->getId()
>>>>>>> 11b7eb5 (save pour review):src/Controller/Comptabilte/LignepaiementmarcheController.php
                    ]),
                    'tab' => '#module0',
                    'current' => '#module0'
                ];

                $tabId = self::TAB_ID;
                $redirect = $url['url'];

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
                return $this->json(compact('statut', 'message', 'redirect', 'data', 'url', 'tabId'), $statutCode);
            } else {
                if ($statut == 1) {
                    return $this->redirect($redirect, Response::HTTP_OK);
                }
            }
        }

<<<<<<< HEAD:src/Controller/Gestionfournisseur/Marche/LigneversementmarcheController.php
        return $this->renderForm('comptabilte/ligneversementfrais/new.html.twig', [
            // 'ligneversementfrai' => $ligneversementfrai,
            'compte' => $compte,
=======
        return $this->renderForm('comptabilte/lignepaiementmarche/new.html.twig', [
            //'lignepaiementmarche' => $lignepaiementmarche,
>>>>>>> 11b7eb5 (save pour review):src/Controller/Comptabilte/LignepaiementmarcheController.php
            'form' => $form,
            'comptefour' => $comptefour,

        ]);
    }

<<<<<<< HEAD:src/Controller/Gestionfournisseur/Marche/LigneversementmarcheController.php
    #[Route('/{id}/edit', name: 'app_comptabilte_ligneversementfrais_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Compte $compte, LigneversementfraisRepository $ligneversementfraisRepository, Ligneversementfrais $ligneversementfrai, EntityManagerInterface $entityManager, FormError $formError): Response
    {

        $form = $this->createForm(LigneversementfraisType::class, $compte, [
=======
    #[Route('/{id}/edit', name: 'app_comptabilte_lignepaiementmarche_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Lignepaiementmarche $lignepaiementmarche, LignepaiementmarcheRepository $lignepaiementmarcheRepository, EntityManagerInterface $entityManager, FormError $formError): Response
    {

        $form = $this->createForm(LignepaiementmarcheEditType::class, $lignepaiementmarche, [
>>>>>>> 11b7eb5 (save pour review):src/Controller/Comptabilte/LignepaiementmarcheController.php
            'method' => 'POST',
            'action' => $this->generateUrl('app_comptabilte_ligneversementfrais_edit', [
                'id' => $compte->getId()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
<<<<<<< HEAD:src/Controller/Gestionfournisseur/Marche/LigneversementmarcheController.php
            $redirect = $this->generateUrl('app_comptabilte_ligneversementfrais_index', [ 'id' => $ligneversementfrai->getCompte()->getId() ]);

            $montant = (int) $form->get('montant')->getData();
            $date = $form->get('datePaiement')->getData();
            $somme = 0;


            $lignes = $ligneversementfraisRepository->findBy(['compte' => $compte->getId()]);
=======
            $redirect = '';
            // $redirect = $this->generateUrl('app_comptabilte_lignepaiementmarche_index', [ 'id' => $ligneversementfrai->getCompte()->getId() ]);
            $montant = (int) $form->get('montantverse')->getData();
            $date = $form->get('datepaiement')->getData();
            $somme = 0;


            $lignes = $lignepaiementmarcheRepository->findBy(['comptefour' => $lignepaiementmarche->getComptefour()->getId()]);
>>>>>>> 11b7eb5 (save pour review):src/Controller/Comptabilte/LignepaiementmarcheController.php

            if ($lignes) {

                foreach ($lignes as $key => $info) {
                    $somme += (int)$info->getMontantverse();
<<<<<<< HEAD:src/Controller/Gestionfournisseur/Marche/LigneversementmarcheController.php
                    $resteAPayer = abs((int)$compte->getMontant() - $somme);
                }
            } else {
                $resteAPayer = abs((int)$compte->getMontant());
=======
                    $resteAPayer = abs((int)$lignepaiementmarche->getComptefour()->getMontant() - $somme);
                }
            } else {
                $resteAPayer = abs((int)$lignepaiementmarche->getComptefour()->getMontant());
>>>>>>> 11b7eb5 (save pour review):src/Controller/Comptabilte/LignepaiementmarcheController.php
            }


            if ($form->isValid()) {


                if ($resteAPayer >= $montant) {

<<<<<<< HEAD:src/Controller/Gestionfournisseur/Marche/LigneversementmarcheController.php
               

                    $compte->setSolde($resteAPayer);
=======
                    $lignepaiementmarche->setDatepaiement($date);
                    $lignepaiementmarche->setMontantverse($montant);

                    $entityManager->persist($lignepaiementmarche);
                    $entityManager->flush();

                    $comptefour = $lignepaiementmarche->getComptefour();
                    $comptefour->setSolde($resteAPayer);
>>>>>>> 11b7eb5 (save pour review):src/Controller/Comptabilte/LignepaiementmarcheController.php

                    $entityManager->persist($comptefour);
                    $entityManager->flush();
<<<<<<< HEAD:src/Controller/Gestionfournisseur/Marche/LigneversementmarcheController.php

                    $load_tab = true;
                    $statut = 1;

                    $message = sprintf('Opération effectuée avec succès');
                    $this->addFlash('success', $message);
                } else {

                    $statut = 0;

                    $message = sprintf('Désole échec de paiement car le montant  saisi est superieur au montant  [%s] qui reste à payer pour un montant total de  %s', $montant, $resteAPayer);
                    $this->addFlash('danger', $message);
                }


                $url = [
                    'url' => $this->generateUrl('app_config_frais_paiement_index', [
                        'id' => $compte->getId()
=======
                }
                $entityManager->persist($lignepaiementmarche);
                $entityManager->flush();

                $data = true;
                $message = 'Opération effectuée avec succès';
                $statut = 1;
                $this->addFlash('success', $message);
                $url = [
                    'url' => $this->generateUrl('app_config_frais_paiement_index', [
                        'id' => $lignepaiementmarche->getComptefour()->getId()
>>>>>>> 11b7eb5 (save pour review):src/Controller/Comptabilte/LignepaiementmarcheController.php
                    ]),
                    'tab' => '#module0',
                    'current' => '#module0'
                ];

                $tabId = self::TAB_ID;
                $redirect = $url['url'];

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

        return $this->renderForm('comptabilte/ligneversementfrais/edit.html.twig', [
            'compte' => $compte,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/show', name: 'app_comptabilte_ligneversementfrais_show', methods: ['GET'])]
    public function show(Ligneversementfrais $ligneversementfrai): Response
    {
        return $this->render('comptabilte/ligneversementfrais/show.html.twig', [
            'ligneversementfrai' => $ligneversementfrai,
        ]);
    }


    #[Route('/{id}/delete', name: 'app_comptabilte_ligneversementfrais_delete', methods: ['DELETE', 'GET'])]
    public function delete(Request $request, Ligneversementfrais $ligneversementfrai, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                    'app_comptabilte_ligneversementfrais_delete',
                    [
                        'id' => $ligneversementfrai->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = true;
            $entityManager->remove($ligneversementfrai);
            $entityManager->flush();

            $redirect = $this->generateUrl('app_comptabilte_ligneversementfrais_index');

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

        return $this->renderForm('comptabilte/ligneversementfrais/delete.html.twig', [
            'ligneversementfrai' => $ligneversementfrai,
            'form' => $form,
        ]);
    }
}
