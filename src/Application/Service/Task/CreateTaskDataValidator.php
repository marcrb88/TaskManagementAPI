<?php

namespace App\Application\Service\Task;

use App\Application\Service\Response\DataValidatorResponse;
use DateTime;
use Ramsey\Uuid\Uuid;
use App\Domain\Repository\DataValidatorInterface;
use App\Application\Service\DateFormatValidator;

class CreateTaskDataValidator implements DataValidatorInterface
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
        $dataValidatorResponse = new DataValidatorResponse(true);

        if (empty($data['title']) || empty($data['description'])) {
            $dataValidatorResponse->setIsValid(false);
            $dataValidatorResponse->setMessage('Title and description are required fields.');
            return $dataValidatorResponse;
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
            $dataValidatorResponse->setIsValid(false);
            $dataValidatorResponse->setMessage('Invalid date format for date fields. Expected format: `YYYY-MM-DDTHH:MM:SS`.');
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
