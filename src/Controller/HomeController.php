<?php

namespace App\Controller;

use App\Service\ContentSessionService;
use Doctrine\ORM\EntityManager;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{

    /**
     * @Route("/", name="index")
     *
     * @return RedirectResponse
     */
    public function index(): RedirectResponse
    {
        return $this->redirectToRoute('content-sharer');
    }

    /**
     * @Route("/content/sharer/{action}", name="content-sharer")
     *
     * @param SessionInterface $localSession
     * @param ContentSessionService $service
     * @param string $action
     * @return Response
     * @throws Exception
     */
    public function contentSharer(
        SessionInterface $localSession,
        ContentSessionService $service,
        string $action = ''
    ): Response {

        if ($action === 'new') {
            $localSession->remove('content-session');
            return $this->redirectToRoute('content-sharer');
        }

        if($action === 'join') {

        }

        $newSession = !$localSession->get('content-session');

        if ($newSession)
            $localSession->set("content-session", $service->generateId());

        $session = $service->getSession($localSession->get('content-session'), !$newSession);


        return $this->render('content-share.html.twig',
            [
                'hadSession' => !$newSession,
                'session' => $session,
            ]
        );
    }
}