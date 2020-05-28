<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    public function findBySlugs(String $categorySlug, String $themeSlug) {
        return $this->createQueryBuilder('c')
            ->where('t.slug = :themeSlug')
            ->andWhere('c.slug = :categorySlug')
            ->innerJoin('c.theme', 't')
            ->setParameters(['categorySlug' => $categorySlug, 'themeSlug' => $themeSlug])
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

}
