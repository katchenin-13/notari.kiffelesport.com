<?php

namespace App\Controller\Actes;

use App\Entity\Enregistrement;
use App\Form\Enregistrement1Type;
use App\Repository\EnregistrementRepository;
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
use App\Controller\FileTrait;
use App\Entity\Dossier;
use App\Repository\IdentificationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

#[Route('/ads/actes/enregistrement')]
class EnregistrementController extends BaseController
{
    use FileTrait;

    const INDEX_ROOT_NAME = 'app_actes_enregistrement_index';


    #[Route('/imprime/acte/conservateur/all', name: 'app_suivi_print_conservatuion_all', methods: ['GET'])]
    public function imprimer(EnregistrementRepository $enregistrementRepository, IdentificationRepository $idebtificationRepository): Response
    {
        //dd($idebtificationRepository->getData(3));

        // dd($sponsoringRepository->findAll());
        $imgFiligrame = "uploads/" . 'logo' . "/" . 'logo2.png';
        return $this->renderPdf("actes/enregistrement/imprime.html.twig", [
            'data' => $enregistrementRepository->getData($this->entreprise),
            //'data_info'=>$infoPreinscriptionRepository->findOneByPreinscription($preinscription)
        ], [
            'orientation' => 'L',
            'protected' => true,

            'format' => 'A5',

            'showWaterkText' => true,
            'fontDir' => [
                $this->getParameter('font_dir') . '/arial',
                $this->getParameter('font_dir') . '/trebuchet',
            ]
        ], true, "", "");
        //return $this->renderForm("stock/sortie/imprime.html.twig");

    }

