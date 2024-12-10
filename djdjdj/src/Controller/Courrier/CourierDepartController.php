<?php

namespace App\Controller\Courrier;

use App\Entity\CourierArrive;
use App\Form\CourierArrive1Type;
use App\Repository\CourierArriveRepository;
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
use App\Entity\FichierAdmin;
use App\Form\CourierArriveType;
use App\Repository\DocumentCourierRepository;
use Doctrine\ORM\QueryBuilder;

#[Route('/ads/courrier/courier/depart')]
class CourierDepartController extends BaseController
{
    const INDEX_ROOT_NAME = 'app_courrier_courier_depart_index';

    #[Route('/', name: 'app_courrier_courier_depart_index', methods: ['GET', 'POST'])]
    public function index(Request $request, DataTableFactory $dataTableFactory): Response
    {

        //dd('hghegkjh');
        $permission = $this->menu->getPermissionIfDifferentNull($this->security->getUser()->getGroupe()->getId(), self::INDEX_ROOT_NAME);

        $table = $dataTableFactory->create()
            ->add('numero', TextColumn::class, ['label' => 'Numéro'])
            ->add('dateReception', DateTimeColumn::class,  ['label' => 'Date réception', 'format' => 'd/m/Y', 'searchable' => false])
            ->add('objet', TextColumn::class, ['label' => 'Objet'])
            ->add('expediteur', TextColumn::class, ['label' => 'Expéditeur'])

            ->createAdapter(ORMAdapter::class, [
                'entity' => CourierArrive::class,
                'query' => function (QueryBuilder $qb) {
                    $qb->select(['p', 'en'])
                        ->from(CourierArrive::class, 'p')
                        ->where('p.type = :type')
                        ->join('p.entreprise', 'en')
                        ->orderBy('p.id ', 'DESC')
                        ->setParameter('type', 'DEPART');
                    if ($this->groupe != "SADM") {
                        $qb->andWhere('en = :entreprise')
                            ->setParameter('entreprise', $this->entreprise);
                    }
                }
            ])
            ->setName('dt_app_courrier_courier_depart');
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
                'archive' => new ActionRender(function () use ($permission) {
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
                    'label' => 'Actions', 'orderable' => false, 'globalSearchable' => false, 'className' => 'grid_row_actions', 'render' => function ($value, CourierArrive $context) use ($renders) {
                        $options = [
                            'default_class' => 'btn btn-xs btn-clean btn-icon mr-2 ',
                            'target' => '#exampleModalSizeLg2',


                            'actions' => [
                                'edit' => [
                                    'target' => '#exampleModalSizeSm2',
                                    'url' => $this->generateUrl('app_courrier_courier_depart_edit', ['id' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-pen', 'attrs' => ['class' => 'btn-default'], 'render' => $renders['edit']
                                ],
                                'show' => [
                                    'url' => $this->generateUrl('app_courrier_courier_depart_show', ['id' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-eye', 'attrs' => ['class' => 'btn-primary'], 'render' => $renders['show']
                                ],
                                'delete' => [
                                    'target' => '#exampleModalSizeNormal',
                                    'url' => $this->generateUrl('app_courrier_courier_depart_delete', ['id' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-trash', 'attrs' => ['class' => 'btn-main'], 'render' => $renders['delete']
                                ],
                                /* 'archive' => [
                                    'url' => $this->generateUrl('courierArrive_archive', ['id' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-file', 'attrs' => ['class' => 'btn-dark', 'title' => 'Archive'], 'render' => new ActionRender(function () use ($renders) {
                                        return $renders['archive'];
                                    })
                                ], */
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


        return $this->render('courrier/courier_depart/index.html.twig', [
            'datatable' => $table,
            'permition' => $permission
        ]);
    }


    /**
     * @Route("/archive/{id}/depart", name="courierArrive_archive", methods={"GET"})
     * @param $id
     * @param CourierArriveRepository $repository
     * @return Response
     */
    #[Route('/archive/{id}/depart', name: 'courierArrive_archive', methods: ['GET', 'POST'])]
    public  function  archive($id, DocumentCourierRepository $documentCourierRepository)
    {


        return $this->render('courrier/courier_depart/archive.html.twig', [
            'titre' => 'Arrive',
            'data' => $documentCourierRepository->getFichier($id),

        ]);
    }

    #[Route('/new', name: 'app_courrier_courier_depart_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, FormError $formError): Response
    {
        $validationGroups = ['Default', 'FileRequired', 'oui'];
        $courierArrive = new CourierArrive();
        $form = $this->createForm(CourierArriveType::class, $courierArrive, [
            'method' => 'POST',
            'doc_options' => [
                'uploadDir' => $this->getUploadDir(self::UPLOAD_PATH, true),
                'attrs' => ['class' => 'filestyle'],
            ],
            'type' => 'autre',
            'validation_groups' => $validationGroups,
            'action' => $this->generateUrl('app_courrier_courier_depart_new')
        ]);
        $form->handleRequest($request);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_courrier_courier_depart_index');


            if ($form->isValid()) {

                $courierArrive->setEntreprise($this->entreprise);
                $courierArrive->setEtat(false);
                $courierArrive->setType('DEPART');
                $courierArrive->setCategorie('COURRIER');
                $courierArrive->setActive(1);
                $entityManager->persist($courierArrive);
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

        return $this->renderForm('courrier/courier_depart/new.html.twig', [
            'courier_depart' => $courierArrive,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/show', name: 'app_courrier_courier_depart_show', methods: ['GET'])]
    public function show(CourierArrive $courierArrive): Response
    {
        return $this->render('courrier/courier_depart/show.html.twig', [
            'courier_depart' => $courierArrive,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_courrier_courier_depart_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, CourierArrive $courierArrive, EntityManagerInterface $entityManager, FormError $formError): Response
    {
        $validationGroups = ['Default', 'FileRequired', 'autre'];
        $form = $this->createForm(CourierArriveType::class, $courierArrive, [
            'method' => 'POST',
            'doc_options' => [
                'uploadDir' => $this->getUploadDir(self::UPLOAD_PATH, true),
                'attrs' => ['class' => 'filestyle'],
            ],
            'type' => 'autre',
            'validation_groups' => $validationGroups,
            'action' => $this->generateUrl('app_courrier_courier_depart_edit', [
                'id' => $courierArrive->getId()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_courrier_courier_depart_index');


            if ($form->isValid()) {

                $entityManager->persist($courierArrive);
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

        return $this->renderForm('courrier/courier_depart/edit.html.twig', [
            'courier_depart' => $courierArrive,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_courrier_courier_depart_delete', methods: ['DELETE', 'GET'])]
    public function delete(Request $request, CourierArrive $courierArrive, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                    'app_courrier_courier_depart_delete',
                    [
                        'id' => $courierArrive->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = true;
            $entityManager->remove($courierArrive);
            $entityManager->flush();

            $redirect = $this->generateUrl('app_courrier_courier_depart_index');

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

        return $this->renderForm('courrier/courier_depart/delete.html.twig', [
            'courier_depart' => $courierArrive,
            'form' => $form,
        ]);
    }



    #[Route('/existe', name: 'existedepart', methods: ['POST', 'GET'])]
    public function existe(CourierArriveRepository $repository, Request $request): Response
    {
        $response = new Response();
        $format = "";
        $nombre = $repository->getNombre();
        $date = date('y');
        $format = $date . '-' . $nombre . ' ' . 'D';

        if ($request->isXmlHttpRequest()) {




            $arrayCollection[] = array(
                'nom' =>  $format,

                // ... Same for each property you want
            );
            $data = json_encode($arrayCollection); // formater le résultat de la requête en json
            //dd($data);
            $response->headers->set('Content-type', 'application/json');
            $response->setContent($data);
        }
        return $this->json([
            'code' => 200,
            'message' => 'ça marche bien',
            'nom' => $format,
        ], 200);
    }


    #[Route('/accuse/{id}', name: 'courierArrive_accuse_edit', methods: ['POST', 'GET'])]
    public function accuse(Request $request, CourierArrive $courierArrive, EntityManagerInterface $em): Response
    {
        $validationGroups = ['Default', 'FileRequired', 'autre'];
        $filePath = 'courrier';
        $form = $this->createForm(CourierArriveType::class, $courierArrive, [
            'method' => 'POST',
            'doc_options' => [
                'uploadDir' => $this->getUploadDir(self::UPLOAD_PATH, true),
                'attrs' => ['class' => 'filestyle'],
            ],
            'validation_groups' => $validationGroups,
            'action' => $this->generateUrl('courierArrive_accuse_edit', [
                'id' => $courierArrive->getId(),
            ])
        ]);

        $file = new FichierAdmin();
        $file->setPath("");

        //  $courierArrive->addFichier($file);

        $form->handleRequest($request);

        $isAjax = $request->isXmlHttpRequest();
        //  $type = $form->getData()->getType();
        if ($form->isSubmitted()) {

            $redirect = $this->generateUrl('app_courrier_courier_interne_index');
            //$brochureFile = $form->get('fichiers')->getData();

            if ($form->isValid()) {

                /*   foreach ($brochureFile as $image) {
                    $file = new File($image->getPath());
                    $newFilename = md5(uniqid()) . '.' . $file->guessExtension();
                    // $fileName = md5(uniqid()).'.'.$file->guessExtension();
                    $file->move($this->getParameter('images_directory'), $newFilename);
                    $image->setPath($newFilename);
                } */
                $em->persist($courierArrive);
                $em->flush();

                $message = 'Opération effectuée avec succès';
                $statut = 1;
                $this->addFlash('success', $message);
            }

            if ($isAjax) {
                return $this->json(compact('statut', 'message', 'redirect'));
            } else {
                if ($statut == 1) {
                    return $this->redirect($redirect);
                }
            }
        }

        return $this->render('courrier/depart/accuse.html.twig', [
            'titre' => "ACCUSE DE RECEPTION",
            'courierArrive' => $courierArrive,
            'form' => $form->createView(),
        ]);
    }
}
