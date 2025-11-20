<?php

namespace App\Repository;

use App\Entity\Animal;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Animal>
 */
class AnimalRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Animal::class);
    }

    /**
     * Find all animals owned by a specific user
     *
     * @param string $userId
     * @return Animal[]
     */
    public function findByOwner(string $userId): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.owner = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('a.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}

