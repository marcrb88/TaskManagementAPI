<?php

namespace App\Application\Service\Task;

use App\Application\Service\Response\DataValidatorResponse;
use App\Domain\Repository\DataValidatorInterface;
use App\Domain\ValueObject\Status;
use App\Domain\ValueObject\Priority;
use DateTime;

class FilterTaskDataValidator implements DataValidatorInterface
{
    public function validate(array $data): DataValidatorResponse
    {
        $filterTaskDataValidatorResponse = new DataValidatorResponse(true);
        
        if (!empty($data['status'])) {
            $status = $this->validateStatus($data['status']);
            if (empty($status)) {
                $filterTaskDataValidatorResponse->setIsValid(false);
                $filterTaskDataValidatorResponse->setMessage('Invalid status value: ' . $data['status']);
            }
        }

        if (!empty($data['priority'])) {
            $priority = $this->validatePriority($data['priority']);
            if (empty($priority)) {
                $filterTaskDataValidatorResponse->setIsValid(false);
                $filterTaskDataValidatorResponse->setMessage('Invalid priority value: ' . $data['priority']);
            }
        }

        $datesToValidate = ['createdAt', 'dueDate', 'updatedAt'];

        foreach ($datesToValidate as $dateField) {
            if (!empty($data[$dateField])) {
                $date = \DateTime::createFromFormat('Y-m-d', $data[$dateField]);
                if ($date === false) {
                    $filterTaskDataValidatorResponse->setIsValid(false);
                    $filterTaskDataValidatorResponse->setMessage(
                        'Invalid date value for `' . $dateField . '` : ' . $data[$dateField] . 
                        '. Date filters must be passed in the `YYYY-MM-DD` format.'
                    );
                }
            }
        }

        return $filterTaskDataValidatorResponse;
    }

    private function validateStatus(?string $status): ?Status
    {
        return $status ? Status::tryFrom($status) : null;
    }
    private function validatePriority(?string $priority): ?Priority
    {
        return $priority ? Priority::tryFrom($priority) : null;
    }
}
