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

    #[Route('/post', name: 'post')]
    public function postBooking(Request $request): Response
    {
        $hardwareId = $request->request->get('hardwareId');
        $start = $request->request->get('start');
        $end = $request->request->get('end');

        if ($hardwareId == null || $start == null || $end == null) {
            return new Response('', Response::HTTP_NOT_FOUND);
        }

        $hardware = $this->hardwareRepository->find($hardwareId);

        if ($hardware == null) {
            return new Response('', Response::HTTP_NOT_FOUND);
        }
        $newBooking = new Booking();
        $newBooking->setHardware($hardware);
        $newBooking->setStartDate(new DateTimeImmutable($start));
        $newBooking->setEndDate(new DateTimeImmutable($end));
        $this->bookingRepository->persist($newBooking, true);
        return new Response('', Response::HTTP_OK);
    }

    #[Route('/reserved', name: 'blocked_time_spans')]
    public function getReservedTimeSpans(Request $request): Response
    {
        $hardwareId = $request->get("hardwareId");
        $date = $request->get('date');

        if ($hardwareId == null || $date == null) {
            return new Response(Response::HTTP_BAD_REQUEST);
        }

        $data = $this->bookingRepository->findReservedTimeSpans($hardwareId, $date);
        return new JsonResponse($data);
    }
}