<?php

namespace App\Repository;

use App\Entity\SessionContent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SessionContent|null find($id, $lockMode = null, $lockVersion = null)
 * @method SessionContent|null findOneBy(array $criteria, array $orderBy = null)
 * @method SessionContent[]    findAll()
 * @method SessionContent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SessionContentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SessionContent::class);
    }

    // /**
    //  * @return SessionContent[] Returns an array of SessionContent objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SessionContent
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
