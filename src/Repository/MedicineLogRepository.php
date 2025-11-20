<?php

namespace App\Repository;

use App\Entity\MedicineLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MedicineLog>
 */
class MedicineLogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MedicineLog::class);
    }

    /**
     * Find all active medications for a specific animal
     *
     * @param string $animalId
     * @return MedicineLog[]
     */
    public function findActiveByAnimal(string $animalId): array
    {
        $now = new \DateTime();
        
        return $this->createQueryBuilder('m')
            ->andWhere('m.animal = :animalId')
            ->andWhere('m.startDate <= :now')
            ->andWhere('m.endDate IS NULL OR m.endDate >= :now')
            ->setParameter('animalId', $animalId)
            ->setParameter('now', $now)
            ->orderBy('m.startDate', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find all medications for a specific animal
     *
     * @param string $animalId
     * @return MedicineLog[]
     */
    public function findByAnimal(string $animalId): array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.animal = :animalId')
            ->setParameter('animalId', $animalId)
            ->orderBy('m.startDate', 'DESC')
            ->getQuery()
            ->getResult();
    }
}

