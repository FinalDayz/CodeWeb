<?php

namespace App\Controller;

use App\Entity\MediaContent;
use App\Entity\SessionContent;
use App\Service\ChatContentHandler;
use App\Service\ContentSessionService;
use App\Service\MediaContentHandler;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Cookie;
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
                [
                    'mediaForm' => $this->mediaForm->createView(),
                    'hadSession' => !$request->cookies->has('new-session'),
                    'session' => $this->session,
                    'sessionContent' => $sessionContent,
                ]
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
    public
    function contentSharer(
        Request $request,
        ContentSessionService $service,
        MediaContentHandler $mediaContentHandler,
        ChatContentHandler $chatContentHandler
    ): Response
    {
        $this->init($request);

        $mediaContentHandler->handleUpload($this->mediaForm, $request, $this->session);
        $chatContentHandler->handlerForm($this->chatForm, $request, $this->session);

        $sessionContent = $service->getSessionContent($this->session);

        return $this->render('content-share.html.twig',
            [
                'mediaForm' => $this->mediaForm->createView(),
                'chatForm' => $this->chatForm->createView(),
                'hadSession' => !$request->cookies->has('new-session'),
                'session' => $this->session,
                'sessionContent' => $sessionContent,
                'clientIp' => $request->getClientIp()
            ]
        );
    }
}