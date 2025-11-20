<?php

namespace App\Repository;

use App\Entity\VaccineLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<VaccineLog>
 */
class VaccineLogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VaccineLog::class);
    }

    /**
     * Find all vaccines for a specific animal
     *
     * @param string $animalId
     * @return VaccineLog[]
     */
    public function findByAnimal(string $animalId): array
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.animal = :animalId')
            ->setParameter('animalId', $animalId)
            ->orderBy('v.administeredAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find upcoming vaccines that are due or will be due soon
     *
     * @param string $animalId
     * @param int $daysAhead Number of days to look ahead (default: 30)
     * @return VaccineLog[]
     */
    public function findUpcomingByAnimal(string $animalId, int $daysAhead = 30): array
    {
        $now = new \DateTime();
        $futureDate = (new \DateTime())->modify("+{$daysAhead} days");
        
        return $this->createQueryBuilder('v')
            ->andWhere('v.animal = :animalId')
            ->andWhere('v.nextDueDate IS NOT NULL')
            ->andWhere('v.nextDueDate BETWEEN :now AND :futureDate')
            ->setParameter('animalId', $animalId)
            ->setParameter('now', $now)
            ->setParameter('futureDate', $futureDate)
            ->orderBy('v.nextDueDate', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find overdue vaccines
     *
     * @param string $animalId
     * @return VaccineLog[]
     */
    public function findOverdueByAnimal(string $animalId): array
    {
        $now = new \DateTime();
        
        return $this->createQueryBuilder('v')
            ->andWhere('v.animal = :animalId')
            ->andWhere('v.nextDueDate IS NOT NULL')
            ->andWhere('v.nextDueDate < :now')
            ->setParameter('animalId', $animalId)
            ->setParameter('now', $now)
            ->orderBy('v.nextDueDate', 'ASC')
            ->getQuery()
            ->getResult();
    }
}

