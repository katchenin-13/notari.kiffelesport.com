<?php

namespace App\Controller\Client;

use App\Entity\Client;
use App\Form\Client1Type;
use App\Repository\ClientRepository;
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
use App\Form\ClientType;
use Doctrine\ORM\QueryBuilder;

#[Route('/ads/client/client')]
class ClientController extends BaseController
{
    const INDEX_ROOT_NAME = 'app_client_client_index';

    #[Route('/{type}', name: 'app_client_client_index', methods: ['GET', 'POST'])]
    public function index(Request $request, DataTableFactory $dataTableFactory, $type): Response
    {
        
        if ($type == 'P') {
            $titre = 'Liste des particuliers';
        } else {

            $titre = 'Liste des entreprises';
        }

        $permission = $this->menu->getPermissionIfDifferentNull($this->security->getUser()->getGroupe()->getId(), self::INDEX_ROOT_NAME);

        if ($type == 'P') {
            $table = $dataTableFactory->create()
                ->add('nom', TextColumn::class, ['label' => 'Nom'])
                ->add('prenom', TextColumn::class, ['label' => 'Prenoms'])
                ->add('email', TextColumn::class, ['label' => 'Addresse email'])
                ->add('profession', TextColumn::class, ['label' => 'Profession'])
                ->add('telPortable', TextColumn::class, ['label' => 'Téléphone'])
                ->createAdapter(ORMAdapter::class, [
                    'entity' => Client::class,
                    'query' => function (QueryBuilder $qb) use ($type) {
                        $qb->select(['p', 'type'])
                            ->from(Client::class, 'p')
                            ->join('p.type_client', 'type');

                        if ($type) {
                            if ($type) {
                                $qb->andWhere('type.code = :type')
                                    ->setParameter('type', $type);
                            }
                        }
                    }
                ])
                ->setName('dt_app_client_client' . $type);
        } else {
            //dd($type);
            $table = $dataTableFactory->create()
                ->add('raisonSocial', TextColumn::class, ['label' => 'RAISON SOCIALE'])
                ->add('registreCommercial', TextColumn::class, ['label' => 'REGISTRE DE COMMERCE'])
                ->add('boitePostal', TextColumn::class, ['label' => 'BOITE POSTALE'])
                ->add('siteWeb', TextColumn::class, ['label' => 'SITE WEB'])
                ->add('emailEntreprise', TextColumn::class, ['label' => 'ADRESSE E-MAIL'])
                ->createAdapter(ORMAdapter::class, [
                    'entity' => Client::class,
                    'query' => function (QueryBuilder $qb) use ($type) {
                        $qb->select(['p', 'type', 'en'])
                            ->from(Client::class, 'p')
                            ->join('p.type_client', 'type')
                            ->join('p.entreprise', 'en');

                        /* if ($type) { */
                            if ($type) {
                                $qb->andWhere('type.code = :type')
                                    ->setParameter('type', $type);
                            }
                        /* } */

                        if ($this->groupe != "SADM") {
                            $qb->andWhere('en = :entreprise')
                                ->setParameter('entreprise', $this->entreprise);
                        }
                    }
                ])
                ->setName('dt_app_client_client' . $type);
        }


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
                    'label' => 'Actions', 'orderable' => false, 'globalSearchable' => false, 'className' => 'grid_row_actions', 'render' => function ($value, Client $context) use ($renders) {
                        $options = [
                            'default_class' => 'btn btn-xs btn-clean btn-icon mr-2 ',
                            'target' => '#exampleModalSizeLg2',


                            'actions' => [
                                'edit' => [
                                    'target' => '#exampleModalSizeSm2',
                                    'url' => $this->generateUrl('app_client_client_edit', ['id' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-pen', 'attrs' => ['class' => 'btn-default'], 'render' => $renders['edit']
                                ],
                                'show' => [
                                    'url' => $this->generateUrl('app_client_client_show', ['id' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-eye', 'attrs' => ['class' => 'btn-primary'], 'render' => $renders['show']
                                ],
                                'delete' => [
                                    'target' => '#exampleModalSizeNormal',
                                    'url' => $this->generateUrl('app_client_client_delete', ['id' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-trash', 'attrs' => ['class' => 'btn-main'], 'render' => $renders['delete']
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


        return $this->render('client/client/index.html.twig', [
            'datatable' => $table,
            'permition' => $permission,
            'type' => $type,
            'titre' => $titre,
        ]);
    }



    #[Route('/{id}/show', name: 'app_client_client_show', methods: ['GET'])]
    public function show(Client $client): Response
    {
        return $this->render('client/client/show.html.twig', [
            'client' => $client,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_client_client_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Client $client, EntityManagerInterface $entityManager, FormError $formError): Response
    {
        $validationGroups = ['Default', 'FileRequired', 'autre'];
        $form = $this->createForm(ClientType::class, $client, [
            'method' => 'POST',
            'doc_options' => [
                'uploadDir' => $this->getUploadDir(self::UPLOAD_PATH, true),
                'attrs' => ['class' => 'filestyle'],
            ],
            'validation_groups' => $validationGroups,
            'action' => $this->generateUrl('app_client_client_edit', [
                'id' => $client->getId()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_config_parametre_client_index');


            if ($form->isValid()) {

                $entityManager->persist($client);
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

        return $this->renderForm('client/client/edit.html.twig', [
            'client' => $client,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_client_client_delete', methods: ['DELETE', 'GET'])]
    public function delete(Request $request, Client $client, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                    'app_client_client_delete',
                    [
                        'id' => $client->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = true;
            $entityManager->remove($client);
            $entityManager->flush();

            $redirect = $this->generateUrl('app_client_client_index');

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

        return $this->renderForm('client/client/delete.html.twig', [
            'client' => $client,
            'form' => $form,
        ]);
    }
}
