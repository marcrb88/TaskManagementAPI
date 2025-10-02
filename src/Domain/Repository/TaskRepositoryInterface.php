<?php
namespace App\Domain\Repository;

use App\Domain\Model\Task;

interface TaskRepositoryInterface
{
    public function save(Task $task): void;
    public function findById(string $id): ?Task;
    public function findAll(): array;
    public function delete(Task $task): void;
    public function findByFilters(array $filters): array;
    public function findOneBy(array $criteria): ?Task;
}