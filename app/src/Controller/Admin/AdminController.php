<?php

namespace App\Controller\Admin;

use App\Entity\Booking;
use App\Entity\Hardware;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        return parent::index();
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Admin Interface');
    }

    public function configureMenuItems(): iterable
    {
            yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
            
            yield MenuItem::section('Booking');
            yield MenuItem::linkToCrud('Manage Bookings', 'fa fa-booking', Booking::class)
                ->setController(BookingCrudController::class);

            yield MenuItem::section('Hardware');
            yield MenuItem::linkToCrud('Manage Hardware', 'fa fa-hw', Hardware::class)
                ->setController(HardwareCrudController::class);
    }
}
