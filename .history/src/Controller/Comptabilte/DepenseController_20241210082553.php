<?php

namespace App\Controller\Comptabilte;

use App\Entity\Depense;
use App\Form\DepenseType;
use App\Repository\DepenseRepository;
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
use App\Entity\Lignedepense;
use App\Entity\Type;
use App\Entity\Typedepense;
use App\Repository\LignedepenseRepository;
use App\Repository\TypedepenseRepository;
use Doctrine\ORM\EntityManagerInterface;

use function PHPSTORM_META\type;

#[Route('/ads/comptabilte/depense')]
class DepenseController extends BaseController
{
    const INDEX_ROOT_NAME = 'app_comptabilte_depense_index';

    #[Route('/', name: 'app_comptabilte_depense_index', methods: ['GET', 'POST'])]
    public function index(Request $request, DataTableFactory $dataTableFactory): Response
    {

        $permission = $this->menu->getPermissionIfDifferentNull($this->security->getUser()->getGroupe()->getId(), self::INDEX_ROOT_NAME);

        $table = $dataTableFactory->create()
            ->add('datedepense', DateTimeColumn::class,  ['label' => 'Date de creation ', 'format' => 'd/m/Y', 'searchable' => false])
            ->add('mois', TextColumn::class, [
                'label' => 'Mois',
                'searchable' => false,
                'render' => function ($value, $context) {
                    // Tableau des mois
                    $moisCorrespondance = [
                        1 => 'Janvier',
                        2 => 'Février',
                        3 => 'Mars',
                        4 => 'Avril',
                        5 => 'Mai',
                        6 => 'Juin',
                        7 => 'Juillet',
                        8 => 'Août',
                        9 => 'Septembre',
                        10 => 'Octobre',
                        11 => 'Novembre',
                        12 => 'Décembre',
                    ];

                    // Récupère le numéro du mois depuis la base
                    $numeroMois = $context->getMois(); // Supposons que 'mois' est l'attribut stocké
                    return $moisCorrespondance[$numeroMois] ?? 'Inconnu'; // Retourne 'Inconnu' si le numéro est invalide
                },
            ])
            ->add('libelle', TextColumn::class, ['label' => 'Libelle'])
            ->add('montant', TextColumn::class, [
                'label' => 'Total',
                'searchable' => false,
                'render' => function ($value, Depense $context) {
                    // Initialisation de la somme totale
                    $montantTotal = 0;
                // Parcours des lignes de dépense associées
                foreach ($context->getLignedepenses() as $ligneDepense) {
                    // Récupération du montant
           $montant = str_replace(' ', '', $ligneDepense->getMontant());
            $montantTotal += (float) $montant; 
                }


                    return abs((int)$montantTotal);
                }
            ])


            ->createAdapter(ORMAdapter::class, [
                'entity' => Depense::class,
            ])
            ->setName('dt_app_comptabilte_depense');
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
                    'label' => 'Actions',
                    'orderable' => false,
                    'globalSearchable' => false,
                    'className' => 'grid_row_actions',
                    'render' => function ($value, Depense $context) use ($renders) {
                        $options = [
                            'default_class' => 'btn btn-xs btn-clean btn-icon mr-2 ',
                            'target' => '#exampleModalSizeLg2',

                            'actions' => [
                                'edit' => [
                                    'url' => $this->generateUrl('app_comptabilte_depense_edit', ['id' => $value]),
                                    'ajax' => true,
                                    'icon' => '%icon% bi bi-pen',
                                    'attrs' => ['class' => 'btn-default'],
                                    'render' => $renders['edit']
                                ],
                                'show' => [
                                    'url' => $this->generateUrl('app_comptabilte_depense_show', ['id' => $value]),
                                    'ajax' => true,
                                    'icon' => '%icon% bi bi-eye',
                                    'attrs' => ['class' => 'btn-primary'],
                                    'render' => $renders['show']
                                ],
                                'delete' => [
                                    'target' => '#exampleModalSizeNormal',
                                    'url' => $this->generateUrl('app_comptabilte_depense_delete', ['id' => $value]),
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


        return $this->render('comptabilte/depense/index.html.twig', [
            'datatable' => $table,
            'permition' => $permission
        ]);
    }
    #[Route('/new', name: 'app_comptabilte_depense_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        FormError $formError,
        TypedepenseRepository $typedepenseRepository,
        LignedepenseRepository $lignedepenseRepository
    ): Response {
        // Création de l'entité Depense
        $depense = new Depense();
        $validationGroups = ['Default', 'FileRequired', 'oui'];
        $typedepense = $typedepenseRepository->findAll();
        //affichage des types de depense
        foreach ($typedepense as $key => $value) {
            $lignedepense = new Lignedepense();
            $lignedepense->setTypedepense($value);
            $depense->addLignedepense($lignedepense);
        }
        // Création du formulaire
        $form = $this->createForm(DepenseType::class, $depense, [
            'method' => 'GET',
            'doc_options' => [
                'uploadDir' => $this->getUploadDir(self::UPLOAD_PATH, true),
                'attrs' => ['class' => 'filestyle'],
            ],
            'validation_groups' => $validationGroups,
            'action' => $this->generateUrl('app_comptabilte_depense_new'),
        ]);

        $form->handleRequest($request);
        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();
        // Traitement du formulaire
        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_parametre_type_index');


            if ($form->isValid()) {

                $ligne = $depense->getLignedepenses();

                foreach ($ligne as $key => $value) {
                    $lignedepenses = new Lignedepense();
                    // $lignedepenses->setMontant($ligne[$key]->getMontant());
                    // $lignedepenses->setTypedepense($ligne[$key]->getTypedepense());
                    // $lignedepenses->setDepenses($depense);
                    // Création d'une nouvelle instance de Lignedepense
             

                    // Récupération des propriétés depuis l'objet $ligne[$key]
                    $montant = (int) $ligne[$key]->getMontant(); // Conversion en float
                    $typedepense = $ligne[$key]->getTypedepense();

                    // Définition des propriétés
                    $lignedepenses->setMontant($montant);
                    $lignedepenses->setTypedepense($typedepense);
                    $lignedepenses->setDepenses($depense);

                    // Persister l'entité Lignedepense dans Doctrine
                    // $entityManager->persist($lignedepenses);
                  
                }

                $entityManager->persist($depense);
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
                    return $this->redirect(
                        $redirect,
                        Response::HTTP_OK
                    );
                }
            }
        }
        // Rendu du formulaire
        return $this->renderForm('comptabilte/depense/new.html.twig', [
            'depense' => $depense,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/show', name: 'app_comptabilte_depense_show', methods: ['GET'])]
    public function show(Depense $depense): Response
    {
        return $this->render('comptabilte/depense/show.html.twig', [
            'depense' => $depense,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_comptabilte_depense_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Depense $depenseliste, EntityManagerInterface $entityManager,LignedepenseRepository $lignedepenseRepository ,FormError $formError): Response
    {
        $validationGroups = ['Default', 'FileRequired', 'oui'];
        $ligne = $lignedepenseRepository->findBy(['depenses' => $depenseliste->getId()]);
       
        foreach ($ligne as $lignes) {
            $value = new Lignedepense();
            $value->setTypedepense($lignes->getTypedepense());
            $value->setDepenses($depenseliste);
            $value->setMontant($lignes->getMontant());
            $depenseliste->addLignedepense($value);
        }
        if (!$depenseliste) {
            throw $this->createNotFoundException('La dépense n\'a pas été trouvée.');
        }
       // dd($depenseliste->getLignedepenses());
        $form = $this->createForm(DepenseType::class, $depenseliste, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_comptabilte_depense_edit', [
                'id' => $depenseliste->getId()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_comptabilte_depense_index');


            if ($form->isValid()) {

                $entityManager->persist($depenseliste);
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

        return $this->renderForm('comptabilte/depense/edit.html.twig', [
            'depenseliste' => $depenseliste,
            'form' => $form,
        ]);
    }

   


    #[Route('/{id}/delete', name: 'app_comptabilte_depense_delete', methods: ['DELETE', 'GET'])]
    public function delete(Request $request, Depense $depense, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                    'app_comptabilte_depense_delete',
                    [
                        'id' => $depense->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = true;
            $entityManager->remove($depense);
            $entityManager->flush();

            $redirect = $this->generateUrl('app_comptabilte_depense_index');

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

        return $this->renderForm('comptabilte/depense/delete.html.twig', [
            'depense' => $depense,
            'form' => $form,
        ]);
    }
}
