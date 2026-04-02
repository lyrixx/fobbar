<?php

namespace App\Repository;

use App\Entity\Topic;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\Model\HomepageTopic;

/**
 * @template-extends ServiceEntityRepository<Topic>
 */
class TopicRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Topic::class);
    }

    /**
     * @return array<HomepageTopic>
     */
    public function findAllForHomepage(): array
    {
        return $this
            ->createQueryBuilder('t')
            ->select('new App\Repository\Model\HomepageTopic(t, count(m))')
            ->leftJoin('t.messages', 'm')
            ->groupBy('t')
            ->orderBy('t.title', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findOneForShow(string $id): ?Topic
    {
        return $this
            ->createQueryBuilder('t')
            ->where('t.id = :id')->setParameter('id', $id)
            ->leftJoin('t.messages', 'm')
            ->leftJoin('t.tags', 'tags')
            ->select('t', 'm', 'tags')
            ->addOrderBy('m.id', 'ASC')
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
