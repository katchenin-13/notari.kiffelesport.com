<?php

namespace App\Service;

use App\Entity\ModuleGroupePermition;
use App\Entity\ConfigApp;
use App\Entity\Groupe;
use App\Entity\Identification;
use App\Entity\Prestataire;
use App\Entity\UserFront;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Security;

use Psr\Container\ContainerInterface;
use function PHPUnit\Framework\isEmpty;

class Menu
{

    private $em;
    private $route;
    private $container;
    private $security;
    private $repo;

    private $resp;
    private $tableau = [];
    private  const IN_MENU_PRINCIPAL = 1;


    public function __construct(EntityManagerInterface $em, RequestStack $requestStack, RouterInterface $router, Security $security)
    {
        $this->em = $em;

        if ($requestStack->getCurrentRequest()) {
            $this->route = $requestStack->getCurrentRequest()->attributes->get('_route');
            $this->container = $router->getRouteCollection()->all();
            $this->security = $security;
        }
        //dd($this->security->getUser()->getGroupe()->getName());
        //foreach($this->container as $key => $value){

        //  if(str_contains($key,'index')){
        //   $this->tableau [] = [
        // $key => str_replace('_',' ',$key)
        //  ];
        //}

        //  }

        // dd( $this->tableau);
        // if($this->getPermission() == null){
        // dd($this->getPermission());
        // }
        //dd($this->getPermission());
        /* if(!$this->getPermission()){
            dd("rrrr");
        }*/
        //$this->getPermission();
    }
    public function getGroupeName()
    {
        return $this->security->getUser()->getGroupe()->getName();
    }
    public function getGroupeCode()
    {
        return $this->security->getUser()->getGroupe()->getCode();
    }
    public function getRoute()
    {
        return $this->route;
    }
    public function getNamePrestataire($reference)
    {
        return $this->em->getRepository(UserFront::class)->findOneBy(['reference' => $reference]);
    }
    public function listeModule()
    {

        return $this->em->getRepository(ModuleGroupePermition::class)->afficheModule($this->security->getUser()->getGroupe()->getId());
    }






    public function listeGroupeModule()
    {
        //dd($this->em->getRepository(ModuleGroupePermition::class)->affiche($this->security->getUser()->getGroupe()->getId()));

        return $this->em->getRepository(ModuleGroupePermition::class)->affiche($this->security->getUser()->getGroupe()->getId(), 1);
    }

    public function getIdentification($dossier)
    {
        //dd($this->em->getRepository(ModuleGroupePermition::class)->affiche($this->security->getUser()->getGroupe()->getId()));

        return $this->em->getRepository(Identification::class)->getData($dossier);
    }

    public function findParametre()
    {

        return $this->em->getRepository(ConfigApp::class)->findConfig();
    }
    public function getTest()
    {
        return "#DDAD59";
    }
    public function getPermission()
    {
        $repo = $this->em->getRepository(ModuleGroupePermition::class)->getPermission($this->security->getUser()->getGroupe()->getId(), $this->route);
        //dd($repo);
        if ($repo != null) {
            return $repo['code'];
        } else {
            return $repo;
        }
    }

    public function getPermissionIfDifferentNull($group, $route)
    {
        $repo = $this->em->getRepository(ModuleGroupePermition::class)->getPermission($group, $route);
        //dd($repo);
        if ($repo != null) {
            return $repo['code'];
        } else {
            return $repo;
        }
    }

    public function liste()
    {


        return  $repo = $this->em->getRepository(Groupe::class)->afficheGroupes();
    }

    public function listeParent()
    {

        return $this->em->getRepository(Groupe::class)->affiche();
    }
    //public function listeModule
    public function listeGroupe()
    {
        $array = [
            'module' => 'modules',
            'app_config_parametre_index' => 'Parametrage général',
            'app_utilisateur_groupe_index' => 'Gestion groupe utilisateur',
            'app_utilisateur_utilisateur_index' => 'Gestion des utilisateur',
            // 'app_utilisateur_employe_index' => 'Liste des employés',
            // 'app_utilisateur_permition_index' => 'Liste des permissions',
            // 'app_utilisateur_groupe_module_index' => 'Liste des groupes modules',
            // 'app_utilisateur_module_index'=>'Liste des modules',
            // 'app_utilisateur_permition_index'=>'Liste des permissions',
            // 'app_utilisateur_module_groupe_permition_index'=>'Liste des module_groupe_permitions',

            'app_config_parametre_ls'=>'Paramétrages',
            // 'app_parametre_icon_index'=>'Les icons',
            // 'app_parametre_config_app_index'=>'Configuration de l\'application',
            //'app_parametre_entreprise_index'=>'Liste des entreprises',
            //'app_parametre_service_index '=>'Liste des services',
            //nouveau menu"
            'app_parametre_fonction_index'=> 'Liste de fonctions',
            'app_utilisateur_employe_index'=>'Liste des employés',
            'app_utilisateur_permition_index'=>'Liste des permissions',
            //encien menu
            // 'app_utilisateur_groupe_module_index'=>'Liste des groupes modules',
            // 'app_utilisateur_module_index'=>'Liste des modules',
            // 'app_utilisateur_permition_index'=>'Liste des permissions',
            // 'app_utilisateur_module_groupe_permition_index'=>'Liste des module_groupe_permitions',

            //configuration
            'app_parametre_civilite_index'=>'Liste des civilites',
            'app_parametre_type_client_index' => 'Liste des types clients',
            'app_parametre_type_index' => 'Liste des types d\'actes',
            'app_parametre_workflow_index'=>'Liste des des process de traitement des dossiers',

            //Gestion des dossiers
            'app_parametre_conservation_index'=>'Liste des conservations',//gestion des conversations
            'app_config_parametre_client_index' => 'Liste des clients',//gestion des clients
            'app_client_client_index'=>'Liste des clients(Particuliers et entreprises)',//sous menu de gestion des clients
            'app_config_parametre_dossier_index' => 'Liste des actes',//gestion des actes
            'app_acte_dossier_index' => 'En cours de traitement et Finalisés',//sous menu de gestion des actes
            'app_dossier_archive_index'=>'Liste des dossiers archivés', //sous menu de gestion des actes
           // 'app_actes_acte_constitution_index' => 'Liste des acte_constitutions',
            'app_actes_enregistrement_index' => 'Liste des actes enregistrements',
            //Gestion des activités
            'app_config_parametre_agenda_index'=>'Liste des activités', //la route pour le menu
            'app_agenda_calendar_index'=> 'Activités programmées',//

            //Gestion des couriers
            'app_courrier_courier_arrive_index'=>'couurier arrivé',
            'app_courrier_courier_depart_index'=>'couurier depart',
            'app_courrier_courier_interne_index' =>'couurier interne',
        ];

        return $array;
    }
    //    public function verifyanddispatch() {
    //
    //
    //
    //    }
}
