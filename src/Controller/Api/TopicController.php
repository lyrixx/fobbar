<?php

namespace App\Controller\Api;

use App\Entity\Topic;
use App\Repository\TopicRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class TopicController extends AbstractController
{
    public function __construct(
        private TopicRepository $topicRepository,
        private SerializerInterface $serializer,
        private ValidatorInterface $validator,
        private EntityManagerInterface $em,
    ) {
    }

    #[Route('/api/topics', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $json = $this->serializer->serialize(
            $this->topicRepository->findAll(),
            'json',
            ['groups' => ['topic:read']]
        );

        return JsonResponse::fromJsonString($json);
    }

    #[Route('/api/topics', methods: ['POST'])]
    public function new(Request $request): JsonResponse
    {
        $topic = $this
            ->serializer
            ->deserialize($request->getContent(), Topic::class, 'json', [
                'groups' => ['topic:write'],
            ])
        ;

        $errors = $this->validator->validate($topic);

        if (\count($errors)) {
            $json = $this->serializer->serialize($errors, 'json');

            return new JsonResponse($json, 422, json: true);
        }

        $this->em->persist($topic);
        $this->em->flush();

        $json = $this->serializer->serialize($topic, 'json', ['groups' => ['topic:read']]);

        return new JsonResponse($json, 201, json: true);
    }

    #[Route('/api/topics/{id}', methods: ['PUT'])]
    public function edit(Request $request, #[MapEntity()] Topic $topic): JsonResponse
    {
        try {
            $this
                ->serializer
                ->deserialize($request->getContent(), $topic::class, 'json', [
                    'groups' => ['topic:write'],
                    'object_to_populate' => $topic,
                    'collect_denormalization_errors' => true,
                ])
            ;
        } catch (\Exception $e) {
            $json = $this->serializer->serialize($e, 'json');

            return new JsonResponse($json, 422, json: true);
        }

        $errors = $this->validator->validate($topic);

        if (\count($errors)) {
            $json = $this->serializer->serialize($errors, 'json');

            return new JsonResponse($json, 422, json: true);
        }

        $this->em->flush();

        $json = $this->serializer->serialize($topic, 'json', ['groups' => ['topic:read']]);

        return new JsonResponse($json, 200, json: true);
    }
}
