<?php

namespace App\Service;

use App\Entity\ChatContent;
use App\Entity\Session;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class ChatContentHandler
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function buildForm(): FormInterface
    {
        return $this->container->get('form.factory')
            ->createBuilder(FormType::class)
            ->add('text', TextareaType::class, ['label' => 'Text content'])
            ->add('send', SubmitType::class, ['label' => 'Send text'])
            ->getForm();
    }

    public function handlerForm(FormInterface $form, Request $request, Session $session)
    {
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $message = $form->get('text')->getData();

            $chatContent = new ChatContent();
            $chatContent->setSession($session);
            $chatContent->setMessage($message);
            $chatContent->setIP($request->getClientIp());

            $entityManager = $this->container->get('doctrine')->getManager();
            $entityManager->persist($chatContent);
            $entityManager->flush();
        }
    }
}