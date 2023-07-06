<?php

namespace App\Repository;

use App\Entity\Booking;
use App\Entity\Hardware;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Booking>
 *
 * @method Booking|null find($id, $lockMode = null, $lockVersion = null)
 * @method Booking|null findOneBy(array $criteria, array $orderBy = null)
 * @method Booking[]    findAll()
 * @method Booking[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Booking::class);
    }

    private function saveToDb(Booking $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Booking $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    private function userIsAllowedToBook(User $user, Hardware $hardware) : bool {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT g FROM \Entity\UserGroup g WHERE :user IN g.userList AND :hardware IN g.hardware'
        )
        ->setParameters([ 
            'user' => $user, 
            'hardware' => $hardware
         ]);
         return sizeof($query->getArrayResult()) > 0;
    }

    private function isBlocked(\DateTimeImmutable $startDate, \DateTimeImmutable $endDate, Hardware $hardware) : bool {
        $entityManger = $this->getEntityManager(); 
        $result = $entityManger->createQuery(
            'SELECT b FROM \Entity\Booking b WHERE (:startDate >= b.startDate AND b.startDate < :endDate AND b.hardware == :hardware) 
            OR (:endDate > b.StartDate AND :endDate < b.endDate AND b.hardware == :hardware)'
        )
        ->setParameters([
            'startDate' => $startDate,
            'endDate' => $endDate,
            'hardware' => $hardware
        ])
        ->getArrayResult();
        return sizeof($result) > 0; 
    }

    public function save(User $user, Hardware $hardware, \DateTimeImmutable $startDate, \DateTimeImmutable $endDate, bool $flush) : Booking|null {
        if ($this->userIsAllowedToBook($user, $hardware) && !$this->isBlocked($startDate, $endDate, $hardware)) {
            $booking = new Booking();
            $booking->setUser($user); 
            $booking->setHardware($hardware);
            $booking->setStartDate($startDate);
            $booking->setEndDate($endDate);
            $this->saveToDb($booking, $flush);
            return $booking;
        }
        return null;
    }

    public function getReserved(\DateTimeImmutable $startDate, \DateTimeImmutable $endDate, Hardware $hardwareId): array {
        $entityManager = $this->getEntityManager(); 
        $query = $entityManager->createQuery(
            'SELECT b FROM \Entity\Booking b WHERE b.startDate >= :startDate AND b.endDate <= :endDate AND b.hardware.id == :hardwareId'
        )
        ->setParameters([
            'startDate' => $startDate, 
            'endDate' => $endDate, 
            'hardwareId' => $hardwareId
        ]);

        return $query->getArrayResult(); 
    }
}
