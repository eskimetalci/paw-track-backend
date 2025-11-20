<?php

namespace App\Repository;

use App\Entity\BlogPost;
use App\Enum\Species;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BlogPost>
 */
class BlogPostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BlogPost::class);
    }

    /**
     * Find all published blog posts
     *
     * @return BlogPost[]
     */
    public function findPublished(): array
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.published = :published')
            ->setParameter('published', true)
            ->orderBy('b.publishedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find published blog posts by species
     *
     * @param Species $species
     * @return BlogPost[]
     */
    public function findPublishedBySpecies(Species $species): array
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.published = :published')
            ->andWhere('b.targetSpecies = :species OR b.targetSpecies IS NULL')
            ->setParameter('published', true)
            ->setParameter('species', $species)
            ->orderBy('b.publishedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find published blog posts by tag
     *
     * @param string $tag
     * @return BlogPost[]
     */
    public function findPublishedByTag(string $tag): array
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.published = :published')
            ->andWhere('JSON_CONTAINS(b.tags, :tag) = 1')
            ->setParameter('published', true)
            ->setParameter('tag', json_encode($tag))
            ->orderBy('b.publishedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find blog post by slug
     *
     * @param string $slug
     * @return BlogPost|null
     */
    public function findOneBySlug(string $slug): ?BlogPost
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.slug = :slug')
            ->andWhere('b.published = :published')
            ->setParameter('slug', $slug)
            ->setParameter('published', true)
            ->getQuery()
            ->getOneOrNullResult();
    }
}

