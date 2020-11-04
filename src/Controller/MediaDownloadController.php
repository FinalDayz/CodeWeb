<?php

namespace App\Controller;

use App\Entity\MediaContent;
use App\Service\ContentSessionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MediaDownloadController extends AbstractController
{
    /**
     * @Route("/content/download/{id}", name="download-content")
     *
     * @param Request $request
     * @param $id
     * @return Response
     * @throws Exception
     */
    public function contentSharer(Request $request, string $id): Response
    {

        return new Reponse('hi');
    }
}