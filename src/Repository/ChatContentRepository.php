<?php

namespace App\Repository;

use App\Entity\ChatContent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ChatContent|null find($id, $lockMode = null, $lockVersion = null)
 * @method ChatContent|null findOneBy(array $criteria, array $orderBy = null)
 * @method ChatContent[]    findAll()
 * @method ChatContent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChatContentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ChatContent::class);
    }

    // /**
    //  * @return ChatContent[] Returns an array of ChatContent objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ChatContent
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
