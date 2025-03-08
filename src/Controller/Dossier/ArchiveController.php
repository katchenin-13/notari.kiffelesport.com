<?php

namespace App\Controller\Dossier;

use App\Entity\Archive;
use App\Form\Archive1Type;
use App\Repository\ArchiveRepository;
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
use App\Form\ArchiveType;
use App\Repository\TypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

#[Route('/ads/dossier/archive')]
class ArchiveController extends BaseController
{
    const INDEX_ROOT_NAME = 'app_dossier_archive_index';

    #[Route('/', name: 'app_dossier_archive_index', methods: ['GET', 'POST'])]
    public function index(Request $request, DataTableFactory $dataTableFactory): Response
    {


        $permission = $this->menu->getPermissionIfDifferentNull($this->security->getUser()->getGroupe()->getId(), self::INDEX_ROOT_NAME);

        $table = $dataTableFactory->create()
            ->add('numeroOuverture', TextColumn::class, ['label' => 'N° ouverture'])
            ->add('dateOuverture', DateTimeColumn::class, ['label' => 'Date d\'ouverture', 'format' => 'd/m/Y', 'searchable' => false])
            ->add('objet', TextColumn::class, ['label' => 'Objet'])
            ->add('typeActe', TextColumn::class, ['label' => 'Type d\'acte', 'field' => 't.titre'])

            ->add('numeroClassification', TextColumn::class,  ['label' => 'N° classification'])
            ->add('dateClassification', DateTimeColumn::class, ['label' => 'Date classification', 'format' => 'd/m/Y', 'searchable' => false])

            ->createAdapter(ORMAdapter::class, [
                'entity' => Archive::class,
                'query' => function (QueryBuilder $qb) {
                    $qb->select(['p', 't'])
                        ->from(Archive::class, 'p')
                        // ->join('p.entreprise', 'en')
                    ->innerJoin('p.typeActe', 't')

                        ->orderBy('p.id ', 'DESC');
                    // if ($this->groupe != "SADM") {
                    //     $qb->andWhere('en = :entreprise')
                    //         ->setParameter('entreprise', $this->entreprise);
                    // }
                }
            ])
            ->setName('dt_app_dossier_archive');
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
                    'label' => 'Actions', 'orderable' => false, 'globalSearchable' => false, 'className' => 'grid_row_actions', 'render' => function ($value, Archive $context) use ($renders) {
                        $options = [
                            'default_class' => 'btn btn-xs btn-clean btn-icon mr-2 ',
                            'target' => '#exampleModalSizeLg2',


                            'actions' => [
                                'edit' => [
                                    'target' => '#exampleModalSizeSm2',
                                    'url' => $this->generateUrl('app_dossier_archive_edit', ['id' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-pen', 'attrs' => ['class' => 'btn-default'], 'render' => $renders['edit']
                                ],
                                'show' => [
                                    'url' => $this->generateUrl('app_dossier_archive_show', ['id' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-eye', 'attrs' => ['class' => 'btn-primary'], 'render' => $renders['show']
                                ],
                                'delete' => [
                                    'target' => '#exampleModalSizeNormal',
                                    'url' => $this->generateUrl('app_dossier_archive_delete', ['id' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-trash', 'attrs' => ['class' => 'btn-main'], 'render' => $renders['delete']
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


        return $this->render('dossier/archive/index.html.twig', [
            'datatable' => $table,
            'permition' => $permission
        ]);
    }

    #[Route('/new', name: 'app_dossier_archive_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, FormError $formError, TypeRepository $typeRepository): Response
    {
        $validationGroups = ['Default', 'FileRequired', 'oui'];
        $codeTypeActe = $request->query->get('type_acte', 'acte_vente');
        $acteVente = $typeRepository->findOneBy(['code' => $codeTypeActe]);
        $archive = new Archive();
        $archive->setTypeActe($acteVente);
        $filePath = 'archives';
        $form = $this->createForm(ArchiveType::class, $archive, [
            'method' => 'POST',
            'doc_options' => [
                'uploadDir' => $this->getUploadDir(self::UPLOAD_PATH, true),
                'attrs' => ['class' => 'filestyle'],
            ],
            'validation_groups' => $validationGroups,
            'action' => $this->generateUrl('app_dossier_archive_new', ['type_acte' => $codeTypeActe])
        ]);
        $form->handleRequest($request);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_config_parametre_dossier_index');


            if ($form->isValid()) {

                $entityManager->persist($archive);
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

        return $this->renderForm('dossier/archive/new.html.twig', [
            'archive' => $archive,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/show', name: 'app_dossier_archive_show', methods: ['GET'])]
    public function show(Archive $archive): Response
    {
        return $this->render('dossier/archive/show.html.twig', [
            'archive' => $archive,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_dossier_archive_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Archive $archive, EntityManagerInterface $entityManager, FormError $formError): Response
    {
        $typeActe = $archive->getTypeActe();
        $validationGroups = ['Default', 'FileRequired', 'autre'];
        $form = $this->createForm(ArchiveType::class, $archive, [
            'method' => 'POST',
            'doc_options' => [
                'uploadDir' => $this->getUploadDir(self::UPLOAD_PATH, true),
                'attrs' => ['class' => 'filestyle'],
            ],
            'validation_groups' => $validationGroups,
            'action' => $this->generateUrl('app_dossier_archive_edit', [
                'id' => $archive->getId()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_config_parametre_dossier_index');


            if ($form->isValid()) {

                $entityManager->persist($archive);
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

        return $this->renderForm('dossier/archive/edit.html.twig', [
            'archive' => $archive,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_dossier_archive_delete', methods: ['DELETE', 'GET'])]
    public function delete(Request $request, Archive $archive, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                    'app_dossier_archive_delete',
                    [
                        'id' => $archive->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = true;
            $entityManager->remove($archive);
            $entityManager->flush();

            $redirect = $this->generateUrl('app_dossier_archive_index');

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

        return $this->renderForm('dossier/archive/delete.html.twig', [
            'archive' => $archive,
            'form' => $form,
        ]);
    }
}
