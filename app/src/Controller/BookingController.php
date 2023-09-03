<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Form\BookingType;
use App\Repository\BookingRepository;
use App\Repository\HardwareRepository;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/booking', name: 'api_booking_')]
class BookingController extends AbstractController
{
    private BookingRepository $bookingRepository;
    private HardwareRepository $hardwareRepository;

    public function __construct(BookingRepository $bookingRepository, HardwareRepository $hardwareRepository)
    {
        $this->bookingRepository = $bookingRepository;
        $this->hardwareRepository = $hardwareRepository;
    }

    #[Route('/create', name: 'create')]
    public function createBooking(Request $request): Response
    {
        $newBooking = new Booking();
        $bookingForm = $this->createForm(BookingType::class, $newBooking);
        $bookingForm->handleRequest($request);

        if ($bookingForm->isSubmitted() && $bookingForm->isValid()) {
            $dateString = $bookingForm->get('date')->getData()->format('Y-m-d');
            $startTimeString = $bookingForm->get('startTime')->getData()->format('H:i:s');
            $endTimeString = $bookingForm->get('endTime')->getData()->format('H:i:s');
            $newBooking->setStartDate(DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $dateString.' '.$startTimeString));
            $newBooking->setEndDate(DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $dateString.' '.$endTimeString));   
            $this->bookingRepository->persist($newBooking, true);
            return $this->render('success_page.html.twig', [
                'hardwareName' => $newBooking->getHardware()->getName(),
                'hardwareIp' => $newBooking->getHardware()->getIpV4(),
                'startDate' => $newBooking->getStartDate()->format('Y-m-d H:i:s'),
                'endDate' => $newBooking->getEndDate()->format('Y-m-d H:i:s')
            ]);
        }
        return $this->render('bookingPage/booking_page.html.twig', [
            'bookingForm' => $bookingForm->createView()
        ]);
    }
}