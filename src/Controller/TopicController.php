<?php

namespace App\Controller;

use App\Entity\Topic;
use App\Form\Type\TopicType;
use App\Repository\TopicRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class TopicController extends AbstractController
{
    public function __construct(
        private readonly TopicRepository $topicRepository,
        private readonly EntityManagerInterface $em,
    ) {
    }

    #[Route('/', name: 'topic_index')]
    public function index(): Response
    {
        $topics = $this->topicRepository->findAllForHomepage();

        return $this->render('topic/index.html.twig', [
            'topics' => $topics,
        ]);
    }

    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    #[Route('/new', name: 'topic_new')]
    public function new(Request $request): Response
    {
        $topic = new Topic();
        $form = $this->createForm(TopicType::class, $topic);
        $form->add('save', SubmitType::class);

        if ($form->handleRequest($request)->isSubmitted() && $form->isValid()) {
            $this->em->persist($topic);
            $this->em->flush();

            $this->addFlash('success', 'The topic has been saved.');

            return $this->redirectToRoute('topic_show', ['id' => $topic->id]);
        }

        return $this->render('topic/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'topic_show')]
    public function show(
        #[MapEntity(expr: 'repository.findOneForShow(id)')]
        Topic $topic,
    ): Response {
        return $this->render('topic/show.html.twig', [
            'topic' => $topic,
        ]);
    }
}
