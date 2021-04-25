<?php

namespace App\Controller;

use App\Entity\MediaContent;
use App\Service\ContentSessionService;
use App\Service\FileHelper;
use App\Service\MediaContentHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ThumbnailController extends AbstractController
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
     * @Route("/content/thumbnail/{id}", name="image-thumbnail")
     *
     * @param Request $request
     * @param string $id
     * @param MediaContentHandler $handler
     * @return Response
     */
    public function ImageThumbnail(Request $request, string $id, FileHelper $fileHelper) {
        $session = $this->contentSessionService->getSessionFromRequest($request);

        $mediaContent = $this->getDoctrine()->getRepository(MediaContent::class)
            ->find($id);

        if($mediaContent === null || $mediaContent->getSession()->getId() !== $session->getId()) {
            throw new NotFoundHttpException("File not found");
        }

        $thumbnailFile = $fileHelper->getThumbnail($mediaContent);

        return $this->file($thumbnailFile);

    }
}