<?php

namespace App\Controller;

use App\Entity\SessionContent;
use App\Service\ContentSessionService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SessionController extends AbstractController
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
     * @param Request $request
     * @param ContentSessionService $service
     * @param string $action
     * @return Response
     * @throws Exception
     */
    public function contentSharer(
        Request $request,
        ContentSessionService $service,
        string $action = ''
    ): Response
    {
        $cookies = $request->cookies;
        $newCookies = [];
        $uid = $cookies->get('uid');

        if (!$uid) {
            $uid = bin2hex(random_bytes(8));
            array_push(
                $newCookies,
                Cookie::create('uid', $uid, strtotime('now + 10 year'))
            );
        }

        if ($action === 'new') {
            $cookies->remove('content-session');
            return $this->redirectToRoute('content-sharer');
        }

        if ($action === 'join') {

        }

        $sessionId = $cookies->get('content-session');
        $newSession = !$sessionId;

        if ($newSession) {
            $sessionId = $service->generateId();
            array_push($newCookies,
                Cookie::create('content-session', $sessionId, strtotime('now + 1 month'))
            );
        }



        $session = $service->getSession($sessionId, !$newSession);

        $sessionContent = $service->getSessionContent($session);
        $response = $this->render('content-share.html.twig',
            [
                'hadSession' => !$newSession,
                'session' => $session,
                'sessionContent' => $sessionContent,
                'uid' => $uid,
            ]
        );

        foreach ($newCookies as $cookie) {
            $response->headers->setCookie(
                $cookie
            );
        }

        return $response;
    }
}