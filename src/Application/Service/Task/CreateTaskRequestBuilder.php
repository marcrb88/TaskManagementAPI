<?php

namespace App\Application\Service\Task;

use App\Application\UseCase\Task\CreateTask\CreateUpdateTaskRequest;
use App\Domain\Repository\TaskRequestBuilderInterface;
use App\Domain\ValueObject\Status;
use App\Domain\ValueObject\Priority;
use DateTime;


class CreateUpdateTaskRequestBuilder implements TaskRequestBuilderInterface
{

    public function build(array $data): CreateUpdateTaskRequest
    {
        $createTaskRequest = new CreateUpdateTaskRequest();

        if (!empty($data['id'])) {
            $createTaskRequest->setId($data['id']);
        }

        if (!empty($data['status'])) {
            $createTaskRequest->setStatus(Status::from($data['status']));
        }

        if (!empty($data['priority'])) {
            $createTaskRequest->setPriority(Priority::from($data['priority']));
        }

        if (!empty($data['assignedTo'])) {
            $createTaskRequest->setAssignedTo($data['assignedTo']);
        }

        if (!empty($data['createdAt'])) {
            $createTaskRequest->setCreatedAt(new DateTime($data['createdAt']));
        }

        if (!empty($data['updatedAt'])) {
            $createTaskRequest->setUpdatedAt(new DateTime($data['updatedAt']));
        }

        if (!empty($data['dueDate'])) {
            $createTaskRequest->setDueDate($data['dueDate'] ? new DateTime($data['dueDate']) : null);
        }

        return $createTaskRequest;
    }

}
