<?php

namespace App\Controller\Configuration;

use App\Controller\BaseController;
use App\Repository\CiviliteRepository;
use App\Service\Breadcrumb;
use App\Service\Menu;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

#[Route('/admin/config/parametre/compte')]
class ConfigCompteController extends BaseController
{

    const INDEX_ROOT_NAME = 'app_config_parametre_compte_index';


    

    #[Route(path: '/', name: 'app_config_parametre_compte_index', methods: ['GET', 'POST'])]
    public function index(Request $request, Breadcrumb $breadcrumb): Response
    {

        $permission = $this->menu->getPermissionIfDifferentNull($this->security->getUser()->getGroupe()->getId(), self::INDEX_ROOT_NAME);

        /* if($this->menu->getPermission()){
             $redirect = $this->generateUrl('app_default');
             return $this->redirect($redirect);
             //dd($this->menu->getPermission());
         } */
        $modules = [
            [
                'label' => 'Compte non soldé ',
                'icon' => 'bi bi-list',
                'href' => $this->generateUrl('app_compte_frais_index', ['etat' => 'nonsolde'])
            ],
            [
                'label' => 'Compte non soldé',
                'icon' => 'bi bi-users',
                'href' => $this->generateUrl('app_compte_frais_index', ['etat' => 'solde'])
            ]


        ];

        $breadcrumb->addItem([
            [
                'route' => 'app_default',
                'label' => 'Tableau de bord'
            ],
            [
                'label' => 'Paramètres'
            ]
        ]);

        return $this->render('config/parametre/index.html.twig', [
            'modules' => $modules,
            'breadcrumb' => $breadcrumb,
            'permition' => $permission,
            'titre' => 'Paiement des frais et honoraires',
            'type' => 'frais',
            
        ]);
    }




    #[Route(path: '/{module}', name: 'app_config_parametre_dossier_ls', methods: ['GET', 'POST'])]
    public function liste(Request $request, string $module): Response
    {
        /**
         * @todo: A déplacer dans un service
         */
        $parametres = [];





        return $this->render('config/parametre/liste.html.twig', ['links' => $parametres[$module] ?? []]);
    }
}
