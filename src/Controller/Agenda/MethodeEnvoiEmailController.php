<?php
namespace App\Controller\Agenda;

use App\Entity\Calendar;
use App\Form\CalendarType;
use App\Service\FormError;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;

class   MethodeEnvoiEmailController
{
    public function __invoke()
    {
        // TODO: Implement __invoke() method.
    }

    #[Route('/new/new', name: 'app_agenda_calendar_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, FormError $formError, MailerInterface $mailer): Response
    {
        $calendar = new Calendar();
        $form = $this->createForm(CalendarType::class, $calendar, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_agenda_calendar_new')
        ]);
        $form->handleRequest($request);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_config_parametre_agenda_index');

            if ($form->isValid()) {
                // Vérification si l'utilisateur a coché "Envoyer par mail"
                $sendByEmail = $request->request->get('sendByEmail'); // Récupération du champ checkbox

                if ($sendByEmail) {
                    // Récupération des clients du dossier associé
                    $clients = $calendar->getDossier()->getClients();

                    foreach ($clients as $client) {
                        $email = $client->getEmail();
                        if ($email) {
                            // Création et envoi du mail
                            $emailMessage = (new Email())
                                ->from('no-reply@votre-entreprise.com') // Remplacez par votre adresse
                                ->to($email)
                                ->subject('Nouvelle activité créée')
                                ->html('<p>Une nouvelle activité a été créée : ' . $calendar->getTitle() . '</p>');

                            $mailer->send($emailMessage);
                        }
                    }
                }

                // Enregistrement de l'activité
                $calendar->setActive(1)
                    ->setAllDay(false)
                    ->setBackgroundColor("#31F74F")
                    ->setBorderColor("#BBF0DA")
                    ->setTextColor("#FFF");
                $calendar->setEntreprise($this->entreprise);

                $entityManager->persist($calendar);
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

        return $this->renderForm('agenda/calendar/new.html.twig', [
            'calendar' => $calendar,
            'form' => $form,
        ]);
    }
}