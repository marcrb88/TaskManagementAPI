<?php

namespace App\Application\Service\Task;

use App\Application\UseCase\Task\UpdateTask\UpdateTaskRequest;
use App\Domain\Repository\TaskRequestBuilderInterface;
use App\Domain\ValueObject\Status;
use App\Domain\ValueObject\Priority;
use DateTime;


class UpdateTaskRequestBuilder implements TaskRequestBuilderInterface
{

    public function build(array $data): UpdateTaskRequest
    {
        $updateTaskRequest = new UpdateTaskRequest();

        if (!empty($data['id'])) {
            $updateTaskRequest->setId($data['id']);
        }

        if (!empty($data['title'])) {
            $updateTaskRequest->setTitle($data['title']);
        }

        if (!empty($data['description'])) {
            $updateTaskRequest->setDescription($data['description']);
        }

        if (!empty($data['status'])) {
            $updateTaskRequest->setStatus(Status::from($data['status']));
        }

        if (!empty($data['priority'])) {
            $updateTaskRequest->setPriority(Priority::from($data['priority']));
        }

        if (!empty($data['assignedTo'])) {
            $updateTaskRequest->setAssignedTo($data['assignedTo']);
        }

        if (!empty($data['createdAt'])) {
            $updateTaskRequest->setCreatedAt(new DateTime($data['createdAt']));
        }

        if (!empty($data['updatedAt'])) {
            $updateTaskRequest->setUpdatedAt(new DateTime($data['updatedAt']));
        }

        if (!empty($data['dueDate'])) {
            $updateTaskRequest->setDueDate($data['dueDate'] ? new DateTime($data['dueDate']) : null);
        }

        return $updateTaskRequest;
    }

}
