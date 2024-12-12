<?php

namespace App\Controller\Comptabilte;


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
use App\Entity\CompteFournisseur;
use App\Entity\Lignepaiementmarche;
use App\Form\LignepaiementmarcheEditType;
use App\Form\LignepaiementmarcheEditTypeEditType;
use App\Form\LignepaiementmarcheType;
use App\Form\LigneVersementFaisEditType;
use App\Repository\LignepaiementmarcheRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Mpdf\Tag\Li;

#[Route('/ads/comptabilte/lignepaiementmarche')] 
class LignepaiementmarcheController extends BaseController
{
    const INDEX_ROOT_NAME = 'app_comptabilte_lignepaiementmarche_index';

 const TAB_ID = 'parametre-tabs';

   


    #[Route('/liste/paiement/{idM}', name: 'app_comptabilte_lignepaiementmarche_index', methods: ['GET', 'POST'])]
    public function indexCompteFournisseur(Request $request, DataTableFactory $dataTableFactory, int $idM): Response
    {
      

        $table = $dataTableFactory->create()
            ->add('comptefournisseurs', TextColumn::class, ['label' => 'N° Compte', 'field' => 'c.id'])
            ->add('datepaiement', DateTimeColumn::class, ['label' => 'Date de paiement', 'format' => 'd/m/Y'])
            ->add('montantverse', TextColumn::class, ['label' => 'Montant'])
            ->createAdapter(ORMAdapter::class, [
                'entity' => Lignepaiementmarche::class,
                'query' => function (QueryBuilder $qb) use ($idM) {
                    $qb->select('c,l')
                    ->from(Lignepaiementmarche::class, 'l')
                        ->join('l.comptefournisseurs', 'c')
                        // ->join('c.fournisseurs', 'f')
                        ->andWhere('c.id = :id')
                        ->setParameter('id', $idM);
                }
            ])
            ->setName('dt_app_comptabilte_lignepaiementmarche' . $idM);

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
                'render' => function ($value, Lignepaiementmarche $context) use ($renders) {
                    $options = [
                        'default_class' => 'btn btn-xs btn-clean btn-icon mr-2 ',
                        'target' => '#modal-xl2',

                        'actions' => [
                            'edit' => [
                                'url' => $this->generateUrl('app_comptabilte_lignepaiementmarche_edit', ['id' => $value]),
                                'ajax' => true,
                                'stacked' => true,
                                'icon' => '%icon% bi bi-pen',
                                'attrs' => ['class' => 'btn-default'],
                                'render' => $renders['edit']
                            ],
                            'show' => [
                                'url' => $this->generateUrl('app_comptabilte_lignepaiementmarche_show', ['id' => $value]),
                                'ajax' => true,
                                'stacked' => true,
                                'icon' => '%icon% bi bi-eye',
                                'attrs' => ['class' => 'btn-primary'],
                                'render' => $renders['show']
                            ],
                            'delete' => [
                                'target' => '#exampleModalSizeNormal',
                                'url' => $this->generateUrl('app_comptabilte_lignepaiementmarche_delete', ['id' => $value]),
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

        return $this->render('comptabilte/lignepaiementmarche/index.html.twig', [
            'datatable' => $table,
            'id' => $idM
        ]);
    }


    
    #[Route('/{id}/new', name: 'app_comptabilte_lignepaiementmarche_new', methods: ['GET', 'POST'])]
    public function new(Request $request,CompteFournisseur $compteFournisseur,Lignepaiementmarche $lignepaiementmarche, LignepaiementmarcheRepository $lignepaiementmarcheRepository, EntityManagerInterface $entityManager, FormError $formError): Response
    {

        $form = $this->createForm(LignepaiementmarcheType::class, $compteFournisseur, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_comptabilte_lignepaiementmarche_new', [
                'id' => $compteFournisseur->getId()
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


            $lignes = $lignepaiementmarcheRepository->findBy(['compte' => $compteFournisseur->getId()]);

            if ($lignes) {

                foreach ($lignes as $key => $info) {
                    $somme += (int)$info->getMontantverse();
                    $resteAPayer = abs((int)$compteFournisseur->getMontant() - $somme);
                }
            } else {
                $resteAPayer = abs((int)$compteFournisseur->getMontant());
            }


            if ($form->isValid()) {


                if ($resteAPayer >= $montant) {

                    $ligneversementfrai = new Lignepaiementmarche();
                    $ligneversementfrai->setDatepaiement($date);
                    $ligneversementfrai->setComptefournisseurs($compteFournisseur);
                    $ligneversementfrai->setMontantverse($montant);
                    $entityManager->persist($ligneversementfrai);
                    $entityManager->flush();

                    $compteFournisseur->setSolde($resteAPayer);

                    $entityManager->persist($compteFournisseur);
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
                        'id' => $compteFournisseur->getId()
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

        return $this->renderForm('comptabilte/lignepaiementmarche/new.html.twig', [
            // 'ligneversementfrai' => $ligneversementfrai,
            'compteFournisseur' => $compteFournisseur,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_comptabilte_lignepaiementmarche_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Lignepaiementmarche $lignepaiementmarche,LignepaiementmarcheRepository $lignepaiementmarcheRepository, EntityManagerInterface $entityManager, FormError $formError): Response
    {
       
        $form = $this->createForm(LignepaiementmarcheEditType::class, $lignepaiementmarche, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_comptabilte_lignepaiementmarche_edit', [
                'id' => $lignepaiementmarche->getId()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = '';
            // $redirect = $this->generateUrl('app_comptabilte_lignepaiementmarche_index', [ 'id' => $ligneversementfrai->getCompte()->getId() ]);
            $montant = (int) $form->get('montantverse')->getData();
            $date = $form->get('dateversementfrais')->getData();
            $somme = 0;
           

            $lignes = $lignepaiementmarcheRepository->findBy(['compte' => $lignepaiementmarche->getComptefournisseurs()->getId()]);

            if ($lignes) {

                foreach ($lignes as $key => $info) {
                    $somme += (int)$info->getMontantverse();
                    $resteAPayer = abs((int)$lignepaiementmarche->getComptefournisseurs()->getMontant() - $somme);
                }
            } else {
                $resteAPayer = abs((int)$lignepaiementmarche->getComptefournisseurs()->getMontant());    
            }

            if ($form->isValid()) {

                if ($resteAPayer >= $montant) {

                    $lignepaiementmarche->setDatepaiement($date);
                    $lignepaiementmarche->setMontantverse($montant);
                
                    $entityManager->persist($lignepaiementmarche);
                    $entityManager->flush();

                    $compte = $lignepaiementmarche->getComptefournisseurs();
                    $compte->setSolde($resteAPayer);

                    $entityManager->persist($compte);
                    $entityManager->flush();
                }
                $entityManager->persist($lignepaiementmarche);  
                $entityManager->flush();

                $data = true;
                $message = 'Opération effectuée avec succès';
                $statut = 1;
                $this->addFlash('success', $message);
                 $url = [
                    'url' => $this->generateUrl('app_config_frais_paiement_index', [
                        'id' => $lignepaiementmarche->getComptefournisseurs()->getId()
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

        return $this->renderForm('comptabilte/lignepaiementmarche/edit.html.twig', [
            'lignepaiementmarche' => $lignepaiementmarche,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/show', name: 'app_comptabilte_lignepaiementmarche_show', methods: ['GET'])]
    public function show(Lignepaiementmarche $lignepaiementmarche): Response
    {
        return $this->render('comptabilte/lignepaiementmarche/show.html.twig', [
            'lignepaiementmarche' => $lignepaiementmarche,
        ]);
    }


    #[Route('/{id}/delete', name: 'app_comptabilte_lignepaiementmarche_delete', methods: ['DELETE', 'GET'])]
    public function delete(Request $request, Lignepaiementmarche $lignepaiementmarche, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                    'app_comptabilte_lignepaiementmarche_delete',
                    [
                        'id' => $lignepaiementmarche->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = true;
            $entityManager->remove($lignepaiementmarche);
            $entityManager->flush();

            $redirect = $this->generateUrl('app_comptabilte_lignepaiementmarche_index');

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

        return $this->renderForm('comptabilte/lignepaiementmarche/delete.html.twig', [
            'lignepaiementmarche' => $lignepaiementmarche,
            'form' => $form,
        ]);
    } 

}
