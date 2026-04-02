<?php

namespace App\Serializer\Normalizer;

use App\Entity\Topic;
use App\Repository\MessageRepository;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class TopicNormalizer implements NormalizerInterface
{
    public function __construct(
        #[Autowire(service: 'serializer.normalizer.object')]
        private NormalizerInterface $normalizer,
        private MessageRepository $messageRepository
    ) {
    }

    public function normalize($object, ?string $format = null, array $context = []): array
    {
        $data = $this->normalizer->normalize($object, $format, $context);

        $data['messageCount'] = $this->messageRepository->count(['topic' => $object->id]);

        return $data;
    }

    public function supportsNormalization($data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof Topic;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [Topic::class => true];
    }
}
