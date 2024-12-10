<?php

namespace App\Controller\Comptabilte;

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
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
#[Route('/ads/comptabilte/ligneversementfrais')]
class LigneversementfraisController extends BaseController
{
    const INDEX_ROOT_NAME = 'app_comptabilte_ligneversementfrais_index';

 const TAB_ID = 'parametre-tabs';

   
public function getdata($idR){
        
}

 #[Route('/liste/paiement/{idR}', name: 'app_comptabilte_ligneversementfraiss_index', methods: ['GET', 'POST'])]
    public function indexListeVersement(Request $request, DataTableFactory $dataTableFactory, int $idR): Response
    {
      

        $table = $dataTableFactory->create()
            ->add('compte', TextColumn::class, ['label' => 'N° Compte', 'field' => 'c.id'])
            ->add('dateversementfrais', DateTimeColumn::class, ['label' => 'Date de paiement', 'format' => 'd/m/Y'])
            ->add('montantverse', TextColumn::class, ['label' => 'Montant'])
            ->createAdapter(ORMAdapter::class, [
                'entity' => Ligneversementfrais::class,
                'query' => function (QueryBuilder $qb) use ($idR) {
                    $qb->select('l, c')
                    ->from(Ligneversementfrais::class, 'l')
                        ->join('l.compte', 'c')
                        ->andWhere('c.id = :id')
                        ->setParameter('id', $idR);
                }
            ])
            ->setName('dt_app_comptabilte_ligneversementfrais' . $idR);

        // Gestion des actions

        $renders = [
                'edit' =>  new ActionRender(function () {
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
                        'target' => '#exampleModalSizeLg2',

                        'actions' => [
                            'edit' => [
                                'url' => $this->generateUrl('app_comptabilte_ligneversementfrais_edit', ['id' => $value]),
                                'ajax' => true,
                                'icon' => '%icon% bi bi-pen',
                                'attrs' => ['class' => 'btn-default'],
                                'render' => $renders['edit']
                            ],
                            'delete' => [
                                'target' => '#exampleModalSizeNormal',
                                'url' => $this->generateUrl('app_comptabilte_ligneversementfrais_delete', ['id' => $value]),
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
        $table->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }

        return $this->render('comptabilte/ligneversementfrais/index.html.twig', [
            'datatable' => $table,
            'id' => $idR
        ]);
    
}
    #[Route('/{id}/new', name: 'app_comptabilte_ligneversementfrais_edit', methods: ['GET', 'POST'])]
    public function new(Request $request, Compte $compte,LigneversementfraisRepository $ligneversementfraisRepository,  EntityManagerInterface $entityManager, FormError $formError): Response
    {

        $form = $this->createForm(LigneversementfraisType::class, $compte, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_comptabilte_ligneversementfrais_edit', [
                'id' => $compte->getId()
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

            
            $lignes = $ligneversementfraisRepository->findBy(['compte' => $compte->getId()]);
      
            if ($lignes) {

                foreach ($lignes as $key => $info) {
                    $somme += (int)$info->getMontantverse();
                    $resteAPayer = abs((int)$compte->getMontant() - $somme);
                }
            } else {
                $resteAPayer = abs((int)$compte->getMontant());
            }


            if ($form->isValid()) {

                if ($resteAPayer >= $montant) {
                   
                    $ligneversementfrais = new Ligneversementfrais();
                    $ligneversementfrais->setDateversementfrais($date);
                    $ligneversementfrais->setCompte($compte);
                    $ligneversementfrais->setMontantverse($montant);
                    $entityManager->persist($ligneversementfrais);
                    $entityManager->flush();

                    $compte->setSolde($resteAPayer);

                    $entityManager->persist($compte);   
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
                        'id' => $compte->getId()
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

        return $this->renderForm('comptabilte/ligneversementfrais/edit.html.twig', [
            // 'ligneversementfrais' => $ligneversementfrais,
            'form' => $form,
           
        ]);
    }


    #[Route('/new', name: 'app_infoinscription_info_inscription_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, FormError $formError): Response
    {
        $infoInscription = new InfoInscription();
        $form = $this->createForm(InfoInscriptionType::class, $infoInscription, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_infoinscription_info_inscription_new')
        ]);
        $form->handleRequest($request);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_infoinscription_info_inscription_index');




            if ($form->isValid()) {

                $entityManager->persist($infoInscription);
                $entityManager->flush();

                $data = true;
                $message       = 'Opération effectuée avec succès';
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

        return $this->render('infoinscription/info_inscription/new.html.twig', [
            'info_inscription' => $infoInscription,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/show', name: 'app_infoinscription_info_inscription_show', methods: ['GET'])]
    public function show(InfoInscription $infoInscription): Response
    {
        return $this->render('infoinscription/info_inscription/show.html.twig', [
            'info_inscription' => $infoInscription,
        ]);
    }
    const TAB_ID = 'parametre-tabs';
    #[Route('/{id}/edit', name: 'app_infoinscription_info_inscription_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        InfoInscription $infoInscription,
        EntityManagerInterface $entityManager,
        FormError $formError,
        InscriptionRepository $inscriptionRepository,
        EcheancierRepository $echeancierRepository,
        Service $service
    ): Response {

        $form = $this->createForm(InfoInscriptionVersementAdminType::class, $infoInscription, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_infoinscription_info_inscription_edit', [
                'id' =>  $infoInscription->getId()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();

        $oldMontant = (int)$infoInscription->getMontant();

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            /*  $redirect = $this->generateUrl('app_infoinscription_info_inscription_index', [
                'id' => $infoInscription->getInscription()->getId()
            ]); */
            /*  $redirect = $this->generateUrl('app_inscription_liste_versement_index', [
                'id' => $infoInscription->getInscription()->getId()
            ]); */

            $new_montant = (int)$form->get('montant')->getData();
            $inscription = $infoInscription->getInscription();


            if ($form->isValid()) {



                $entityManager->persist($infoInscription);
                $entityManager->flush();

                if ($oldMontant != $new_montant) {
                    $service->paiementInscriptionEdit($inscription);
                }

                $url = [
                    'url' => $this->generateUrl('app_inscription_liste_versement_index', [
                        'id' => $infoInscription->getInscription()->getId()
                    ]),
                    'tab' => '#module1',
                    'current' => '#module1'
                ];

                $tabId = self::TAB_ID;
                $redirect = $url['url'];

                $data = true;
                $load_tab = true;
                $message       = 'Opération effectuée avec succès';
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

        return $this->render('infoinscription/info_inscription/edit.html.twig', [
            'info_inscription' => $infoInscription,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: 'app_infoinscription_info_inscription_delete', methods: ['DELETE', 'GET'])]
    public function delete(Request $request, InfoInscription $infoInscription, InscriptionRepository $inscriptionRepository, EcheancierRepository $echeancierRepository, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                    'app_infoinscription_info_inscription_delete',
                    [
                        'id' => $infoInscription->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $inscription = $infoInscription->getInscription();
            $data = true;
            $entityManager->remove($infoInscription);
            $entityManager->flush();

            $service->paiementInscriptionEdit($inscription);



            /*   $redirect = $this->generateUrl('app_inscription_liste_versement_index', [
                'id' => $infoInscription->getInscription()->getId()
            ]);
 */
            $message = 'Opération effectuée avec succès';

            $url = [
                'url' => $this->generateUrl('app_inscription_liste_versement_index', [
                    'id' => $infoInscription->getInscription()->getId()
                ]),
                'tab' => '#module1',
                'current' => '#module1'
            ];

            $tabId = self::TAB_ID;
            $redirect = $url['url'];


            $response = [
                'statut'   => 1,
                'message'  => $message,
                'redirect' => $redirect,
                'data' => $data,
                'url' => $url,
                'tabId' => $tabId
            ];

            $this->addFlash('success', $message);

            if (!$request->isXmlHttpRequest()) {
                return $this->redirect($redirect);
            } else {
                return $this->json($response);
            }
        }

        return $this->render('infoinscription/info_inscription/delete.html.twig', [
            'info_inscription' => $infoInscription,
            'form' => $form,
        ]);
    }

}
