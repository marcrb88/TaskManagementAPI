<?php
namespace App\Domain\Repository;

interface TaskRequestBuilderInterface
{
    public function build(array $data): object;
}