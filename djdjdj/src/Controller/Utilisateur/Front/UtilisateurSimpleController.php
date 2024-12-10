<?php

namespace App\Controller\Utilisateur\Front;

use App\Entity\UtilisateurSimple;
use App\Form\UtilisateurSimpleType;
use App\Repository\UtilisateurSimpleRepository;
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

#[Route('/ads/utilisateur/front/utilisateur/simple')]
class UtilisateurSimpleController extends BaseController
{

    private function numero()
    {

        $query = $this->em->createQueryBuilder();
        $query->select("count(a.id)")
            ->from(UtilisateurSimple::class, 'a');

        $nb = $query->getQuery()->getSingleScalarResult();
        if ($nb == 0) {
            $nb = 1;
        } else {
            $nb = $nb + 1;
        }
        return (date("y") . 'US' . date("m", strtotime("now")) . str_pad($nb, 3, '0', STR_PAD_LEFT));
    }


    const INDEX_ROOT_NAME = 'app_utilisateur_utilisateur_simple_index';

    #[Route('/ads/', name: 'app_utilisateur_utilisateur_simple_index', methods: ['GET', 'POST'])]
    public function index(Request $request, DataTableFactory $dataTableFactory): Response
    {
        $permission = $this->menu->getPermissionIfDifferentNull($this->security->getUser()->getGroupe()->getId(), self::INDEX_ROOT_NAME);

        $table = $dataTableFactory->create()
            ->add('nom', TextColumn::class, ['label' => 'Nom'])
            ->add('prenoms', TextColumn::class, ['label' => 'Prenoms'])
            ->add('contact', TextColumn::class, ['label' => 'Contact'])
            ->createAdapter(ORMAdapter::class, [
                'entity' => UtilisateurSimple::class,
            ])
            ->setName('dt_app_utilisateur_front_utilisateur_simple');
        if ($permission != null) {

            $renders = [
                'edit' =>  new ActionRender(function () use ($permission) {
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
                    } else {
                        return true;
                    }
                }),
                'change_password' =>  new ActionRender(function () use ($permission) {
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
                    } else {
                        return true;
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
                    } else {
                        return true;
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
                    } else {
                        return true;
                    }
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
                $table->add('reference', TextColumn::class, [
                    'label' => 'Actions', 'orderable' => false, 'globalSearchable' => false, 'className' => 'grid_row_actions', 'render' => function ($value, UtilisateurSimple $context) use ($renders) {
                        // dd($context);
                        $options = [
                            'default_class' => 'btn btn-xs btn-clean btn-icon mr-2 ',
                            'target' => '#exampleModalSizeLg2',

                            'actions' => [
                                'edit' => [
                                    'url' => $this->generateUrl('app_utilisateur_front_utilisateur_simple_edit', ['reference' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-pen', 'attrs' => ['class' => 'btn-default'], 'render' => $renders['edit']
                                ],
                                'show' => [
                                    'url' => $this->generateUrl('app_utilisateur_front_utilisateur_simple_show', ['reference' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-eye', 'attrs' => ['class' => 'btn-primary'], 'render' => $renders['show']
                                ],
                                'change_password' => [
                                    'target' => '#exampleModalSizeNormal',
                                    'url' => $this->generateUrl('app_utilisateur_front_utilisateur_simple_change_password', ['reference' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-lock', 'attrs' => ['class' => 'btn-success'], 'render' => $renders['change_password']
                                ],
                                'delete' => [
                                    'target' => '#exampleModalSizeNormal',
                                    'url' => $this->generateUrl('app_utilisateur_front_utilisateur_simple_delete', ['reference' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-trash', 'attrs' => ['class' => 'btn-main'],  'render' => $renders['delete']
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


        return $this->render('utilisateur/front/utilisateur_simple/index.html.twig', [
            'datatable' => $table,
            'permition' => $permission
        ]);
    }

    #[Route('/ads/new', name: 'app_utilisateur_front_utilisateur_simple_new', methods: ['GET', 'POST'])]
    public function new(Request $request, UtilisateurSimpleRepository $utilisateurSimpleRepository, FormError $formError): Response
    {
        $utilisateurSimple = new UtilisateurSimple();
        $validationGroups = ['Default', 'FileRequired', 'autre'];
        $form = $this->createForm(UtilisateurSimpleType::class, $utilisateurSimple, [
            'method' => 'POST',
            'type' => 'utilisateur_simple',
            'password' => 'password',
            'doc_options' => [
                'uploadDir' => $this->getUploadDir(self::UPLOAD_PATH, true),
                'attrs' => ['class' => 'filestyle'],
            ],
            'validation_groups' => $validationGroups,
            'action' => $this->generateUrl('app_utilisateur_front_utilisateur_simple_new')
        ]);
        $form->handleRequest($request);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_utilisateur_front_utilisateur_simple_index');
            $password = $form->get('password')->getData();

            if ($form->isValid()) {
                $utilisateurSimple->setPassword($this->hasher->hashPassword($utilisateurSimple, $password));
                $utilisateurSimple->setReference($this->numero());
                $utilisateurSimpleRepository->save($utilisateurSimple, true);
                $data = true;
                $message       = 'Opération effectuée avec succès';
                $statut = 1;
                $this->addFlash('success', $message);
            } else {
                $message = $formError->all($form);
                $statut = 0;
                $statutCode = Response::HTTP_INTERNAL_SERVER_ERROR;
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

        return $this->renderForm('utilisateur/front/utilisateur_simple/new.html.twig', [
            'utilisateur_simple' => $utilisateurSimple,
            'form' => $form,
        ]);
    }

    #[Route('/ads/{reference}/show', name: 'app_utilisateur_front_utilisateur_simple_show', methods: ['GET'])]
    public function show(UtilisateurSimple $utilisateurSimple): Response
    {
        return $this->render('utilisateur/front/utilisateur_simple/show.html.twig', [
            'utilisateur_simple' => $utilisateurSimple,
        ]);
    }

    #[Route('/ads/{reference}/edit', name: 'app_utilisateur_front_utilisateur_simple_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, UtilisateurSimple $utilisateurSimple, UtilisateurSimpleRepository $utilisateurSimpleRepository, FormError $formError): Response
    {
        //dd($utilisateurSimple->getNom());
        $validationGroups = ['Default', 'FileRequired', 'autre'];
        $form = $this->createForm(UtilisateurSimpleType::class, $utilisateurSimple, [
            'method' => 'POST',
            'type' => 'utilisateur_simple',
            'password' => 'nopassword',
            'doc_options' => [
                'uploadDir' => $this->getUploadDir(self::UPLOAD_PATH, true),
                'attrs' => ['class' => 'filestyle'],
            ],
            'validation_groups' => $validationGroups,
            'action' => $this->generateUrl('app_utilisateur_front_utilisateur_simple_edit', [
                'reference' =>  $utilisateurSimple->getReference()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_utilisateur_front_utilisateur_simple_index');


            if ($form->isValid()) {

                $utilisateurSimpleRepository->save($utilisateurSimple, true);
                $data = true;
                $message       = 'Opération effectuée avec succès';
                $statut = 1;
                $this->addFlash('success', $message);
            } else {
                $message = $formError->all($form);
                $statut = 0;
                $statutCode = Response::HTTP_INTERNAL_SERVER_ERROR;
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

        return $this->renderForm('utilisateur/front/utilisateur_simple/edit.html.twig', [
            'utilisateur_simple' => $utilisateurSimple,
            'form' => $form,
        ]);
    }

    #[Route('/{reference}/change/password', name: 'app_utilisateur_front_utilisateur_simple_change_password', methods: ['GET', 'POST'])]
    public function changePassword(Request $request, UtilisateurSimple $utilisateurSimple, UtilisateurSimpleRepository $utilisateurSimpleRepository, FormError $formError): Response
    {
        $validationGroups = ['Default', 'FileRequired', 'autre'];
        $form = $this->createForm(UtilisateurSimpleType::class, $utilisateurSimple, [
            'method' => 'POST',
            'type' => 'utilisateur_simple',
            'password' => 'changePassword',
            'doc_options' => [
                'uploadDir' => $this->getUploadDir(self::UPLOAD_PATH, true),
                'attrs' => ['class' => 'filestyle'],
            ],
            'validation_groups' => $validationGroups,
            'action' => $this->generateUrl('app_utilisateur_front_utilisateur_simple_change_password', [
                'reference' =>  $utilisateurSimple->getReference()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();
        //dd($utilisateurSimple);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            //dd($utilisateurSimple);
            $response = [];
            $redirect = $this->generateUrl('app_utilisateur_front_prestataire_index');
            // $quartier = $form->get('quartier')->getData();
            $password = $form->get('password')->getData();


            if ($form->isValid()) {
                //dd($quartier);
                $utilisateurSimple->setPassword($this->hasher->hashPassword($utilisateurSimple, $password));
                //$prestataire->setQuartier($quartier);
                $utilisateurSimpleRepository->save($utilisateurSimple, true);
                $data = true;
                $message       = 'Opération effectuée avec succès';
                $statut = 1;
                $this->addFlash('success', $message);
            } else {
                $message = $formError->all($form);
                $statut = 0;
                $statutCode = Response::HTTP_INTERNAL_SERVER_ERROR;
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

        return $this->renderForm('utilisateur/front/utilisateur_simple/password.html.twig', [
            'prestataire' => $utilisateurSimple,
            'form' => $form,
        ]);
    }

    #[Route('/ads/{reference}/delete', name: 'app_utilisateur_front_utilisateur_simple_delete', methods: ['DELETE', 'GET'])]
    public function delete(Request $request, UtilisateurSimple $utilisateurSimple, UtilisateurSimpleRepository $utilisateurSimpleRepository): Response
    {
        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                    'app_utilisateur_front_utilisateur_simple_delete',
                    [
                        'reference' => $utilisateurSimple->getReference()
                    ]
                )
            )
            ->setMethod('DELETE')
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = true;
            $utilisateurSimpleRepository->remove($utilisateurSimple, true);

            $redirect = $this->generateUrl('app_utilisateur_front_utilisateur_simple_index');

            $message = 'Opération effectuée avec succès';

            $response = [
                'statut'   => 1,
                'message'  => $message,
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

        return $this->renderForm('utilisateur/front/utilisateur_simple/delete.html.twig', [
            'utilisateur_simple' => $utilisateurSimple,
            'form' => $form,
        ]);
    }
}
