<?php

namespace App\Repository;

use App\Entity\NoticeParagraph;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method NoticeParagraph|null find($id, $lockMode = null, $lockVersion = null)
 * @method NoticeParagraph|null findOneBy(array $criteria, array $orderBy = null)
 * @method NoticeParagraph[]    findAll()
 * @method NoticeParagraph[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NoticeParagraphRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NoticeParagraph::class);
    }

    // /**
    //  * @return NoticeParagraph[] Returns an array of NoticeParagraph objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('n.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?NoticeParagraph
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
