<?php

namespace App\Controller;

use App\Entity\Link;
use App\Entity\StatisticLink;
use App\Services\StatisticService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StatisticController extends AbstractController
{
    /**
     * @Route("/statistic/{slug}", name="statistic")
     *
     * @param string  $slug
     * @param Request $request
     *
     * @return Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function index($slug, Request $request): Response
    {
        $doctrine = $this->getDoctrine();
        $link = $doctrine->getRepository(Link::class)->findOneBy(['short' => $slug]);
        if (null === $link) {
            return $this->render('index/not-found.html.twig');
        }

        $repo = $doctrine->getRepository(StatisticLink::class);
        $statisticItems = $repo->findBy(['link' => $link->getId()], [], 50);
        $uniq_users = $repo->createQueryBuilder('s')
            ->select('count(DISTINCT s.fingerprint)')
//            ->where('s.date BETWEEN DATE_SUB(NOW(),INTERVAL 14 DAY) and DATE_SUB(NOW(),INTERVAL 1 DAY)')
            ->getQuery()
            ->getSingleScalarResult();

        return $this->render('statistic/index.html.twig', [
            'items'      => $statisticItems,
            'link'       => $link,
            'short_link' => $this->getParameter('domain_link') . '/' . $link->getShort(),
            'uniq_users' => $uniq_users
        ]);
    }

    /**
     * @Route("/statistic/add/image", name="statistic_image_showed", methods={"POST"})
     *
     * @param Request          $request
     * @param StatisticService $statisticService
     *
     * @return JsonResponse
     */
    public function imageShowed(Request $request, StatisticService $statisticService): JsonResponse
    {
        $image = $request->get('image');

        if (!empty($image)) {
            $statisticService->imageShowed($image);

            return new JsonResponse([
                'message' => 'Ура'
            ]);
        }

        return new JsonResponse([
            'message' => 'Неверный запрос',
        ], 500);
    }
}
