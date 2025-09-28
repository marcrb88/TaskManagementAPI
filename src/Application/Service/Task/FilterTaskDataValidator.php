<?php

namespace App\Application\Service\Task;

use App\Application\Service\Response\FilterTaskDataValidatorResponse;
use App\Domain\Repository\DataValidatorInterface;
use App\Domain\ValueObject\Status;
use App\Domain\ValueObject\Priority;

class FilterTaskDataValidator implements DataValidatorInterface
{
    public function validate(array $data): FilterTaskDataValidatorResponse
    {
        $filterTaskDataValidatorResponse = new FilterTaskDataValidatorResponse(true);
        
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
