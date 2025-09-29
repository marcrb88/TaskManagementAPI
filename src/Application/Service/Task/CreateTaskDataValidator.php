<?php

namespace App\Application\Service\Task;

use App\Application\Service\Response\TaskDataValidatorResponse;
use DateTime;
use Ramsey\Uuid\Uuid;
use App\Domain\Repository\DataValidatorInterface;

class CreateTaskDataValidator implements DataValidatorInterface
{
    public function validate(array $data): TaskDataValidatorResponse
    {
        $dataValidatorResponse = new TaskDataValidatorResponse(true);

        if (empty($data['title']) || empty($data['description'])) {
            $dataValidatorResponse->setIsValid(false);
            $dataValidatorResponse->setMessage('Title and description are required fields.');
            return $dataValidatorResponse;
        }

        if (!empty($data['dueDate'])) {
            $dueDate = new DateTime($data['dueDate']);
            $now = new DateTime();
            if ($dueDate < $now) {
                $dataValidatorResponse->setIsValid(false);
                $dataValidatorResponse->setMessage('Due date must be a future date.');
                return $dataValidatorResponse;
            }
        }

        if (!empty($data['assignedTo'])) {
            if (!Uuid::isValid($data['assignedTo'])) {
                $dataValidatorResponse->setIsValid(false);
                $dataValidatorResponse->setMessage('Assigned user ID is not a valid UUID.');
                return $dataValidatorResponse;
            }
        }

        return $dataValidatorResponse;
    }
}
