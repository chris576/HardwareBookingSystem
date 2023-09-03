<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class HomeController extends AbstractController
{
    private UrlGeneratorInterface $urlGenerator;
    public function __construct(UrlGeneratorInterface $urlGenerator) {
        $this->urlGenerator = $urlGenerator;
    }

    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        if (!$this->isGranted('ROLE_USER')) {
            return $this->render('landing_page.html.twig');
        }
        return $this->redirect($this->urlGenerator->generate('api_booking_create'));
    }
}
