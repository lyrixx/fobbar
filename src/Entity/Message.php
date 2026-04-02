<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
class Message
{
    #[ORM\Id]
    #[ORM\Column(type: 'guid')]
    public string $id;

    #[ORM\ManyToOne(inversedBy: 'messages')]
    public Topic $topic;

    #[ORM\ManyToOne()]
    #[ORM\JoinColumn(nullable: false)]
    public User $author;

    #[ORM\Column()]
    public \DateTimeImmutable $createdAt;

    #[ORM\Column()]
    public string $content;

    #[ORM\Column(type: 'text', nullable: true)]
    public ?string $contentCompiled = null;

    #[ORM\Column(length: 20)]
    public string $state = 'draft';

    public function __construct(User $author, Topic $topic)
    {
        $this->id = Uuid::v7()->toRfc4122();
        $this->topic = $topic;
        $this->author = $author;
        $this->createdAt = new \DateTimeImmutable();
    }
}
