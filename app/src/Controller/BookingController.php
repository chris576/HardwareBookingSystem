<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Form\BookingType;
use App\Repository\BookingRepository;
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
    private Security $security;
    public function __construct(BookingRepository $bookingRepository, Security $security)
    {
        $this->bookingRepository = $bookingRepository;
        $this->security = $security;
    }

    #[Route('/create', name: 'create')]
    public function createBooking(Request $request): Response
    {
        $newBooking = new Booking();
        $bookingForm = $this->createForm(BookingType::class, $newBooking);
        $bookingForm->handleRequest($request);

        if ($bookingForm->isSubmitted() && $bookingForm->isValid()) {
            $startDateTime = $bookingForm->get('startDateTime')->getData();
            
            $endDateTimeWith = function (int $length) use ($startDateTime): \DateTimeInterface {
                $endDateTime = clone $startDateTime;
                $endDateTime->modify('+'.$length.' hour');
                return $endDateTime;
            };

            $endDateTime = $bookingForm->has('length')
                // Remember: Modifying endTime is just allowed as ROLE_ADMIN => endTime is only set, if ROLE_ADMIN
                ? $endDateTimeWith->call($this, $bookingForm->get('length')->getData())
                // If ROLE_USER, endTime is not set, so its set by default to +1 hour.
                : $endDateTimeWith->call($this, 1);

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
        return $this->render('bookingPage/booking_page.html.twig', [
            'bookingForm' => $bookingForm->createView()
        ]);
    }
}
