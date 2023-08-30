<?php

namespace App\Controller;

use App\Repository\BookingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/booking', name: 'booking_api_')]
class BookingController extends AbstractController
{
    private BookingRepository $bookingRepository; 

    public function __construct(BookingRepository $bookingRepository) 
    {
        $this->bookingRepository = $bookingRepository;
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