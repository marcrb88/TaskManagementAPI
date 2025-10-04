<?php

namespace App\Application\Service\Task;

use App\Application\Service\Response\DataValidatorResponse;
use DateTime;
use App\Domain\ValueObject\Status;
use Ramsey\Uuid\Uuid;
use App\Domain\Repository\DataValidatorInterface;
use App\Application\Service\DateFormatValidator;

class UpdateTaskDataValidator implements DataValidatorInterface
{
    private DateFormatValidator $dateFormatValidator;

    public function __construct
    (
        DateFormatValidator $dateFormatValidator
    )
    {
        $this->dateFormatValidator = $dateFormatValidator;
        
    }

    public function validate(array $data): DataValidatorResponse
    {
        $updateTaskDataValidatorResponse = new DataValidatorResponse(true);

        if (!empty($data['id']) && !Uuid::isValid($data['id'])) {
            $updateTaskDataValidatorResponse->setIsValid(false);
            $updateTaskDataValidatorResponse->setMessage('Task ID is not a valid UUID.');
            return $updateTaskDataValidatorResponse;
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

        $datesToValidate = [];
        if (!empty($data['updatedAt'])) {
            $datesToValidate['updatedAt'] = $data['updatedAt'];
        }
        if (!empty($data['dueDate'])) {
            $datesToValidate['dueDate'] = $data['dueDate'];
        }
        if (!empty($data['createdAt'])) {
            $datesToValidate['createdAt'] = $data['createdAt'];
        }

        $dateFormatValidation = $this->dateFormatValidator->validate($datesToValidate);
        
        if (!$dateFormatValidation) {
            $updateTaskDataValidatorResponse->setIsValid(false);
            $updateTaskDataValidatorResponse->setMessage('Invalid date format for date fields. Expected format: `YYYY-MM-DDTHH:MM:SS`.');
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
