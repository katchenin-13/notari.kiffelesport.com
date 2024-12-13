<?php


namespace App\Controller\Configuration;

use App\Entity\Compte;
use App\Entity\Comptefour;
use App\Entity\CompteFournisseur;
use App\Service\Breadcrumb;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('admin/config/parametre/fournisseur/paiement')]
class ConfigMarcheController extends AbstractController
{

    const INDEX_ROOT_NAME = 'app_config_fournisseur_paiement_index';

    #[Route(path: '/fournisseur/compte/{id}', name: 'app_config_fournisseur_paiement_index', methods: ['GET', 'POST'])]
    // #[RoleMethod(title: 'Gestion des Paramètres', as: 'index')]
    public function indexConfigMarcheVersement(Request $request, Breadcrumb $breadcrumb, int $id): Response
    {
        $module = $request->query->get('module');


        $modules = [
            [
                'label' => 'DETAIL DES VERSEMENTS',
                'icon' => 'bi bi-list',
                'module' => 'gestion',
                'href' => $this->generateUrl('app_comptabilte_lignepaiementmarche_index', ['idM' => $id])
                //'href' => $this->generateUrl('app_inscription_liste_versements', ['id' => $id])
            ],
            [

                'label' => 'DETAIL Du COMPTE',
                'icon' => 'bi bi-list',
                'module' => 'general',
                'href' => $this->generateUrl('app_compte_fournisseur_show', ['id' => $id])
                // 'href' => $this->generateUrl('app_inscription_inscription_paiement_ok', ['id' => $id])
            ],

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


        if ($module) {
            $modules = array_filter($modules, fn($_module) => $_module['module'] == $module);
        }

        return $this->render('config/paiement/index_fournisseur.html.twig', [
            'modules' => $modules,
            'breadcrumb' => $breadcrumb,
            'id' => $id
        ]);
    }
}
