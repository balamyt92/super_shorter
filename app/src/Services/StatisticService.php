<?php


namespace App\Services;


use App\Entity\Link;
use App\Entity\StatisticImage;
use App\Entity\StatisticLink;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

class StatisticService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * StatisticService constructor.
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function imageShowed($image): void
    {
        $record = new StatisticImage();
        $record->setImage($image);
        $record->setDate(new DateTimeImmutable());
        $this->em->persist($record);
        $this->em->flush();
    }

    public function followingLink(Link $link, $fingerprint): void
    {
        $statisticItem = new StatisticLink();
        $statisticItem->setLink($link);
        $statisticItem->setFingerprint($fingerprint);
        $statisticItem->setDate(new DateTimeImmutable());
        $this->em->persist($statisticItem);
        $this->em->flush();
    }
}
