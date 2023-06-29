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

    public function findAllAsNames(): array
    {
        return $this
            ->createQueryBuilder('h')
            ->select('h.name')
            ->getQuery()
            ->getResult();
    }
}
