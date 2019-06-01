<?php

namespace App\Controller;

use App\Entity\Link;
use App\Services\LinkShorterService;
use App\Services\StatisticService;
use App\Utils\LinkNameExistException;
use App\Utils\LinkNameInvalidException;
use App\Utils\LinkNameLongException;
use Doctrine\DBAL\DBALException;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    public const FINGERPRINT = 'fingerprint';

    /**
     * @Route("/add-fingerprint", name="add-fingerprint")
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function addFingerprint(Request $request): JsonResponse
    {
        $this->get('session')->set(self::FINGERPRINT, $request->get(self::FINGERPRINT));

        return new JsonResponse([
            'success' => true
        ]);
    }

    /**
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        return $this->render('index/index.html.twig', ['domain_link' => $this->getParameter('domain_link')]);
    }

    /**
     * @Route("/short", name="short-link")
     *
     * @param Request            $request
     * @param LinkShorterService $linkSorter
     *
     * @param LoggerInterface    $logger
     *
     * @return JsonResponse
     */
    public function short(Request $request, LinkShorterService $linkSorter, LoggerInterface $logger): JsonResponse
    {
        try {
            $customName = $request->get('name');
            $url = $request->get('url');
            $fingerprint = $request->get('fingerprint');
            $expire_date = $request->get('expire_date');
            $is_commercial = (bool)$request->get('commercial');

            if (false === filter_var($url, FILTER_VALIDATE_URL)) {
                return new JsonResponse([
                    'message' => 'Это не ссылка'
                ], 500);
            }

            if (!empty($customName)) {
                try {
                    $link = $linkSorter->generateByCustomName($url, $customName, $fingerprint, $expire_date, $is_commercial);
                } catch (LinkNameExistException $e) {
                    return new JsonResponse([
                        'message' => 'Не удалось создать ссылку - сокращение уже занято',
                    ], 500);
                } catch (LinkNameLongException $e) {
                    return new JsonResponse([
                        'message' => 'Не удалось создать ссылку - слишком длинное (или короткое) сокращение',
                    ], 500);
                } catch (LinkNameInvalidException $e) {
                    return new JsonResponse([
                        'message' => 'Не удалось создать ссылку - сокращение содержит недопустимые символы',
                    ], 500);
                }
            } else {
                $link = $linkSorter->generate($url, $fingerprint, $expire_date, $is_commercial);
            }

            if ($link) {
                return new JsonResponse([
                    'link' => $link
                ]);
            }
        } catch (Exception $e) {
            $logger->critical('Пользователь не смог создать ссылку', [$e, $request]);
        }

        return new JsonResponse([
            'message' => 'Не удалось создать ссылку',
        ], 500);
    }

    /**
     * @Route("/{slug}", name="link")
     *
     * @param string             $slug
     * @param Request            $request
     * @param LinkShorterService $shorter
     * @param StatisticService   $statisticService
     *
     * @return RedirectResponse|Response
     */
    public function link($slug, Request $request, LinkShorterService $shorter, StatisticService $statisticService)
    {
        $link = $this->getDoctrine()->getRepository(Link::class)->findOneBy(['short' => $slug]);
        if ($link && $link->hasActive()) {

            $fingerprint = $this->get('session')->get(self::FINGERPRINT);
            if (null === $fingerprint) {
                return $this->render('index/fingerprint.html.twig');
            }

            $statisticService->followingLink($link, $fingerprint);

            if ($link->getIsCommercial()) {
                $image = $shorter->getCommercialImage($this->getParameter('commercial_dir'));

                return $this->render('index/commercial.html.twig', compact('link', 'image'));
            }

            return $this->redirect($link->getSource(), 301);
        }

        return $this->render('index/not-found.html.twig', [], new Response('', 404));
    }
}
