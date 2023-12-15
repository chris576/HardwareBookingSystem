<?php

namespace App\Repository;

use App\Entity\Hardware;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Hardware>
 *
 * @method Hardware|null find($id, $lockMode = null, $lockVersion = null)
 * @method Hardware|null findOneBy(array $criteria, array $orderBy = null)
 * @method Hardware[]    findAll()
 * @method Hardware[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HardwareRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Hardware::class);
    }

    public function save(Hardware $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Hardware $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByUserIdentifier($identifier): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
            SELECT distinct h.id, h.name, h.ip_v4, h.description FROM hardware as h
            JOIN role_hardware as rh ON rh.hardware_id = h.id
            JOIN role_user as ru ON ru.role_id = rh.role_id
            JOIN `user` as u ON u.email = :email AND ru.user_id = u.id
        ';
        $resultSet = $conn->executeQuery($sql, ['email' => $identifier]);
        return $resultSet->fetchAllAssociative();
    }

    //    /**
//     * @return Hardware[] Returns an array of Hardware objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('h.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

    //    public function findOneBySomeField($value): ?Hardware
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
