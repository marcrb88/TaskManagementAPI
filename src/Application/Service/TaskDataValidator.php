<?php

namespace App\Application\Service;

use App\Domain\Repository\UserRepositoryInterface;
use DateTime;
use App\Application\Service\Response\TaskDataValidatorResponse;
use Ramsey\Uuid\Uuid;

class TaskDataValidator
{
    public function __construct
    (
        private UserRepositoryInterface $userRepository
    ) {
        $this->userRepository = $userRepository;
    }

    public function validate(array $data): TaskDataValidatorResponse
    {
        $taskDataValidatorResponse = new TaskDataValidatorResponse(true);
        
        if (empty($data['title']) || empty($data['description'])) {
            $taskDataValidatorResponse->setIsValid(false);
            $taskDataValidatorResponse->setMessage('Title and description are required fields.');
            return $taskDataValidatorResponse;
        }

        if (!empty($data['dueDate'])) {
            $dueDate = new DateTime($data['dueDate']);
            $now = new DateTime();
            if ($dueDate < $now) {
                $taskDataValidatorResponse->setIsValid(false);
                $taskDataValidatorResponse->setMessage('Due date must be a future date.');
                return $taskDataValidatorResponse;
            }
        }

        if (!empty($data['assignedTo'])) {
            if (!Uuid::isValid($data['assignedTo'])) {
                $taskDataValidatorResponse->setIsValid(false);
                $taskDataValidatorResponse->setMessage('Assigned user ID is not a valid UUID.');
                return $taskDataValidatorResponse;
            }

            $user = $this->userRepository->findById($data['assignedTo']);
            if (empty($user)) {
                $taskDataValidatorResponse->setIsValid(false);
                $taskDataValidatorResponse->setMessage('Assigned user does not exist.');
                return $taskDataValidatorResponse;
            }
        }

        return $taskDataValidatorResponse;
    }
}
