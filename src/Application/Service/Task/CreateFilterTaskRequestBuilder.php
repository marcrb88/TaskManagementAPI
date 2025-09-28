<?php

namespace App\Application\Service\Task;

use App\Application\UseCase\Task\ListTask\CreateFilterRequest;
use App\Domain\Repository\TaskRequestBuilderInterface;
use DateTime;
use App\Domain\ValueObject\Priority;
use App\Domain\ValueObject\Status;


class CreateFilterTaskRequestBuilder implements TaskRequestBuilderInterface
{

    public function build(array $data): CreateFilterRequest
    {
        $createTaskRequest = new CreateFilterRequest();

        if (!empty($data['status'])) {
            $createTaskRequest->setStatus(Status::from($data['status']));
        }

        if (!empty($data['priority'])) {
            $createTaskRequest->setPriority(Priority::from($data['priority']));
        }

        if (!empty($data['assignedTo'])) {
            $createTaskRequest->setAssignedTo($data['assignedTo']);
        }

        if (!empty($data['dueDate'])) {
            $start = new DateTime($data['createdAt'] . ' 00:00:00');
            $end   = new DateTime($data['createdAt'] . ' 23:59:59');
            $createTaskRequest->setDueDateFrom($start);
            $createTaskRequest->setDueDateTo($end);
        }

       if (!empty($data['createdAt'])) {
            $start = new DateTime($data['createdAt'] . ' 00:00:00');
            $end   = new DateTime($data['createdAt'] . ' 23:59:59');
            $createTaskRequest->setCreatedAtFrom($start);
            $createTaskRequest->setCreatedAtTo($end);
        }

        return $createTaskRequest;
    }
}
