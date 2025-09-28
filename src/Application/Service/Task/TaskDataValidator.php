<?php

namespace App\Application\Service\Task;

use App\Application\Service\Response\DataValidatorResponse;
use App\Domain\Repository\UserRepositoryInterface;
use DateTime;
use Ramsey\Uuid\Uuid;

class TaskDataValidator
{
    public function __construct
    (
        private UserRepositoryInterface $userRepository
    ) {
        $this->userRepository = $userRepository;
    }

    public function validate(array $data): DataValidatorResponse
    {
        $dataValidatorResponse = new DataValidatorResponse(true);

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

            $user = $this->userRepository->findById($data['assignedTo']);
            if (empty($user)) {
                $dataValidatorResponse->setIsValid(false);
                $dataValidatorResponse->setMessage('Assigned user does not exist.');
                return $dataValidatorResponse;
            }
        }

        return $dataValidatorResponse;
    }
}
