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
use Doctrine\ORM\EntityManagerInterface;

#[Route('/ads/comptabilte/ligneversementfrais')]
class LigneversementfraisController extends BaseController
{
const INDEX_ROOT_NAME = 'app_comptabilte_ligneversementfrais_index';

   

    #[Route('/new', name: 'app_comptabilte_ligneversementfrais_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, FormError $formError): Response
{
$ligneversementfrai = new Ligneversementfrais();
$form = $this->createForm(LigneversementfraisType::class, $ligneversementfrai, [
'method' => 'POST',
'action' => $this->generateUrl('app_comptabilte_ligneversementfrais_new')
]);
$form->handleRequest($request);

$data = null;
$statutCode = Response::HTTP_OK;

$isAjax = $request->isXmlHttpRequest();

    if ($form->isSubmitted()) {
    $response = [];
    $redirect = $this->generateUrl('app_comptabilte_ligneversementfrais_index');


    if ($form->isValid()) {

    $entityManager->persist($ligneversementfrai);
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
    return $this->json( compact('statut', 'message', 'redirect', 'data'), $statutCode);
    } else {
    if ($statut == 1) {
    return $this->redirect($redirect, Response::HTTP_OK);
    }
    }


    }

    return $this->renderForm('comptabilte/ligneversementfrais/new.html.twig', [
    'ligneversementfrai' => $ligneversementfrai,
    'form' => $form,
    ]);
}

    #[Route('/{id}/show', name: 'app_comptabilte_ligneversementfrais_show', methods: ['GET'])]
public function show(Ligneversementfrais $ligneversementfrai): Response
{
return $this->render('comptabilte/ligneversementfrais/show.html.twig', [
'ligneversementfrai' => $ligneversementfrai,
]);
}

    #[Route('/{id}/edit', name: 'app_comptabilte_ligneversementfrais_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Ligneversementfrais $ligneversementfrai, EntityManagerInterface $entityManager, FormError $formError): Response
{

$form = $this->createForm(LigneversementfraisType::class, $ligneversementfrai, [
'method' => 'POST',
'action' => $this->generateUrl('app_comptabilte_ligneversementfrais_edit', [
'id' => $ligneversementfrai->getId()
])
]);

$data = null;
$statutCode = Response::HTTP_OK;

$isAjax = $request->isXmlHttpRequest();


$form->handleRequest($request);

    if ($form->isSubmitted()) {
    $response = [];
    $redirect = $this->generateUrl('app_comptabilte_ligneversementfrais_index');


    if ($form->isValid()) {

    $entityManager->persist($ligneversementfrai);
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
    return $this->json( compact('statut', 'message', 'redirect', 'data'), $statutCode);
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

    #[Route('/{id}/delete', name: 'app_comptabilte_ligneversementfrais_delete', methods: ['DELETE', 'GET'])]
    public function delete(Request $request, Ligneversementfrais $ligneversementfrai, EntityManagerInterface $entityManager): Response
{
$form = $this->createFormBuilder()
->setAction(
$this->generateUrl(
'app_comptabilte_ligneversementfrais_delete'
, [
'id' => $ligneversementfrai->getId()
]
)
)
->setMethod('DELETE')
->getForm();
$form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
    $data = true;
    $entityManager->remove($ligneversementfrai);
    $entityManager->flush();

    $redirect = $this->generateUrl('app_comptabilte_ligneversementfrais_index');

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

return $this->renderForm('comptabilte/ligneversementfrais/delete.html.twig', [
'ligneversementfrai' => $ligneversementfrai,
'form' => $form,
]);
}
}