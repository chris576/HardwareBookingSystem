<?php

namespace App\Repository;

use App\Entity\Booking;
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

    public function persist(Booking $entity, bool $flush = false): void
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

    public function getBookable(array $dateTimeRange, int $hardwareId, int $bookingLength = 1): array
    {
        return $this->getEntityManager()->createQuery(
            /** TODO: Temporäre Tabelle aus Buchbaren und eine aus nicht buchbare Zeiten. Dann Kreuzprodukt B * B * NB, mit Prüfung ob die Differenz gleich 1 ist, bzw. nicht innerhalb NB */
            /** UNTEN: Tabelle an buchbaren Zeiten. */
            'SELECT r, r2 FROM r FROM :dateTimeRange as r, :dateTimeRange as r2
                JOIN \Entity\Booking as b ON
                    /** Nur diejenigen Buchungen für die gewünschte Hardware dürfen betrachtet werden. */
                    b.hardware = :hardwareId
                    /** Alle Zeiten die zwischen start und ende liegen, also nicht buchbar sind, werden ignoriert */ 
                    AND (r not between b.startDate AND b.endDate OR r2 not between b.startDate AND b.endDate)
                    /** Alle Zeiten, in denen das Start oder Enddatum kleiner oder größer ist als das minimum/maximum von r, werden ignoriert. */
                    AND ( b.startDate between MIN(r) AND MAX(r) OR b.endDate between MIN(r) AND MAX(r) )
                /** Alle Zeiten, wo die Differenz von r1 und r2 der Buchungsdauer in Stunden ist.  */
                WHERE TIMESTAMPDIFF(HOUR, r, r2) IS :bookingLength
                /** R und R2 dürfen Zeiten, in denen die Buchung schon belegt ist, nicht überbuchen. */
                    AND ( b.startDate NOT BETWEEN r AND r2 OR b.endDate NOT BETWEEN r AND r2 )
            '
        )
            ->setParameters([
                'dateTimeRange' => $dateTimeRange,
                'bookingLength' => $bookingLength,
                'hardwareId' => $hardwareId
            ])
            ->getArrayResult();
    }

    //    private function userIsAllowedToBook(User $user, Hardware $hardware) : bool {
    //        $entityManager = $this->getEntityManager();
    //      $query = $entityManager->createQuery(
    //            'SELECT g FROM \Entity\UserGroup g WHERE :user IN g.userList AND :hardware IN g.hardware'
    //        )
    //        ->setParameters([ 
    //            'user' => $user, 
    //            'hardware' => $hardware
    //         ]);
    //         return sizeof($query->getArrayResult()) > 0;
    //    }
}
