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

    public function findReservedTimeSpans(int $hardwareId, \DateTime $date) : array {
        $connection = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT b.start_date FROM booking as b
            WHERE b.hardware_id = :hardwareId AND Date(b.start_date) = :date
        ';

        $resultSet = $connection->executeQuery($sql, [ "hardwareId" => $hardwareId, "date" => $date->format('YYYY-MM-DD') ]);
        return $resultSet->fetchAllNumeric();
    }
}
