<?php
namespace App\Application\Factory;

use App\Application\Service\Task\CreateUpdateTaskRequestBuilder;
use App\Application\Service\Task\CreateFilterTaskRequestBuilder;
use App\Domain\Repository\TaskRequestBuilderInterface;

class TaskRequestBuilderFactory
{
    public const TYPE_CREATE = 'create';
    public const TYPE_FILTER = 'filter';
    public const TYPE_UPDATE = 'update';

    public function getBuilder(string $type): TaskRequestBuilderInterface
    {
        return match ($type) {
            self::TYPE_UPDATE,
            self::TYPE_CREATE => new CreateUpdateTaskRequestBuilder(),
            self::TYPE_FILTER => new CreateFilterTaskRequestBuilder(),
            default => throw new \InvalidArgumentException("Unknown builder type: $type"),
        };
    }
}
