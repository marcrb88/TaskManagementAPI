<?php

namespace App\Application\Service\Task;

use App\Application\Service\Response\DataValidatorResponse;
use DateTime;
use App\Domain\ValueObject\Status;
use Ramsey\Uuid\Uuid;
use App\Domain\Repository\DataValidatorInterface;

class UpdateTaskDataValidator implements DataValidatorInterface
{
    public function validate(array $data): DataValidatorResponse
    {
        $updateTaskDataValidatorResponse = new DataValidatorResponse(true);

        if (!empty($data['id'])) {
            if (!Uuid::isValid($data['id'])) {
                $updateTaskDataValidatorResponse->setIsValid(false);
                $updateTaskDataValidatorResponse->setMessage('Task ID is not a valid UUID.');
                return $updateTaskDataValidatorResponse;
            }
        }

        if (isset($data['title']) && empty($data['title'])) {
            $updateTaskDataValidatorResponse->setIsValid(false);
            $updateTaskDataValidatorResponse->setMessage('Title cannot be empty.');
            return $updateTaskDataValidatorResponse;
        }

        if (isset($data['description']) && empty($data['description'])) {
            $updateTaskDataValidatorResponse->setIsValid(false);
            $updateTaskDataValidatorResponse->setMessage('Description cannot be empty.');
            return $updateTaskDataValidatorResponse;
        }

        if (!empty($data['dueDate'])) {
            $dueDate = new DateTime($data['dueDate']);
            $now = new DateTime();
            if ($dueDate < $now) {
                $updateTaskDataValidatorResponse->setIsValid(false);
                $updateTaskDataValidatorResponse->setMessage('Due date must be a future date.');
                return $updateTaskDataValidatorResponse;
            }
        }

        if (!empty($data['status'])) {
            $newStatus = Status::tryFrom($data['status']);
            if (!$newStatus) {
                $updateTaskDataValidatorResponse->setIsValid(false);
                $updateTaskDataValidatorResponse->setMessage('Invalid status value.');
                return $updateTaskDataValidatorResponse;
            }
        }

        return $updateTaskDataValidatorResponse;
    }
}
