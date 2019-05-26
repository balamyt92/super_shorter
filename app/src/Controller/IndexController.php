<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        return $this->render('index/index.html.twig');
    }

    /**
     * @Route("/{slug}", name="link")
     * @param         $slug
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function link($slug, Request $request)
    {
        $link = $this->getDoctrine()->getRepository(Link::class)->findOneBy(['slug' => $slug]);
        if ($link) {
            return $this->redirect($link, 301);
        }

        return $this->render('index/not-found.html.twig');
    }
}
