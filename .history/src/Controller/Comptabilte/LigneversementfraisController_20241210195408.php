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

 #[Route('/liste/paiement/{idR}', name: 'app_comptabilte_ligneversementfrais_index', methods: ['GET', 'POST'])]
    public function indexgg(Request $request, DataTableFactory $dataTableFactory, int $idR): Response
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
                        'target' => '#exampleModalSizeLg2',

                        'actions' => [
                            'edit' => [
                                'url' => $this->generateUrl('app_comptabilte_ligneversementfrais_edit', ['id' => $value]),
                                'ajax' => true,
                                'icon' => '%icon% bi bi-pen',
                                'attrs' => ['class' => 'btn-default'],
                                'render' => $renders['edit']
                            ],
                            'show' => [
                                'url' => $this->generateUrl('app_comptabilte_ligneversementfrais_show', ['id' => $value]),
                                'ajax' => true,
                                'icon' => '%icon% bi bi-eye',
                                'attrs' => ['class' => 'btn-primary'],
                                'render' => $renders['show']
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

    
     #[Route('/{id}/edit', name: 'app_comptabilte_ligneversementfrais_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Ligneversementfrais $ligneversementfrai, EntityManagerInterface $entityManager, FormError $formError): Response
    {

        $form = $this->createForm(LigneversementfraisType::class, $ligneversementfrai, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_comptabilte_ligneversementfrais_edit', [
                'id' => $ligneversementfrai->getId()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_comptabilte_ligneversementfraiss_index', [ 'id' => $ligneversementfrai->getCompte()->getId() ]);


            if ($form->isValid()) {

                $entityManager->persist($ligneversementfrai);
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

        return $this->renderForm('comptabilte/ligneversementfrais/edit.html.twig', [
            'ligneversementfrai' => $ligneversementfrai,
            'form' => $form,
        ]);
    }


    #[Route('/{id}/edit', name: 'app_comptabilte_ligneversementfrais_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Ligneversementfrais $ligneversementfrai, EntityManagerInterface $entityManager, FormError $formError): Response
    {

        $form = $this->createForm(LigneversementfraisType::class, $ligneversementfrai, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_comptabilte_ligneversementfrais_edit', [
                'id' => $ligneversementfrai->getId()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_comptabilte_ligneversementfraiss_index', [ 'id' => $ligneversementfrai->getCompte()->getId() ]);


            if ($form->isValid()) {

                $entityManager->persist($ligneversementfrai);
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

        return $this->renderForm('comptabilte/ligneversementfrais/edit.html.twig', [
            'ligneversementfrai' => $ligneversementfrai,
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
