<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Repository\BookingRepository;
use App\Repository\HardwareRepository;
use App\Repository\UserRepository;
use App\Service\MailServiceContainer;
use DateTime;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

#[Route('/api/booking', name: 'api_booking_')]
class BookingController extends AbstractController
{
    private BookingRepository $bookingRepository;
    private HardwareRepository $hardwareRepository;
    private UserRepository $userRepository;
    private MailServiceContainer $mailServiceContainer;
    private Security $security;
    public function __construct(BookingRepository $bookingRepository, HardwareRepository $hardwareRepository, UserRepository $userRepository, Security $security, MailServiceContainer $mailServiceContainer)
    {
        $this->bookingRepository = $bookingRepository;
        $this->hardwareRepository = $hardwareRepository;
        $this->security = $security;
        $this->userRepository = $userRepository;
        $this->mailServiceContainer = $mailServiceContainer;
    }

    #[Route('/read', name: 'read', methods: 'GET')]
    public function readBookings(Request $request): Response
    {
        $hardwareId = $request->query->has('hardware') ? $request->query->get('hardware') : 1;
        if (!$request->query->has('date') || $request->query->get('date') == date('Y-m-d')) {
            $bookingDateTime = new DateTime('now', new \DateTimeZone('Europe/Berlin'));
            $bookingDateTime->setTime(intval($bookingDateTime->format('H')) + 1, 0, 0);
        } else {
            $bookingDateTime = DateTime::createFromFormat("Y-m-d H:i:s", $request->query->get('date') . ' 00:00:00');
        }
        $booking_length = ($request->query->has('booking_length') && $this->security->isGranted('ROLE_ADMIN')) ? $request->query->get('booking_length') : 1;
        $bookables = $this->bookingRepository->getBookable(
            $bookingDateTime,
            $hardwareId,
            $booking_length
        );
        return $this->render('booking/page.booking.html.twig', [
            'hardwareList' => $this->hardwareRepository->findByUserIdentifier($this->security->getUser()->getUserIdentifier()),
            'isAdmin' => $this->isGranted('ROLE_ADMIN'),
            'bookables' => $bookables,
            'bookingLength' => $booking_length,
            'bookingDate' => $bookingDateTime,
            'selectedHardwareId' => $hardwareId
        ]);
    }

    #[Route('/create', name: 'create', methods: 'POST')]
    public function createBooking(Request $request): Response
    {
        $hasBookingSlot = $request->request->has('booking_slot');
        $hasHardwareId = $request->request->has('hardware');

        if (!$hasBookingSlot || !$hasHardwareId) {
            $this->addFlash('error', 'Die Parameter deiner Anfrage sind nicht korrekt.');
            return $this->redirectToRoute('api_booking_read');
        }

        $jsonBookingSlot = json_decode($request->request->get('booking_slot'), true);
        $startDateTime = $jsonBookingSlot['startDateTime'];
        $endDateTime = $jsonBookingSlot['endDateTime'];

        if (!$jsonBookingSlot || !$startDateTime || !$endDateTime) {
            $this->addFlash('error', 'Die Parameter in booking_slot sind nicht richtig definiert.');
            return $this->redirectToRoute('api_booking_read');
        }

        $hardwareId = $request->request->get('hardware');
        $hardware = $this->hardwareRepository->find($hardwareId);

        if (!$hardwareId) {
            $this->addFlash('error', 'Die angeforderte Hardware wurde nicht gefunden.');
            return $this->redirectToRoute('api_booking_read');
        }

        if ($this->userRepository->isUserAllowedToBook($this->security->getUser()->getUserIdentifier(), $hardwareId)) {
            $this->addFlash('error', 'Ihnen ist es nicht erlaubt, dieses Objekt zu buchen.');
            return $this->redirectToRoute('api_booking_read');
        }

        $newBooking = new Booking();
        $startDateTime = DateTime::createFromFormat('Y-m-d H:i:s', $startDateTime);
        $endDateTime = DateTime::createFromFormat('Y-m-d H:i:s', $endDateTime);

        if (!$this->bookingRepository->isBookable($startDateTime, $endDateTime, $hardwareId)) {
            $this->addFlash('error', 'Die Hardware "' . $hardware->getName() . '" ist zu diesem Zeitpunkt bereits belegt.');
            return $this->redirectToRoute('api_booking_read');
        }

        $newBooking->setStartDate(DateTimeImmutable::createFromMutable($startDateTime));
        $newBooking->setEndDate(DateTimeImmutable::createFromMutable($endDateTime));
        $newBooking->setUser($this->security->getUser());
        $newBooking->setHardware($hardware);
        $this->bookingRepository->persist($newBooking, true);

        // @TODO: Send Password as well to the local server.
        $this->mailServiceContainer->send(
            $this->getUser()->getUserIdentifier(),
            $this->getParameter('mail.sender.default'),
            "Ihre Zugangsdaten",
            'booking/mail.booking.html.twig',
            [
                'username' => $hardware->getVpnUserName(),
                'password' => (string) random_bytes(30),
                'hardwareName' => $newBooking->getHardware()->getName(),
                'startDate' => $newBooking->getStartDate(),
                'endDate' => $newBooking->getEndDate()
            ]);

        $this->addFlash('success', 'Deine Buchung der Hardware "' . $hardware->getName() . '" war erfolgreich.');

        return $this->redirectToRoute('api_booking_read', [
            'date' => $request->request->get('date'),
            'hardware' => $request->request->get('hardware'),
            'booking_length' => $request->request->get('booking_length')
        ]);
    }

    #[Route('/delete/{bId}', name: 'delete', methods: 'POST')]
    public function deleteBooking(Request $request, int $bId): Response
    {
        $booking = $this->bookingRepository->find($bId);
        if ($booking == null) {
            return new Response(Response::HTTP_NOT_FOUND);
        }
        $this->mailServiceContainer->send(
            $booking->getUser()->getEmail(),
            $this->getParameter('mail.sender.default'),
            'Stornierung Ihrer Buchung',
            'booking/mail.cancel.html.twig',
            [
                'hardwareName' => $booking->getHardware()->getName(),
                'startDate' => $booking->getStartDate(),
                'endDate' => $booking->getEndDate()
            ]
        );
        $this->bookingRepository->remove($booking, true);
        return new Response(Response::HTTP_OK);
    }
}