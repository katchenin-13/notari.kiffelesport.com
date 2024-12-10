<?php

namespace App\Controller;


use App\Service\FormError;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SiteController extends AbstractController
{
    #[Route(path: '/immo-plus', name: 'app_immo_plus', methods: ['GET'])]
    public function index(Request $request): Response
    {
        return $this->render('site/base.html.twig');
    }
}
