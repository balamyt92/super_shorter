<?php

namespace App\Controller;

use App\Entity\Link;
use App\Services\LinkShorterService;
use App\Utils\LinkNameExistException;
use App\Utils\LinkNameInvalidException;
use App\Utils\LinkNameLongException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        return $this->render('index/index.html.twig');
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

            if (false === filter_var($url, FILTER_VALIDATE_URL)) {
                return new JsonResponse([
                    'message' => 'Это не ссылка'
                ], 500);
            }

            if (!empty($customName)) {
                try {
                    $link = $linkSorter->generateByCustomName($url, $customName, $fingerprint, $expire_date);
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
                $link = $linkSorter->generate($url, $fingerprint, $expire_date);
            }

            if ($link) {
                return new JsonResponse([
                    'link' => $link
                ]);
            }
        } catch (\Exception $e) {
            $logger->critical('Пользователь не смог создать ссылку', [$e, $request]);
        }

        return new JsonResponse([
            'message' => 'Не удалось создать ссылку',
        ], 500);
    }

    /**
     * @Route("/{slug}", name="link")
     *
     * @param         $slug
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function link($slug, Request $request)
    {
        $link = $this->getDoctrine()->getRepository(Link::class)->findOneBy(['short' => $slug]);
        if ($link) {
            return $this->redirect($link->getSource(), 301);
        }

        return $this->render('index/not-found.html.twig', [], new Response('', 404));
    }
}
