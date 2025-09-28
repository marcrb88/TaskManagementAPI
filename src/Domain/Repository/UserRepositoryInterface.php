<?php
namespace App\Domain\Repository;

use App\Domain\Model\User;

interface UserRepositoryInterface
{
    public function save(User $user): void;
    public function findById(string $id): ?User;
    public function findAll(): array;
    public function delete(User $user): void;
    public function findByEmail(string $email): ?User;
}
