<?php

namespace App\EventSubscriber;

use App\Controller\SessionNotRequiredController;
use App\Service\ContentSessionService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class SessionInterceptor implements EventSubscriberInterface
{

    /**
     * @var ContentSessionService
     */
    private $contentSessionService;

    public function __construct(ContentSessionService $contentSessionService)
    {
        $this->contentSessionService = $contentSessionService;
    }

    public function onKernelController(ControllerEvent $event)
    {
        $controller = $event->getController();

        // when a controller class defines multiple action methods, the controller
        // is returned as [$controllerInstance, 'methodName']
        if (is_array($controller)) {
            $controller = $controller[0];
        }

        if (!($controller instanceof SessionNotRequiredController)) {
            $request = $event->getRequest();

            $sessionId = $request->cookies->get('content-session');

            if (!$sessionId) {
                $sessionId = $this->contentSessionService->generateId();

                $request->cookies->set(
                    'content-session',
                    $sessionId
                );

                $request->cookies->set('new-session', null);
            }
        }
    }

    public function onKernelResponse(ResponseEvent $event) {
        $cookies = $event->getRequest()->cookies;
        if($cookies->has('new-session')) {
            $event->getResponse()->headers->setCookie(
                Cookie::create('content-session', $cookies->get('content-session'), strtotime('now + 1 month'))
            );
        }
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
            KernelEvents::RESPONSE => 'onKernelResponse'
        ];
    }
}