<?php

namespace App\Controller\Actes;

use App\Entity\ActeConstitution;
use App\Form\ActeConstitution1Type;
use App\Repository\ActeConstitutionRepository;
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
use App\Form\ActeConstitutionType;
use Doctrine\ORM\QueryBuilder;

#[Route('/ads/actes/acte/constitution')]
class ActeConstitutionController extends BaseController
{
    use FileTrait;
    const INDEX_ROOT_NAME = 'app_actes_acte_constitution_index';

    #[Route('/{etat}', name: 'app_actes_acte_constitution_index', methods: ['GET', 'POST'])]
    public function index(Request $request, DataTableFactory $dataTableFactory, $etat): Response
    {


        $permission = $this->menu->getPermissionIfDifferentNull($this->security->getUser()->getGroupe()->getId(), self::INDEX_ROOT_NAME);


        $table = $dataTableFactory->create()
            ->add('numero', TextColumn::class, ['label' => 'Numéro'])
            ->add('dateCreation', TextColumn::class, ['label' => 'Date création'])
            ->add('objet', TextColumn::class, ['label' => 'Objet'])
            ->add('etat', TextColumn::class, ['label' => 'Etape'])
            ->createAdapter(ORMAdapter::class, [
                'entity' => ActeConstitution::class,
                'query' => function (QueryBuilder $qb) use ($etat) {
                    $qb->select(['p'])
                        ->from(ActeConstitution::class, 'p')
                        ->join('p.entreprise', 'en')
                        ->andWhere('p.etat = :etat')
                        ->setParameter('etat', $etat);


                    if ($this->groupe != "SADM") {
                        $qb->andWhere('en = :entreprise')
                            ->setParameter('entreprise', $this->entreprise);
                    }
                }
            ])
            ->setName('dt_app_actes_acte_constitution' . $etat);

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
                    'label' => 'Actions', 'orderable' => false, 'globalSearchable' => false, 'className' => 'grid_row_actions', 'render' => function ($value, ActeConstitution $context) use ($renders) {
                        $options = [
                            'default_class' => 'btn btn-xs btn-clean btn-icon mr-2 ',
                            'target' => '#exampleModalSizeLg2',

                            'actions' => [
                                'target' => '#exampleModalSizeSm2',
                                'edit' => [
                                    'url' => $this->generateUrl('app_actes_acte_constitution_edit', ['id' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-pen', 'attrs' => ['class' => 'btn-default'], 'render' => $renders['edit']
                                ],
                                'show' => [
                                    'url' => $this->generateUrl('app_actes_acte_constitution_show', ['id' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-eye', 'attrs' => ['class' => 'btn-primary'], 'render' => $renders['show']
                                ],
                                'delete' => [
                                    'target' => '#exampleModalSizeNormal',
                                    'url' => $this->generateUrl('app_actes_acte_constitution_delete', ['id' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-trash', 'attrs' => ['class' => 'btn-main'], 'render' => $renders['delete']
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


        return $this->render('actes/acte_constitution/index.html.twig', [
            'datatable' => $table,
            'permition' => $permission,
            'etat' => $etat
        ]);
    }

    #[Route('/new/new', name: 'app_actes_acte_constitution_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, FormError $formError): Response
    {
        $validationGroups = ['Default', 'FileRequired', 'oui'];
        $acteConstitution = new ActeConstitution();
        $form = $this->createForm(ActeConstitutionType::class, $acteConstitution, [
            'method' => 'POST',
            'doc_options' => [
                'uploadDir' => $this->getUploadDir(self::UPLOAD_PATH, true),
                'attrs' => ['class' => 'filestyle'],
            ],
            'validation_groups' => $validationGroups,
            'action' => $this->generateUrl('app_actes_acte_constitution_new')
        ]);
        $form->handleRequest($request);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_config_parametre_constitution_index');


            if ($form->isValid()) {
                $acteConstitution->setEtat('cree');
                $entityManager->persist($acteConstitution);
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

        return $this->renderForm('actes/acte_constitution/new.html.twig', [
            'acte_constitution' => $acteConstitution,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/show', name: 'app_actes_acte_constitution_show', methods: ['GET'])]
    public function show(ActeConstitution $acteConstitution): Response
    {
        return $this->render('actes/acte_constitution/show.html.twig', [
            'acte_constitution' => $acteConstitution,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_actes_acte_constitution_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ActeConstitution $acteConstitution, EntityManagerInterface $entityManager, FormError $formError): Response
    {
        $validationGroups = ['Default', 'FileRequired', 'autre'];
        $form = $this->createForm(ActeConstitutionType::class, $acteConstitution, [
            'method' => 'POST',
            'doc_options' => [
                'uploadDir' => $this->getUploadDir(self::UPLOAD_PATH, true),
                'attrs' => ['class' => 'filestyle'],
            ],
            'validation_groups' => $validationGroups,
            'action' => $this->generateUrl('app_actes_acte_constitution_edit', [
                'id' => $acteConstitution->getId()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_config_parametre_constitution_index');


            if ($form->isValid()) {

                $entityManager->persist($acteConstitution);
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

        return $this->renderForm('actes/acte_constitution/edit.html.twig', [
            'acte_constitution' => $acteConstitution,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_actes_acte_constitution_delete', methods: ['DELETE', 'GET'])]
    public function delete(Request $request, ActeConstitution $acteConstitution, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                    'app_actes_acte_constitution_delete',
                    [
                        'id' => $acteConstitution->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = true;
            $entityManager->remove($acteConstitution);
            $entityManager->flush();

            $redirect = $this->generateUrl('app_actes_acte_constitution_index');

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

        return $this->renderForm('actes/acte_constitution/delete.html.twig', [
            'acte_constitution' => $acteConstitution,
            'form' => $form,
        ]);
    }
}
