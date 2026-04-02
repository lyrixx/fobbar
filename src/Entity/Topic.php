<?php

namespace App\Entity;

use App\Repository\TopicRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TopicRepository::class)]
class Topic
{
    #[Groups(['topic:read'])]
    #[ORM\Id]
    #[ORM\Column(type: 'guid')]
    public string $id;

    #[Assert\NotBlank()]
    #[Groups(['topic:read', 'topic:write'])]
    #[ORM\Column()]
    public string $title;

    /** @var Collection<int, Message> */
    #[ORM\OneToMany(targetEntity: Message::class, mappedBy: 'topic')]
    public Collection $messages;

    /** @var Collection<int, Tag> */
    #[Groups(['topic:read'])]
    #[ORM\ManyToMany(targetEntity: Tag::class, inversedBy: 'topics', cascade: ['persist'])]
    private Collection $tags;

    public function __construct()
    {
        $this->id = uuid_create();
        $this->messages = new ArrayCollection();
        $this->tags = new ArrayCollection();
    }

    /**
     * @return Collection<int, Tag>
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): void
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
        }
    }

    public function removeTag(Tag $tag): void
    {
        if ($this->tags->contains($tag)) {
            $this->tags->removeElement($tag);
        }
    }
}
