<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Repository\BookingRepository;
use App\Repository\HardwareRepository;
use DateTime;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

#[Route('/api/booking', name: 'api_booking_')]
class BookingController extends AbstractController
{
    private BookingRepository $bookingRepository;
    private HardwareRepository $hardwareRepository;
    private Security $security;
    public function __construct(BookingRepository $bookingRepository, HardwareRepository $hardwareRepository, Security $security)
    {
        $this->bookingRepository = $bookingRepository;
        $this->hardwareRepository = $hardwareRepository;
        $this->security = $security;
    }

    #[Route('/create', name: 'create')]
    public function createBooking(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $newBooking = new Booking();
            $startDateTime = DateTime::createFromFormat('YYYY-MM-DD HH', $request->request->get('startDateTime'));
            $endDateTime = DateTime::createFromFormat('YYYY-MM-DD HH', $request->request->get('endDateTime'));
            $newBooking->setStartDate(DateTimeImmutable::createFromMutable($startDateTime));
            $newBooking->setEndDate(DateTimeImmutable::createFromMutable($endDateTime));
            $newBooking->setUser($this->security->getUser());
            $this->bookingRepository->persist($newBooking, true);
            return $this->render('success_page.html.twig', [
                'user' => $newBooking->getUser(),
                'hardwareName' => $newBooking->getHardware()->getName(),
                'hardwareIp' => $newBooking->getHardware()->getIpV4(),
                'startDate' => $newBooking->getStartDate()->format('Y-m-d H:i:s'),
                'endDate' => $newBooking->getEndDate()->format('Y-m-d H:i:s')
            ]);
        }
        if ($request->isMethod('GET') && $request->query->has('date') && $request->query->has('hardware')) {
            $bookables = $this->bookingRepository->getBookable(
                    DateTime::createFromFormat("YYYY-MM-DD", $request->query->get('date')),
                    $request->query->get('hardware'),
                    $request->query->has('booking_length') ? $request->query->get('booking_length') : 1
            );
            return new JsonResponse(json_encode($bookables));
        }
        return $this->render('bookingPage/booking_page.html.twig', [
            'hardwareList' => $this->hardwareRepository->findAll(),
            'isAdmin' => $this->isGranted('ROLE_ADMIN')
        ]);
    }
}