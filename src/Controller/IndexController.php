<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    #[Route('/index', name: 'app_index')]
    public function index(): Response
    {
        return $this->render('index/index.html.twig', [
            'controller_name' => 'IndexController',
        ]);
    }

    #[Route('/faq', name: 'faq')]
    public function faq(): Response
    {
                return $this->render('index/faq.html.twig');

    }

    #[Route('/produits', name: 'produits')]
    public function produits(): Response
    {
                return $this->render('index/produits.html.twig');

    }

    #[Route('/smartplus', name: 'smartplus')]
    public function smartplus(): Response
    {
                return $this->render('index/smartplus.html.twig');

    }

    #[Route('/contact', name: 'contact')]
    public function contact(): Response
    {
                return $this->render('index/contact.html.twig');

    }
}
