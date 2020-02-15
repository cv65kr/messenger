<?php

declare(strict_types=1);

namespace Messenger\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Messenger\Aggregate\AggregateRootId;
use Messenger\Entity\EventStream;

class EventStreamRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EventStream::class);
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save(EventStream $eventStream): void
    {
        $this->getEntityManager()->persist($eventStream);
        $this->getEntityManager()->flush();
    }

    public function findByAggregateRootId(AggregateRootId $aggregateRootId): array
    {
        return $this->createQueryBuilder('e')
            ->where('e.aggregateId = :id')
            ->setParameter('id', $aggregateRootId->toUUID()->getBytes())
            ->orderBy('e.version', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
