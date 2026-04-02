<?php

namespace App\DataFixtures;

use App\Entity\Message;
use App\Entity\Tag;
use App\Entity\Topic;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    private ObjectManager $manager;

    public function load(ObjectManager $manager): void
    {
        $this->manager = $manager;

        $webDevTag = $this->createTag('web-dev');

        $alice = $this->createUser('alice');
        $bob = $this->createUser('bob');

        $topic = $this->createTopic('PHP');
        $topic->addTag($webDevTag);
        for ($i = 0; $i < 10; ++$i) {
            $author = $i % 2 === 0 ? $alice : $bob;
            $this->createMessage($author, $topic, 'Message ' . $i);
        }

        $this->createMessage($alice, $topic, 'SHOULD NOT BE DISPLAYED', false);

        $topic = $this->createTopic('CSS');
        $topic->addTag($webDevTag);
        $topic = $this->createTopic('JavaScript');
        $topic->addTag($webDevTag);

        $manager->flush();
    }

    private function createTag(string $title): Tag
    {
        $tag = new Tag($title);

        $this->manager->persist($tag);

        return $tag;
    }

    private function createTopic(string $title): Topic
    {
        $topic = new Topic();
        $topic->title = $title;

        $this->manager->persist($topic);

        return $topic;
    }

    private function createMessage(User $alice, Topic $topic, ?string $content = null, bool $published = true): Message
    {
        $message = new Message($alice, $topic);
        $message->content = $content;
        $message->state = $published ? 'published' : 'unpublished';

        $this->manager->persist($message);

        return $message;
    }

    private function createUser(string $username): User
    {
        // the password is: `password`
        $user = new User($username, '$2y$13$1r1hpz/dH57kU4reKtt4uOwn.R1iEaMrKgDN3iBMBBv2HbN1vf8Ke');

        $this->manager->persist($user);

        return $user;
    }
}
