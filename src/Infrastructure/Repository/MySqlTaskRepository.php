<?php

namespace App\Infrastructure\Repository;

use App\Domain\Model\Task;
use App\Domain\Repository\TaskRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class MySqlTaskRepository implements TaskRepositoryInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function save(Task $task): void
    {
        $this->entityManager->persist($task);
        $this->entityManager->flush();
    }

    public function findById(string $id): ?Task
    {
        return $this->entityManager->find(Task::class, $id);
    }

    public function findAll(): array
    {
        return $this->entityManager->getRepository(Task::class)->findAll();
    }

    public function delete(Task $task): void
    {
        $this->entityManager->remove($task);
        $this->entityManager->flush();
    }

    public function findByFilters(array $filters): array
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('t')
        ->from(Task::class, 't');

        if (!empty($filters['status'])) {
            $qb->andWhere('t.status = :status')
            ->setParameter('status', $filters['status']);
        }

        if (!empty($filters['priority'])) {
            $qb->andWhere('t.priority = :priority')
            ->setParameter('priority', $filters['priority']);
        }

        if (!empty($filters['assignedTo'])) {
            $qb->andWhere('t.assignedTo = :assignedTo')
            ->setParameter('assignedTo', $filters['assignedTo']);
        }

        if (!empty($filters['createdAtFrom'])) {
            $qb->andWhere('t.createdAt >= :createdAtFrom')
            ->setParameter('createdAtFrom', $filters['createdAtFrom']);
        }

        if (!empty($filters['createdAtTo'])) {
            $qb->andWhere('t.createdAt <= :createdAtTo')
            ->setParameter('createdAtTo', $filters['createdAtTo']);
        }

        if (!empty($filters['dueDateFrom'])) {
            $qb->andWhere('t.dueDate >= :dueDateFrom')
            ->setParameter('dueDateFrom', $filters['dueDateFrom']);
        }

        if (!empty($filters['dueDateTo'])) {
            $qb->andWhere('t.dueDate <= :dueDateTo')
            ->setParameter('dueDateTo', $filters['dueDateTo']);
        }

        return $qb->getQuery()->getResult();
    }

    public function findOneBy(array $criteria): ?Task
    {
        return $this->entityManager
            ->getRepository(Task::class)
            ->findOneBy($criteria);
    }
        
}
