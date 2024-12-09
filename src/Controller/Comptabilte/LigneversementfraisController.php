<?php

namespace App\Controller\Comptabilte;

use App\Entity\Ligneversementfrais;
use App\Form\LigneversementfraisType;
use App\Repository\LigneversementfraisRepository;
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
use App\Entity\Compte;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
#[Route('/ads/comptabilte/ligneversementfrais')]
class LigneversementfraisController extends BaseController
{
    const INDEX_ROOT_NAME = 'app_comptabilte_ligneversementfrais_index';

 const TAB_ID = 'parametre-tabs';

   
public function getdata($idR){
        
}


    #[Route('/{id}/edit', name: 'app_comptabilte_ligneversementfrais_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Compte $compte,LigneversementfraisRepository $ligneversementfraisRepository, Inscription $inscription, TypeFrais $typeFrais, EntityManagerInterface $entityManager, FormError $formError): Response
    {

        $form = $this->createForm(LigneversementfraisType::class, $compte, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_comptabilte_ligneversementfrais_edit', [
                'id' => $compte->getId()
            ])
        ]);

        $data = null;
        $url = null;
        $tabId = null;
        $statut = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = '';

            $montant = (int) $form->get('montant')->getData();
            $date = $form->get('datePaiement')->getData();
            $somme = 0;

            
            $lignes = $ligneversementfraisRepository->findBy(['compte' => $compte->getId()]);

            if ($lignes) {

                foreach ($lignes as $key => $info) {
                    $somme += (int)$info->getMontantverse();
                    $resteAPayer = abs((int)$compte->getMontant() - $somme);
                }
            } else {
                $resteAPayer = abs((int)$compte->getMontant());
            }


            if ($form->isValid()) {


                if ($resteAPayer >= $montant) {
                   
                    $ligneversementfrai = new Ligneversementfrais();
                    $ligneversementfrai->setDateversementfrais($date);
                    $ligneversementfrai->setCompte($compte);
                    $ligneversementfrai->setMontantverse($montant);
                    $entityManager->persist($ligneversementfrai);
                    $entityManager->flush();

                    $compte->setSolde($resteAPayer);

                    $entityManager->persist($compte);   
                    $entityManager->flush();

                    $load_tab = true;
                    $statut = 1;

                    $message = sprintf('Opération effectuée avec succès');
                    $this->addFlash('success', $message);
                } else {

                    $statut = 0;

                    $message = sprintf('Désole échec de paiement car le montant  saisi est superieur au montant  [%s] qui reste à payer pour un montant total de  %s', $montant, $resteAPayer);
                    $this->addFlash('danger', $message);
                }


                $url = [
                    'url' => $this->generateUrl('app_config_frais_paiement_index', [
                        'id' => $compte->getId()
                    ]),
                    'tab' => '#module0',
                    'current' => '#module0'
                ];

                $tabId = self::TAB_ID;
                $redirect = $url['url'];

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
                return $this->json(compact('statut', 'message', 'redirect', 'data', 'url', 'tabId'), $statutCode);

            } else {
                if ($statut == 1) {
                    return $this->redirect($redirect, Response::HTTP_OK);
                }
            }
        }

        return $this->renderForm('comptabilte/ligneversementfrais/edit.html.twig', [
            'ligneversementfrai' => $ligneversementfrai,
            'form' => $form,
        ]);
    }

}
