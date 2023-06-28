<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use \Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name:'home')]
    public function index()
    {
        return $this->render('base.html.twig');
    }
}