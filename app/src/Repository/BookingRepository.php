<?php

namespace App\Repository;

use App\Entity\Booking;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
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

    public function isBookable(\DateTime $startDateTime, \DateTime $endDateTime, int $hardwareId): bool
    {
        $query = $this->getEntityManager()->getConnection()->createQueryBuilder()
            ->select('COUNT(*)')
            ->from('booking')
            ->where('DATE_ADD(:startDateTime, INTERVAL 1 SECOND) between start_date AND end_date')
            ->orWhere('DATE_SUB(:endDateTime, INTERVAL 1 SECOND) between start_date AND end_date')
            ->andWhere(':hardwareId = hardware_id');
        $resultSet = $this->getEntityManager()->getConnection()->executeQuery($query, [
            'hardwareId' => $hardwareId,
            'startDateTime' => $startDateTime->format('Y-m-d H:i:s'),
            'endDateTime' => $endDateTime->format('Y-m-d H:i:s')
        ]);
        return $resultSet->fetchOne() == 0;
    }

    public function getBookable(\DateTime $bookingDate, int $hardwareId, int $bookingLength = 1): mixed
    {
        $query = 'CALL calculateBookables(:bookingDate, :hardwareId, :bookingLength);';
        $resultSet = $this->getEntityManager()->getConnection()->executeQuery($query, [
            'hardwareId' => $hardwareId,
            'bookingDate' => $bookingDate->format('Y-m-d H:i:s'),
            'bookingLength' => $bookingLength
        ]);
        return $resultSet->fetchAllAssociative();
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