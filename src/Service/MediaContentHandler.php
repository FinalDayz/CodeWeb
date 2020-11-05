<?php

namespace App\Service;

use App\Entity\MediaContent;
use App\Entity\Session;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\File;

class MediaContentHandler
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var FileUploader
     */
    private $uploader;

    public function __construct(ContainerInterface $container, FileUploader $uploader)
    {
        $this->container = $container;
        $this->uploader = $uploader;
    }

    public function buildForm(): FormInterface
    {
        return $this->container->get('form.factory')
            ->createBuilder(FormType::class)
            ->add('file', FileType::class, [
                'constraints' => [
                    new File([
                        'maxSize' => '500M',
                    ])
                ],
            ])
            ->add('add', SubmitType::class, ['label' => 'Add file'])
            ->getForm();
    }

    public function handleUpload(FormInterface $form, Request $request, Session $session)
    {
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $contentFile */
            $contentFile = $form->get('file')->getData();
            $mediaContent = new MediaContent();
            $mediaContent->setSize($contentFile->getSize());
            $mediaContent->setName($contentFile->getClientOriginalName());
            $mediaContent->setFileType($contentFile->getMimeType());
            //TODO: change this to a config file, but I am too lazy to do this now
            $fileName = $this->uploader->upload($contentFile, "/data/mediaContent/");

            $mediaContent->setLocation(
                $this->container->get('kernel')->getProjectDir() . "/data/mediaContent/" . $fileName
            );
            $mediaContent->setIP($request->getClientIp());
            $mediaContent->setSession($session);

            /** @var ObjectManager $entityManager */
            $entityManager = $this->container->get('doctrine')->getManager();
            $entityManager->persist($mediaContent);
            $entityManager->flush();

        }
    }
}