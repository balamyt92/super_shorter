<?php

namespace App\Repository;

use App\Entity\StatisticLink;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method StatisticLink|null find($id, $lockMode = null, $lockVersion = null)
 * @method StatisticLink|null findOneBy(array $criteria, array $orderBy = null)
 * @method StatisticLink[]    findAll()
 * @method StatisticLink[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StatisticLinkRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, StatisticLink::class);
    }

    // /**
    //  * @return StatisticLink[] Returns an array of StatisticLink objects
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
    public function findOneBySomeField($value): ?StatisticLink
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
