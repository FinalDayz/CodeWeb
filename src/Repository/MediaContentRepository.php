<?php

namespace App\Repository;

use App\Entity\MediaContent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MediaContent|null find($id, $lockMode = null, $lockVersion = null)
 * @method MediaContent|null findOneBy(array $criteria, array $orderBy = null)
 * @method MediaContent[]    findAll()
 * @method MediaContent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MediaContentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MediaContent::class);
    }

    // /**
    //  * @return MediaContent[] Returns an array of MediaContent objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?MediaContent
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
