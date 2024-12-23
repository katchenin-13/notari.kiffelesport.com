<?php

namespace App\Controller\Rapports;

use App\Entity\Rapporthebdomadaire;
use App\Form\RapporthebdomadaireType;
use App\Repository\RapporthebdomadaireRepository;
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
use App\Entity\Employe;
use App\Entity\FichierAdmin;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityManagerInterface;
use Mpdf\MpdfException;

#[Route('/ads/rapports/rapporthebdomadaire')]
class RapporthebdomadaireController extends BaseController
{
    const INDEX_ROOT_NAME = 'app_rapports_rapporthebdomadaire_index';
    use FileTrait;
    #[Route('/', name: 'app_rapports_rapporthebdomadaire_index', methods: ['GET', 'POST'])]
    public function index(Request $request, DataTableFactory $dataTableFactory): Response
    {


        $permission = $this->menu->getPermissionIfDifferentNull($this->security->getUser()->getGroupe()->getId(), self::INDEX_ROOT_NAME);

        $table = $dataTableFactory->create()

            ->add('libelle', TextColumn::class, ['label' => 'Libellé'])
            ->add('daterapports', DateTimeColumn::class, ['label' => 'Date', 'format' => 'd/m/Y'])
            ->add('employes', TextColumn::class, ['label' => 'Nom et Prénom', "searchable" => false, 'render' => function ($value, Rapporthebdomadaire $context) {
                return $context->getUtilisateur()->getNomComplet();
            }])
            ->add('function', TextColumn::class, ['label' => 'Fonction', "searchable" => false, 'render' => function ($value, Rapporthebdomadaire $context) {
                $fonction = $context->getUtilisateur()->getEmploye()->getFonction()->getLibelle();
                return $fonction;
            }])
            ->createAdapter(ORMAdapter::class, [
                'entity' => Rapporthebdomadaire::class,
                'query'=> function(QueryBuilder $req){
                $req->select('r')
                    ->from(Rapporthebdomadaire::class, 'r')
                    ->leftJoin('r.utilisateur', 'u')
                    ->innerJoin('u.employe', 'e')
                    ->andWhere('u =:user or e =:user2')
                    ->setParameter('user2', $this->getUser()->getEmploye())
                    ->setParameter('user', $this->getUser())
                ;
                
                }
            ])
            ->setName('dt_app_rapports_rapporthebdomadaire');
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
                'imprimer' => new ActionRender(function () use ($permission) {
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
                    'label' => 'Actions',
                    'orderable' => false,
                    'globalSearchable' => false,
                    'className' => 'grid_row_actions',
                    'render' => function ($value, Rapporthebdomadaire $context) use ($renders) {
                        $options = [
                            'default_class' => 'btn btn-xs btn-clean btn-icon mr-2 ',
                            'target' => '#exampleModalSizeLg2',

                            'actions' => [
                                'edit' => [
                                    'url' => $this->generateUrl('app_rapports_rapporthebdomadaire_edit', ['id' => $value]),
                                    'ajax' => true,
                                    'icon' => '%icon% bi bi-pen',
                                    'attrs' => ['class' => 'btn-default'],
                                    'render' => $renders['edit']
                                ],
                             'imprimer' => [
                                    'url' => $this->generateUrl('app_rapports_rapporthebdomadaire_fichier', ['id' => $value]),
                                    'ajax' => true,
                                    'icon' => '%icon% bi bi-file',
                                    'attrs' => ['class' => 'btn-secondary', 'title' => 'Télécharger le fichier joindre du rapport'],
                                    'render' => $renders['imprimer']
                                ],
                                'delete' => [
                                    'target' => '#exampleModalSizeNormal',
                                    'url' => $this->generateUrl('app_rapports_rapporthebdomadaire_delete', ['id' => $value]),
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
        }

        $table->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }


        return $this->render('rapports/rapporthebdomadaire/index.html.twig', [
            'datatable' => $table,
            'permition' => $permission
        ]);
    }

    #[Route('/new', name: 'app_rapports_rapporthebdomadaire_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, FormError $formError): Response
    {
        $rapporthebdomadaire = new Rapporthebdomadaire();
        $form = $this->createForm(RapporthebdomadaireType::class, $rapporthebdomadaire, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_rapports_rapporthebdomadaire_new')
        ]);
        $form->handleRequest($request);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_rapports_rapporthebdomadaire_index');


            if ($form->isValid()) {

                $entityManager->persist($rapporthebdomadaire);
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

        return $this->renderForm('rapports/rapporthebdomadaire/new.html.twig', [
            'rapporthebdomadaire' => $rapporthebdomadaire,
            'form' => $form,
        ]);
    }


    #[Route('/{id}/show', name: 'app_rapports_rapporthebdomadaire_show', methods: ['GET'])]
    public function show(Rapporthebdomadaire $rapporthebdomadaire): Response
    {
        return $this->render('rapports/rapporthebdomadaire/show.html.twig', [
            'rapporthebdomadaire' => $rapporthebdomadaire,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_rapports_rapporthebdomadaire_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Rapporthebdomadaire $rapporthebdomadaire, EntityManagerInterface $entityManager, FormError $formError): Response
    {

        $form = $this->createForm(RapporthebdomadaireType::class, $rapporthebdomadaire, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_rapports_rapporthebdomadaire_edit', [
                'id' => $rapporthebdomadaire->getId()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_rapports_rapporthebdomadaire_index');


            if ($form->isValid()) {

                $entityManager->persist($rapporthebdomadaire);
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

        return $this->renderForm('rapports/rapporthebdomadaire/edit.html.twig', [
            'rapporthebdomadaire' => $rapporthebdomadaire,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_rapports_rapporthebdomadaire_delete', methods: ['DELETE', 'GET'])]
    public function delete(Request $request, Rapporthebdomadaire $rapporthebdomadaire, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                    'app_rapports_rapporthebdomadaire_delete',
                    [
                        'id' => $rapporthebdomadaire->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = true;
            $entityManager->remove($rapporthebdomadaire);
            $entityManager->flush();

            $redirect = $this->generateUrl('app_rapports_rapporthebdomadaire_index');

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

        return $this->renderForm('rapports/rapporthebdomadaire/delete.html.twig', [
            'rapporthebdomadaire' => $rapporthebdomadaire,
            'form' => $form,
        ]);
    }
#[Route('/{id}/imprimer', name: 'app_rapports_rapporthebdomadaire_fichier', methods: ['GET', 'POST'])]
    public  function  archive(Rapporthebdomadaire $rapporthebdomadaire, RapporthebdomadaireRepository $rapporthebdomadaireRepository)
    {
        $datas = $rapporthebdomadaire->getFichier();    
        return $this->render('rapports/rapporthebdomadaire/archive.html.twig', [
            'data' => $datas,
        ]);
    }
   
}
