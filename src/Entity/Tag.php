<?php

namespace App\Entity;

use App\Repository\TagRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: TagRepository::class)]
class Tag
{
    #[ORM\Id]
    #[ORM\Column(type: 'guid')]
    public string $id;

    #[Groups(['topic:read'])]
    #[ORM\Column()]
    public string $title;

    /** @var Collection<int, Topic> */
    #[ORM\ManyToMany(targetEntity: Topic::class, mappedBy: 'tags')]
    public Collection $topics;

    public function __construct(string $title)
    {
        $this->id = uuid_create();
        $this->title = $title;
        $this->topics = new ArrayCollection();
    }
}
