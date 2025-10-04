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
        $createFilterRequest = new CreateFilterRequest();

        if (!empty($data['title'])) {
            $createFilterRequest->setTitle($data['title']);
        }

        if (!empty($data['description'])) {
            $createFilterRequest->setDescription($data['description']);
        }

        if (!empty($data['status'])) {
            $createFilterRequest->setStatus(Status::from($data['status']));
        }

        if (!empty($data['priority'])) {
            $createFilterRequest->setPriority(Priority::from($data['priority']));
        }

        if (!empty($data['dueDate'])) {
            $start = new DateTime($data['dueDate'] . ' 00:00:00');
            $end   = new DateTime($data['dueDate'] . ' 23:59:59');
            $createFilterRequest->setDueDateFrom($start);
            $createFilterRequest->setDueDateTo($end);
        }

       if (!empty($data['createdAt'])) {
            $start = new DateTime($data['createdAt'] . ' 00:00:00');
            $end   = new DateTime($data['createdAt'] . ' 23:59:59');
            $createFilterRequest->setCreatedAtFrom($start);
            $createFilterRequest->setCreatedAtTo($end);
        }

        if (!empty($data['updatedAt'])) {
            $start = new DateTime($data['updatedAt'] . ' 00:00:00');
            $end   = new DateTime($data['updatedAt'] . ' 23:59:59');
            $createFilterRequest->setUpdatedAtFrom($start);
            $createFilterRequest->setUpdatedAtTo($end);
        }

        return $createFilterRequest;
    }
}
