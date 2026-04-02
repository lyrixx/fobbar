<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\Topic;
use App\Form\Type\MessageType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class MessageController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly EntityManagerInterface $em,
    ) {
    }

    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    #[Route('/topic/{id}/message/new', name: 'message_new')]
    public function new(Request $request, #[MapEntity()] Topic $topic): Response
    {
        $author = $this->userRepository->getRandomUser();
        $message = new Message($author, $topic);
        $form = $this->createForm(MessageType::class, $message);
        $form->add('save', SubmitType::class);

        if ($form->handleRequest($request)->isSubmitted() && $form->isValid()) {
            $this->em->persist($message);
            $this->em->flush();

            $this->addFlash('success', 'The message has been saved.');

            return $this->redirectToRoute('topic_show', ['id' => $topic->id]);
        }

        return $this->render('message/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    #[Route('/messages/{id}', name: 'message_edit')]
    public function edit(Request $request, #[MapEntity()] Message $message): Response
    {
        $form = $this->createForm(MessageType::class, $message);
        $form->add('save', SubmitType::class);

        if ($form->handleRequest($request)->isSubmitted() && $form->isValid()) {
            $this->em->flush();

            $this->addFlash('success', 'The message has been updated.');

            return $this->redirectToRoute('topic_show', ['id' => $message->topic->id]);
        }

        return $this->render('message/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
