<?php

namespace App\Repository;

use App\Entity\PooLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PooLog>
 */
class PooLogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PooLog::class);
    }

    /**
     * Find poo logs for a specific animal, ordered by date (most recent first)
     *
     * @param string $animalId
     * @param int $limit
     * @return PooLog[]
     */
    public function findRecentByAnimal(string $animalId, int $limit = 30): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.animal = :animalId')
            ->setParameter('animalId', $animalId)
            ->orderBy('p.recordedAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Find poo logs within a date range for an animal
     *
     * @param string $animalId
     * @param \DateTimeInterface $startDate
     * @param \DateTimeInterface $endDate
     * @return PooLog[]
     */
    public function findByAnimalAndDateRange(string $animalId, \DateTimeInterface $startDate, \DateTimeInterface $endDate): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.animal = :animalId')
            ->andWhere('p.recordedAt BETWEEN :startDate AND :endDate')
            ->setParameter('animalId', $animalId)
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->orderBy('p.recordedAt', 'ASC')
            ->getQuery()
            ->getResult();
    }
}

