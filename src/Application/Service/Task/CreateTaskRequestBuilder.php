<?php

namespace App\Application\Service\Task;

use App\Application\UseCase\Task\CreateTask\CreateTaskRequest;
use App\Domain\ValueObject\Status;
use App\Domain\ValueObject\Priority;
use App\Infrastructure\Repository\MySqlUserRepository;
use DateTime;


class CreateTaskRequestBuilder
{
    private MySqlUserRepository $userRepository;

    public function __construct
    (
        MySqlUserRepository $userRepository
    )
    {
        $this->userRepository = $userRepository;
    }

    public function build(array $data): CreateTaskRequest
    {
        $createTaskRequest = new CreateTaskRequest(
            $data['title'],
            $data['description']
        );

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
