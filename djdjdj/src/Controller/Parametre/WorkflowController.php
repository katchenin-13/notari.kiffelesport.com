<?php

namespace App\Controller\Parametre;

use App\Entity\Workflow;
use App\Form\WorkflowType;
use App\Repository\WorkflowRepository;
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
use App\Entity\GestionWorkflow;
use App\Entity\Type;
use App\Form\GestionWorkflowType;
use App\Repository\TypeRepository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

#[Route('/ads/parametre/workflow')]
class WorkflowController extends BaseController
{
    const INDEX_ROOT_NAME = 'app_parametre_workflow_index';

    #[Route('/', name: 'app_parametre_workflow_index', methods: ['GET', 'POST'])]
    public function index(Request $request, DataTableFactory $dataTableFactory): Response
    {


        $permission = $this->menu->getPermissionIfDifferentNull($this->security->getUser()->getGroupe()->getId(), self::INDEX_ROOT_NAME);
        /* 
        $type = $request->query->get('type');

        //dd($type);

        $builder = $this->createFormBuilder(null, [
            'method' => 'GET',
            'action' => $this->generateUrl('app_parametre_workflow_index', compact('type'))
        ])->add('type', EntityType::class, [
            'class' => Type::class,
            'label' => 'Type acte',
            'choice_label' => 'titre',
            'attr' => ['class' => 'form-control has-select2'],

        ]); */
        //TYPE D’ACTE, LIBELLE_WORKFLOW, NOMBRE ETAPE, NOMBRE TOTAL JOURS. 

        $table = $dataTableFactory->create()

            ->add('typeACte', TextColumn::class, ['label' => 'Type acte', 'field' => 'type.titre'])
            ->add('titre', TextColumn::class, ['label' => 'Libelle workflow'])
            ->add('etat', TextColumn::class, ['label' => 'Nombre étapes', 'className' => ' w-100px', 'render' => function ($value, GestionWorkflow $gestionWorkflow) {
                return count($gestionWorkflow->getType()->getWorkflows());
            }])
            ->add('total', TextColumn::class, ['label' => 'Nombre total jours', 'className' => ' w-80px'])

            ->createAdapter(ORMAdapter::class, [
                'entity' => GestionWorkflow::class,
                'query' => function (QueryBuilder $qb) {
                    $qb->select(['p', 'type'])
                        ->from(GestionWorkflow::class, 'p')
                        ->join('p.type', 'type');
                }
            ])
            ->setName('dt_app_parametre_workflow');
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
                    'label' => 'Actions', 'orderable' => false, 'globalSearchable' => false, 'className' => 'grid_row_actions', 'render' => function ($value, GestionWorkflow $context) use ($renders) {
                        $options = [
                            'default_class' => 'btn btn-xs btn-clean btn-icon mr-2 ',
                            'target' => '#exampleModalSizeLg2',


                            'actions' => [
                                'edit' => [
                                    'target' => '#exampleModalSizeSm2',
                                    'url' => $this->generateUrl('app_parametre_type_workflow_edit', ['id' => $context->getType()->getId(), 'workflow' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-pen', 'attrs' => ['class' => 'btn-default'], 'render' => $renders['edit']
                                ],
                                /*    'show' => [
                                    'url' => $this->generateUrl('app_parametre_workflow_show', ['id' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-eye', 'attrs' => ['class' => 'btn-primary'], 'render' => $renders['show']
                                ],
                                'delete' => [
                                    'target' => '#exampleModalSizeNormal',
                                    'url' => $this->generateUrl('app_parametre_workflow_delete', ['id' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-trash', 'attrs' => ['class' => 'btn-main'], 'render' => $renders['delete']
                                ] */
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


        return $this->render('parametre/workflow/index.html.twig', [
            'datatable' => $table,
            'permition' => $permission,
            /* 'grid_id' => $gridId,
            'form' => $builder->getForm()->createView() */
        ]);
    }

    #[Route('/new', name: 'app_parametre_workflow_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, FormError $formError, TypeRepository $repository): Response
    {
        $workflow = new GestionWorkflow();
        $form = $this->createForm(GestionWorkflowType::class, $workflow, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_parametre_workflow_new')
        ]);
        $form->handleRequest($request);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_parametre_workflow_index');
            $datas = $form->get('workflow')->getData();

            $typeActe = $form->get('type')->getData();


            if ($form->isValid()) {

                $total = 0;

                foreach ($datas as $data) {
                    $data->setTypeActe($typeActe);
                    $total = $total + $data->getNombreJours();
                }
                $workflow->setActive(1);
                $workflow->setTotal($total);
                /* $workflow->setType($typeActe); */

                $entityManager->persist($workflow);
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

        return $this->renderForm('parametre/workflow/new.html.twig', [
            'workflow' => $workflow,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/show', name: 'app_parametre_workflow_show', methods: ['GET'])]
    public function show(Workflow $workflow): Response
    {
        return $this->render('parametre/workflow/show.html.twig', [
            'workflow' => $workflow,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_parametre_workflow_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, GestionWorkflow $workflow, EntityManagerInterface $entityManager, FormError $formError): Response
    {

        $form = $this->createForm(GestionWorkflowType::class, $workflow, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_parametre_workflow_edit', [
                'id' => $workflow->getId()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_parametre_workflow_index');
            $datas = $form->get('workflow')->getData();

            if ($form->isValid()) {

                $total = 0;

                foreach ($datas as $data) {

                    $total = $total + $data->getNombreJours();
                }

                $entityManager->persist($workflow);
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

        return $this->renderForm('parametre/workflow/edit.html.twig', [
            'workflow' => $workflow,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_parametre_workflow_delete', methods: ['DELETE', 'GET'])]
    public function delete(Request $request, Workflow $workflow, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                    'app_parametre_workflow_delete',
                    [
                        'id' => $workflow->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = true;
            $entityManager->remove($workflow);
            $entityManager->flush();

            $redirect = $this->generateUrl('app_parametre_workflow_index');

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

        return $this->renderForm('parametre/workflow/delete.html.twig', [
            'workflow' => $workflow,
            'form' => $form,
        ]);
    }
}
