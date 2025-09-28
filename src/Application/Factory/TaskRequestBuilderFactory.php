<?php
namespace App\Application\Factory;

use App\Application\Service\Task\CreateTaskRequestBuilder;
use App\Application\Service\Task\CreateFilterTaskRequestBuilder;
use App\Domain\Repository\TaskRequestBuilderInterface;

class TaskRequestBuilderFactory
{
    public const TYPE_CREATE = 'create';
    public const TYPE_FILTER = 'filter';

    public function getBuilder(string $type): TaskRequestBuilderInterface
    {
        return match ($type) {
            self::TYPE_CREATE => new CreateTaskRequestBuilder(),
            self::TYPE_FILTER => new CreateFilterTaskRequestBuilder(),
            default => throw new \InvalidArgumentException("Unknown builder type: $type"),
        };
    }
}
