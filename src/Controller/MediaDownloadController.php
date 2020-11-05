<?php

namespace App\Controller;

use App\Entity\MediaContent;
use App\Service\ContentSessionService;
use App\Service\MediaContentHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class MediaDownloadController extends AbstractController
{

    /**
     * @var ContentSessionService
     */
    private $contentSessionService;

    public function __construct(ContentSessionService $contentSessionService)
    {
        $this->contentSessionService = $contentSessionService;
    }

    /**
     * @Route("/content/download/{id}", name="download-content")
     *
     * @param Request $request
     * @param string $id
     * @param MediaContentHandler $handler
     * @return Response
     */
    public function downloadMedia(Request $request, string $id, MediaContentHandler $handler): Response
    {
        $session = $this->contentSessionService->getSessionFromRequest($request);

        $mediaContent = $this->getDoctrine()->getRepository(MediaContent::class)
            ->find($id);

        if($mediaContent === null || $mediaContent->getSession()->getId() !== $session->getId()) {
            throw new NotFoundHttpException("File not found");
        }

        $mediaFile = new File($mediaContent->getLocation());
        return $this->file($mediaFile, $mediaContent->getName());
    }
}