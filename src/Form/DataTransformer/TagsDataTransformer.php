<?php

namespace App\Form\DataTransformer;

use App\Entity\Tag;
use App\Repository\TagRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * @implements DataTransformerInterface<list<Tag>, string>
 */
final readonly class TagsDataTransformer implements DataTransformerInterface
{
    public function __construct(
        private TagRepository $tagRepository,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function transform(mixed $tagsAsArray): mixed
    {
        $titles = [];

        foreach ($tagsAsArray as $tag) {
            $titles[] = $tag->title;
        }

        return implode(', ', $titles);
    }

    public function reverseTransform(mixed $value): mixed
    {
        if (!$value) {
            return [];
        }

        $titles = array_map(trim(...), explode(',', $value));

        $tags = [];
        foreach ($titles as $title) {
            $tag = $this->tagRepository->findOneBy(['title' => $title]);

            if (!$tag) {
                $tag = new Tag($title);

                $this->entityManager->persist($tag);
            }

            $tags[] = $tag;
        }

        return $tags;
    }
}
