<?php

namespace App\Application\Service\Task;

use App\Application\UseCase\Task\ListTask\CreateFilterRequest;
use App\Domain\Repository\TaskRequestBuilderInterface;
use App\Domain\ValueObject\Status;
use App\Domain\ValueObject\Priority;
use DateTime;
use Ramsey\Uuid\Uuid;


class CreateFilterTaskRequestBuilder implements TaskRequestBuilderInterface
{

    public function build(array $data): CreateFilterRequest
    {
        $createTaskRequest = new CreateFilterRequest();

        if (!empty($data['id'])) {
            $uuid = $data['assignedTo'];
            if (!Uuid::isValid($uuid)) {
                throw new \InvalidArgumentException("Assigned user ID is not a valid UUID.");
            }
            $createTaskRequest->setId($data['id']);
        }

        if (!empty($data['status'])) {
            $status = $this->validateStatus($data['status']);
            if (empty($status)) {
                throw new \InvalidArgumentException("Invalid status value: " . $data['status']);
            }
            $createTaskRequest->setStatus($status);
        }

        if (!empty($data['priority'])) {
            $priority = $this->validatePriority($data['priority']);
            if (empty($priority)) {
                throw new \InvalidArgumentException("Invalid priority value: " . $data['priority']);
            }
            $createTaskRequest->setPriority($priority);
        }

        if (!empty($data['assignedTo'])) {
            $createTaskRequest->setAssignedTo($data['assignedTo']);
        }

        if (!empty($data['dueDate'])) {
            $createTaskRequest->setDueDate($data['dueDate'] ? new DateTime($data['dueDate']) : null);
        }

        if (!empty($data['createdAt'])) {
            $createTaskRequest->setCreatedAt(new DateTime($data['createdAt']));
        }

        return $createTaskRequest;
    }

    private function validateStatus(?string $status): ?Status
    {
        return $status ? Status::tryFrom($status) : null;
    }

    private function validatePriority(?string $priority): ?Priority
    {
        return $priority ? Priority::tryFrom($priority) : null;
    }

}
