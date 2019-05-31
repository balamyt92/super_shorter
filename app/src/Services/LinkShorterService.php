<?php

namespace App\Services;

use App\Entity\Link;
use App\Utils\LinkNameExistException;
use App\Utils\LinkNameInvalidException;
use App\Utils\LinkNameLongException;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class LinkShorterService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * LinkSorterService constructor.
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param string            $source
     * @param string            $fingerprint
     * @param DateTimeImmutable $expire_at
     *
     * @param bool              $is_commercial
     *
     * @return string
     * @throws Exception
     */
    public function generate(string $source, $fingerprint = null, $expire_at = null, $is_commercial = false): string
    {
        // будем использовать базу как счетчик
        // для этого запишем сначала без укороченной ссылки что бы получить id
        // да это костыль
        // TODO: сделать выделенный каунтер в виде микросервиса :D
        $link = new Link();
        $link->setSource($source);
        $link->setShort((string)random_int(0, 10000));
        $link->setCreateAt(new DateTimeImmutable());
        $link->setUser($fingerprint);
        if ($expire_at) {
            $link->setExpireAt($expire_at);
        }
        if ($is_commercial) {
            $link->setIsCommercial($is_commercial);
        }
        $this->em->persist($link);
        $this->em->flush();

        // настоящая генерация ссылки на основе ранее полученного id
        // префикс на основе года позволит практически исключить генерацию слов из участвующих в роутинге приложения
        // и тех что пользователи захотят создать сами
        $short = $this->makeLink((int)date('Y'), $link->getId());
        $link->setShort($short);
        $this->em->persist($link);
        $this->em->flush();

        return $short;
    }

    /**
     * @param int $prefix
     * @param int $number
     *
     * @return string
     * @throws Exception
     */
    private function makeLink($prefix, $number): string
    {
        $codes_prefix = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $prefix_str = $this->numberToStr($prefix, $codes_prefix);
        // создаем иллюзию рандома ссылки
        $postfix_str = $this->numberToStr(random_int($number * 1000, ($number * 1000) + 999));

        return $prefix_str . $postfix_str;
    }

    /**
     * Перевод числа в другую систему исчисления на основе переданного словаря
     *
     * @param int|float $number
     * @param string    $codes
     *
     * @return string
     */
    private function numberToStr(
        $number,
        $codes = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'
    ): string {
        $out = '';
        $chars_length = mb_strlen($codes);
        while ($number > $chars_length - 1) {
            $key = $number % $chars_length;
            $number = floor($number / $chars_length) - 1;
            $out = $codes[$key] . $out;
        }

        return $out;
    }

    public function generateByCustomName(
        string $url,
        string $customName,
        $fingerprint = null,
        $expire_at = null,
        $is_commercial = false
    ): string {
        $repo = $this->em->getRepository(Link::class);

        if (mb_strlen($customName) > 100) {
            throw new LinkNameLongException('Max link length 100 character');
        }

        if (mb_strlen($customName) < 3) {
            throw new LinkNameLongException('Min link length 3 character');
        }

        if (preg_match('/[a-z\-\.0-9]+/m', $customName) !== 1) {
            throw new LinkNameInvalidException('Link includes invalid symbols');
        }

        if ($repo->count(['short' => $customName]) > 0) {
            throw new LinkNameExistException('Link already exist');
        }

        $link = new Link();
        $link->setShort($customName);
        $link->setSource($url);
        $link->setCreateAt(new DateTimeImmutable());
        $link->setUser($fingerprint);
        if (null !== $expire_at) {
            $link->setExpireAt(new DateTimeImmutable($expire_at));
        }
        if ($is_commercial) {
            $link->setIsCommercial($is_commercial);
        }

        $this->em->persist($link);
        $this->em->flush();

        return $link->getShort();
    }

    public function getCommercialImage($path): string
    {
        $image = null;

        $directory = new \RecursiveDirectoryIterator($path);
        $iterator = new \RecursiveIteratorIterator($directory);
        $files = [];
        foreach ($iterator as $info) {
            if (!$info->isDir()) {
                $files[] = $info->getFilename();
            }
        }
        shuffle($files);

        return $files[0];
    }
}
