<?php

namespace App\Repository;

use App\Entity\StatisticImage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method StatisticImage|null find($id, $lockMode = null, $lockVersion = null)
 * @method StatisticImage|null findOneBy(array $criteria, array $orderBy = null)
 * @method StatisticImage[]    findAll()
 * @method StatisticImage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StatisticImageRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, StatisticImage::class);
    }

    // /**
    //  * @return StatisticImage[] Returns an array of StatisticImage objects
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
    public function findOneBySomeField($value): ?StatisticImage
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