    #[Route('/', name: 'app_actes_enregistrement_index', methods: ['GET', 'POST'])]
    public function index(Request $request, DataTableFactory $dataTableFactory): Response
    {


        $permission = $this->menu->getPermissionIfDifferentNull($this->security->getUser()->getGroupe()->getId(), self::INDEX_ROOT_NAME);

        $table = $dataTableFactory->create()
            ->add('numeroOuverture', TextColumn::class, ['label' => 'Numéro ouverture', 'field' => 'd.numeroOuverture'])
            ->add('objet', TextColumn::class, ['label' => 'Objet', 'field' => 'd.objet'])
            ->add('dateCreation', DateTimeColumn::class,  ['label' => 'Date acte', 'field' => 'd.dateCreation', 'format' => 'd/m/Y', 'searchable' => false])
            ->add('date', DateTimeColumn::class, ['format' => 'd/m/Y', 'searchable' => false, 'label' => 'Date dépot'])
            ->add('nombreJours', TextColumn::class, ['className' => 'w-200px text-center', 'label' => 'Nombre jours à la conservation', 'render' => function ($value, Enregistrement $context) {

                $datetime = new \DateTime("now");
                $diff_in_days = floor((strtotime($context->getDate()->format('Y-m-d')) - strtotime($datetime->format("Y-m-d"))) / (60 * 60 * 24));

                //dd($diff_in_days);
                return $diff_in_days;
            }])
            /*   ->add('depot', TextColumn::class, ['className' => 'w-250px', 'label' => 'Etat', 'render' => function ($value, Enregistrement $context) {
                $data = $context->getEnregistrements()->filter(function (Enregistrement $enregistrement) {
                    return $enregistrement->getSens() == 1;
                });

                return $data != null ? 'Dossier à la conservation' : 'Dossier pas encore à la conservation';
            }]) */
            ->createAdapter(ORMAdapter::class, [
                'entity' => Enregistrement::class,
                'query' => function (QueryBuilder $qb) {
                    $qb->select(['p,  en,d'])
                        ->from(Enregistrement::class, 'p')
                        ->join('p.dossier', 'd')
                        ->join('d.entreprise', 'en')
                        ->andWhere('d.etape = :etape')
                        ->andWhere('p.sens = :sens')
                        ->andWhere('p.date is not null')
                        ->setParameter('etape', 'enregistrement')
                        ->setParameter('sens', 1)
                        ->orderBy('p.id ', 'DESC');

                    if ($this->groupe != "SADM") {
                        $qb->andWhere('en = :entreprise')
                            ->setParameter('entreprise', $this->entreprise);
                    }
                }

            ])
            ->setName('dt_app_actes_enregistrement');
        if ($permission != null) {

            $renders = [
                'edit' => new ActionRender(function () use ($permission) {
                    if ($permission == 'R') {
                        return false;
                    } elseif ($permission == 'RD') {
                        return false;
                    } elseif ($permission == 'RU') {
                        return false;
                    } elseif ($permission == 'CRUD') {
                        return false;
                    } elseif ($permission == 'CRU') {
                        return false;
                    } elseif ($permission == 'CR') {
                        return false;
                    }
                }),
                'delete' => new ActionRender(function () use ($permission) {
                    if ($permission == 'R') {
                        return false;
                    } elseif ($permission == 'RD') {
                        return false;
                    } elseif ($permission == 'RU') {
                        return false;
                    } elseif ($permission == 'CRUD') {
                        return false;
                    } elseif ($permission == 'CRU') {
                        return false;
                    } elseif ($permission == 'CR') {
                        return false;
                    }
                }),
                'show' => new ActionRender(function () use ($permission) {
                    if ($permission == 'R') {
                        return false;
                    } elseif ($permission == 'RD') {
                        return false;
                    } elseif ($permission == 'RU') {
                        return false;
                    } elseif ($permission == 'CRUD') {
                        return false;
                    } elseif ($permission == 'CRU') {
                        return false;
                    } elseif ($permission == 'CR') {
                        return false;
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
                    'label' => 'Actions', 'orderable' => false, 'globalSearchable' => false, 'className' => 'grid_row_actions', 'render' => function ($value, Enregistrement $context) use ($renders) {
                        $options = [
                            'default_class' => 'btn btn-xs btn-clean btn-icon mr-2 ',
                            'target' => '#exampleModalSizeLg2',

                            'actions' => [
                                'edit' => [
                                    'url' => $this->generateUrl('app_actes_enregistrement_edit', ['id' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-pen', 'attrs' => ['class' => 'btn-default'], 'render' => $renders['edit']
                                ],
                                'show' => [
                                    'url' => $this->generateUrl('app_actes_enregistrement_show', ['id' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-eye', 'attrs' => ['class' => 'btn-primary'], 'render' => $renders['show']
                                ],
                                'delete' => [
                                    'target' => '#exampleModalSizeNormal',
                                    'url' => $this->generateUrl('app_actes_enregistrement_delete', ['id' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-trash', 'attrs' => ['class' => 'btn-main'], 'render' => $renders['delete']
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


        return $this->render('actes/enregistrement/index.html.twig', [
            'datatable' => $table,
            'permition' => $permission
        ]);
    }

    #[Route('/new', name: 'app_actes_enregistrement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, FormError $formError): Response
    {
        $enregistrement = new Enregistrement();
        $form = $this->createForm(Enregistrement1Type::class, $enregistrement, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_actes_enregistrement_new')
        ]);
        $form->handleRequest($request);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_actes_enregistrement_index');


            if ($form->isValid()) {

                $entityManager->persist($enregistrement);
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

        return $this->renderForm('actes/enregistrement/new.html.twig', [
            'enregistrement' => $enregistrement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/show', name: 'app_actes_enregistrement_show', methods: ['GET'])]
    public function show(Enregistrement $enregistrement): Response
    {
        return $this->render('actes/enregistrement/show.html.twig', [
            'enregistrement' => $enregistrement,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_actes_enregistrement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Enregistrement $enregistrement, EntityManagerInterface $entityManager, FormError $formError): Response
    {

        $form = $this->createForm(Enregistrement1Type::class, $enregistrement, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_actes_enregistrement_edit', [
                'id' => $enregistrement->getId()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_actes_enregistrement_index');


            if ($form->isValid()) {

                $entityManager->persist($enregistrement);
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

        return $this->renderForm('actes/enregistrement/edit.html.twig', [
            'enregistrement' => $enregistrement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_actes_enregistrement_delete', methods: ['DELETE', 'GET'])]
    public function delete(Request $request, Enregistrement $enregistrement, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                    'app_actes_enregistrement_delete',
                    [
                        'id' => $enregistrement->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = true;
            $entityManager->remove($enregistrement);
            $entityManager->flush();

            $redirect = $this->generateUrl('app_actes_enregistrement_index');

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

        return $this->renderForm('actes/enregistrement/delete.html.twig', [
            'enregistrement' => $enregistrement,
            'form' => $form,
        ]);
    }
}
