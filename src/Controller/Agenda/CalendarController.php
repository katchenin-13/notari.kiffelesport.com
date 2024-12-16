<?php

namespace App\Controller\Agenda;

use App\Entity\Calendar;
use App\Form\Calendar1Type;
use App\Repository\CalendarRepository;
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
use App\Form\CalendarType;
use App\Service\SendMailService;
use Doctrine\ORM\QueryBuilder;

#[Route('/ads/agenda/calendar')]
class CalendarController extends BaseController
{
    const INDEX_ROOT_NAME = 'app_agenda_calendar_index';

    #[Route('/{etat}', name: 'app_agenda_calendar_index', methods: ['GET', 'POST'])]
    public function index(Request $request, DataTableFactory $dataTableFactory, $etat): Response
    {


        $permission = $this->menu->getPermissionIfDifferentNull($this->security->getUser()->getGroupe()->getId(), self::INDEX_ROOT_NAME);

        $table = $dataTableFactory->create()
            ->add('title', TextColumn::class, ['label' => 'Titre'])
            ->add('start', DateTimeColumn::class,  ['label' => 'Date début', 'format' => 'd/m/Y', 'searchable' => false])
            ->add('end', DateTimeColumn::class, ['label' => 'Date fin', 'format' => 'd/m/Y', 'searchable' => false])

            ->createAdapter(ORMAdapter::class, [
                'entity' => Calendar::class,
                'query' => function (QueryBuilder $qb) use ($etat) {
                    $qb->select(['p', 'en'])
                        ->from(Calendar::class, 'p')
                        ->join('p.entreprise', 'en')
                        ->orderBy('p.id ', 'DESC');
                    if ($this->groupe != "SADM") {
                        $qb->andWhere('en = :entreprise')
                            ->setParameter('entreprise', $this->entreprise);
                    }
                    if ($etat == "passe") {
                        $qb->andWhere('p.start <= :start or p.end <= :start')
                            ->setParameter('start', new \DateTime());
                    } elseif ($etat == "prochain") {
                        $qb->andWhere('p.end > :end')
                            ->setParameter('end', new \DateTime());
                    }
                }
            ])
            ->setName('dt_app_agenda_calendar' . $etat);
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
                    'label' => 'Actions', 'orderable' => false, 'globalSearchable' => false, 'className' => 'grid_row_actions', 'render' => function ($value, Calendar $context) use ($renders) {
                        $options = [
                            'default_class' => 'btn btn-xs btn-clean btn-icon mr-2 ',
                            'target' => '#exampleModalSizeLg2',


                            'actions' => [
                                'edit' => [
                                    'target' => '#exampleModalSizeSm2',
                                    'url' => $this->generateUrl('app_agenda_calendar_edit', ['id' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-pen', 'attrs' => ['class' => 'btn-default'], 'render' => $renders['edit']
                                ],
                                'show' => [
                                    'url' => $this->generateUrl('app_agenda_calendar_show', ['id' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-eye', 'attrs' => ['class' => 'btn-primary'], 'render' => $renders['show']
                                ],
                                'delete' => [
                                    'target' => '#exampleModalSizeNormal',
                                    'url' => $this->generateUrl('app_agenda_calendar_delete', ['id' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-trash', 'attrs' => ['class' => 'btn-main'], 'render' => $renders['delete']
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


        return $this->render('agenda/calendar/index.html.twig', [
            'datatable' => $table,
            'permition' => $permission,
            'etat' => $etat,
            'titre' => "Liste des  activités"
        ]);
    }

    #[Route('/new/new', name: 'app_agenda_calendar_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, FormError $formError,SendMailService $sendMailService): Response
    {

        $titre = "Ajouter un événement";
        $calendar = new Calendar();
        $form = $this->createForm(CalendarType::class, $calendar, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_agenda_calendar_new')
        ]);
        $form->handleRequest($request);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();
        // dd($form->getData());
        if ($form->isSubmitted()) {
          
            $response = [];
            $redirect = $this->generateUrl('app_config_parametre_agenda_index');
            //$email = "";
            // if ($form->getData()->getClient()->getRaisonSocial() == "") {
            //     $email = $form->getData()->getClient()->getEmail();
            // } else {

            //     $email = $form->getData()->getClient()->getEmailEntreprise();
            // }

            // $identite = "";
            // //dd($form->getData()->getClient());
            // if ($form->getData()->getClient()->getRaisonSocial() == "") {
            //     $identite = $form->getData()->getClient()->getNom() . " " . $form->getData()->getClient()->getPrenom();
            // } else {

            //     $identite = $form->getData()->getClient()->getRaisonSocial();
            // }

            //$objet = $form->getData()->getDescription();
            if ($form->isValid()) {
                /*     $mailerService->send(
                    'INFORMATION CONCERNANT LE RENDEZ-VOUS',
                    'konatenvaly@gmail.com',
                    $email,
                    "_admin/contact/template.html.twig",
                    [
                        'message' =>  $objet,
                        'entreprise' =>  "Notari",
                        'identite' =>  $identite,
                        'telephone' =>  '0704314164'
                    ]
                );*/

                $info_user = [
                    'login' => "ddd",
                    'password' => "ttt"
                ];

                $context = compact('info_user');

                // TO DO
                $sendMailService->send(
                    'konatenhamed@ufrseg.enig-sarl.com',
                    "konatenhamed@gmail.com",
                    'Informations',
                    'agenda_mail',
                    $context
                );


                $calendar->setActive(1)
                    ->setAllDay(false)
                    ->setBackgroundColor("#31F74F")
                    ->setBorderColor("#BBF0DA")
                    ->setTextColor("#FAF421");
                $calendar->setEntreprise($this->entreprise);
                $entityManager->persist($calendar);
                $entityManager->flush();
// 
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

        return $this->renderForm('agenda/calendar/new.html.twig', [
            'calendar' => $calendar,
            'form' => $form,
            'titre' => $titre
        ]);
    }

    #[Route('/{id}/show', name: 'app_agenda_calendar_show', methods: ['GET'])]
    public function show(Calendar $calendar): Response
    {
        return $this->render('agenda/calendar/show.html.twig', [
            'calendar' => $calendar,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_agenda_calendar_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Calendar $calendar, EntityManagerInterface $entityManager, FormError $formError,SendMailService $sendMailService): Response
    {

        $form = $this->createForm(CalendarType::class, $calendar, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_agenda_calendar_edit', [
                'id' => $calendar->getId()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_config_parametre_agenda_index');


            if ($form->isValid()) {

                $entityManager->persist($calendar);
                $entityManager->flush();

                $info_user = [
                    'login' => "ddd",
                    'password' => "ttt"
                ];

                $context = compact('info_user');

                      // TO DO
                      $sendMailService->send(
                        'konatenhamed@ufrseg.enig-sarl.com',
                        "konatenhamed@gmail.com",
                        'Informations',
                        'agenda_mail',
                        $context
                    );

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

        return $this->renderForm('agenda/calendar/edit.html.twig', [
            'calendar' => $calendar,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_agenda_calendar_delete', methods: ['DELETE', 'GET'])]
    public function delete(Request $request, Calendar $calendar, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                    'app_agenda_calendar_delete',
                    [
                        'id' => $calendar->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = true;
            $entityManager->remove($calendar);
            $entityManager->flush();

            $redirect = $this->generateUrl('app_config_parametre_agenda_index');

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

        return $this->renderForm('agenda/calendar/delete.html.twig', [
            'calendar' => $calendar,
            'form' => $form,
        ]);
    }
}
