<?php

namespace App\Controller;

use App\Service\ChatContentHandler;
use App\Service\ContentSessionService;
use App\Service\MediaContentHandler;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SessionController extends AbstractController
{

    /**
     * @var MediaContentHandler
     */
    private $mediaContentHandler;
    /**
     * @var FormInterface
     */
    private $mediaForm;
    /**
     * @var ContentSessionService
     */
    private $contentSessionService;
    /**
     * @var \App\Entity\Session
     */
    private $session;
    /**
     * @var FormInterface
     */
    private $chatForm;
    /**
     * @var ChatContentHandler
     */
    private $chatContentHandler;
    /**
     * @var array
     */
    private $data;

    public function __construct(
        MediaContentHandler $mediaContentHandler,
        ContentSessionService $contentSessionService,
        ChatContentHandler $chatContentHandler
    )
    {
        $this->mediaContentHandler = $mediaContentHandler;
        $this->contentSessionService = $contentSessionService;
        $this->chatContentHandler = $chatContentHandler;
    }

    /**
     * @Route("/", name="index")
     *
     * @return RedirectResponse
     */
    public
    function index(): RedirectResponse
    {
        return $this->redirectToRoute('content-sharer');
    }

    public
    function init(Request $request)
    {
        $this->mediaForm = $this->mediaContentHandler->buildForm();
        $this->chatForm = $this->chatContentHandler->buildForm();
        $this->session = $this->contentSessionService->getSessionFromRequest($request);

        $this->data = [
            'mediaForm' => $this->mediaForm->createView(),
            'chatForm' => $this->chatForm->createView(),
            'hadSession' => !$request->cookies->has('new-session'),
            'session' => $this->session,
            'clientIp' => $request->getClientIp(),
            'latestContent' => $this->contentSessionService->getLatestContentFromSession($this->session)
        ];
    }

    /**
     * @Route("/content/sharer/new", name="content-sharer-new-session")
     *
     * @param Request $request
     * @param ContentSessionService $service
     * @return Response
     * @throws Exception
     */
    public
    function newSession(
        Request $request,
        ContentSessionService $service
    ): Response
    {
        $response = $this->redirectToRoute('content-sharer');
        $response->headers->clearCookie('content-session');
        return $response;
    }


    /**
     * @Route("/content/sharer/join", name="content-sharer-join-session")
     *
     * @param Request $request
     * @param ContentSessionService $service
     * @return Response
     * @throws Exception
     */
    public
    function joinSession(
        Request $request,
        ContentSessionService $service
    ): Response
    {
        $this->init($request);

        $sessionContent = $service->getSessionContent($this->session);

        $sessionId = strtolower($request->get('session-key'));
        if (!$service->verifySessionKey($sessionId)) {
            $this->addFlash("error", "invalid session key");
            return $this->render('content-share.html.twig',
                array_merge([
                    'sessionContent' => $sessionContent,
                ], $this->data)
            );
        } else {
            $response = $this->redirectToRoute('content-sharer');
            $response->headers->setCookie(
                Cookie::create('content-session', $sessionId, strtotime('now + 1 month'))
            );
            return $response;
        }
    }

    /**
     * @Route("/content/sharer/", name="content-sharer")
     *
     * @param Request $request
     * @param ContentSessionService $service
     * @param MediaContentHandler $mediaContentHandler
     * @param ChatContentHandler $chatContentHandler
     * @return Response
     */
    public function contentSharer(
        Request $request,
        ContentSessionService $service,
        MediaContentHandler $mediaContentHandler,
        ChatContentHandler $chatContentHandler
    ): Response
    {
        $this->init($request);

        $mediaContentHandler->handleUpload($this->mediaForm, $request, $this->session);
        $this->chatForm = $chatContentHandler->handlerForm($this->chatForm, $request, $this->session);

        $sessionContent = $service->getSessionContent($this->session);

        return $this->render('content-share.html.twig',
            array_merge([
                'sessionContent' => $sessionContent,
            ], $this->data)
        );
    }

    /**
     * @Route("/xhr/latest/{latestId}", name="latest-content-check")
     *
     * @param Request $request
     * @param string $latestId
     * @return JsonResponse
     */
    public function hasLatestContent(
        Request $request,
        string $latestId,
        ContentSessionService $service
    ): Response
    {
        $this->init($request);

        $latestContent = $service->getLatestContentFromSession($this->session);
        $hasLatest = $latestContent->getId() === intval($latestId);

        return new JsonResponse($hasLatest);
    }
}