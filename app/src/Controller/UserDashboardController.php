<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

#[Route('/user/dashboard', name: 'app_user_dashboard_')]
class UserDashboardController extends AbstractController
{
    private Security $security;

    private UserRepository $userRepository;

    public function __construct(Security $security, UserRepository $userRepository)
    {
        $this->security = $security;
        $this->userRepository = $userRepository;
    }

    #[Route('/', name: 'index')]
    public function index(): Response
    {
        if ($this->security->getUser() == null) {
            return new Response(Response::HTTP_NOT_FOUND);
        }
        $user = $this->userRepository->findOneBy(['email' => $this->security->getUser()->getUserIdentifier()]);
        return $this->render('user/dashboard.html.twig', [
            'bookings' => $user->getBookings()
        ]);
    }
}
