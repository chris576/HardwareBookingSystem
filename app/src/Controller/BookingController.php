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

    #[Route('/success', name: 'success')]
    public function bookingSuccessPage(Request $request): Response
    {
        $hardware = $this->hardwareRepository->find($request->query->get('hardware'));
        return $this->render('success_page.html.twig', [
            'user' => $request->query->get('user'),
            'hardwareName' => $hardware->getName(),
            'hardwareIp' => $hardware->getIpV4(),
            'hasStartDateTime' => $request->query->get('hasStartDateTime'),
            'hasEndDateTime' => $request->query->get('hasStartDateTime')
        ]);
    }

    #[Route('/create', name: 'create')]
    public function createBooking(Request $request): Response|JsonResponse
    {
        if ($request->isMethod('POST')) {
            
            $hasStartDateTime = $request->request->has('startDateTime');
            $hasEndDateTime = $request->request->has('endDateTime');
            $hasHardwareId = $request->request->has('hardware');

            if (!$hasStartDateTime || !$hasEndDateTime || !$hasHardwareId) {
                return new Response('Some parameter was missing.', Response::HTTP_BAD_REQUEST);
            }

            $hardware = $this->hardwareRepository->find($request->request->get('hardware'));

            if ($hardware == null) {
                return new Response('Hardware was not found.', Response::HTTP_BAD_REQUEST);
            }

            $newBooking = new Booking();
            $startDateTime = DateTime::createFromFormat('Y-m-d H:i:s', $request->request->get('startDateTime'));
            $endDateTime = DateTime::createFromFormat('Y-m-d H:i:s', $request->request->get('endDateTime'));
            $newBooking->setStartDate(DateTimeImmutable::createFromMutable($startDateTime));
            $newBooking->setEndDate(DateTimeImmutable::createFromMutable($endDateTime));
            $newBooking->setUser($this->security->getUser());
            $newBooking->setHardware($hardware);
            $this->bookingRepository->persist($newBooking, true);

            return $this->redirectToRoute('api_booking_success', [
                [
                    'user' => $newBooking->getUser(),
                    'hardwareName' => $newBooking->getHardware()->getName(),
                    'hardwareIp' => $newBooking->getHardware()->getIpV4(),
                    'hasStartDateTime' => $newBooking->getStartDate()->format('Y-m-d H:i:s'),
                    'hasEndDateTime' => $newBooking->getEndDate()->format('Y-m-d H:i:s')
                ]
            ]);
        }

        if ($request->isMethod('GET') && $request->query->has('date') && $request->query->has('hardware')) {
            $hardware = $request->query->get('hardware');
            if ($request->query->get('date') == date('Y-m-d')) {
                $bookingDateTime = DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s'));
                $bookingDateTime->setTime(intval($bookingDateTime->format('H')) + 1, 0, 0);
            } else {
                $bookingDateTime = DateTime::createFromFormat("Y-m-d H:i:s", $request->query->get('date').' 00:00:00');
            }
            $bookables = $this->bookingRepository->getBookable(
                $bookingDateTime,
                $hardware,
                ($request->query->has('booking_length') && $request->query->get('booking_length') != null) ? $request->query->get('booking_length') : 1
            );
            return new JsonResponse($bookables);
        }

        return $this->render('bookingPage/booking_page.html.twig', [
            'hardwareList' => $this->hardwareRepository->findAll(),
            'isAdmin' => $this->isGranted('ROLE_ADMIN')
        ]);
    }
}