<?php

namespace App\Application\Service\User;

use App\Application\Service\Response\DataValidatorResponse;
use App\Domain\Repository\DataValidatorInterface;
use App\Application\Service\DateFormatValidator;

class UserDataValidator implements DataValidatorInterface
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

        if (empty($data['name']) || empty($data['email'])) {
            $dataValidatorResponse->setIsValid(false);
            $dataValidatorResponse->setMessage('Name and email are required fields.');
            return $dataValidatorResponse;
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $dataValidatorResponse->setIsValid(false);
            $dataValidatorResponse->setMessage('Email is not valid.');
            return $dataValidatorResponse;
        }

         $datesToValidate = [];
        if (!empty($data['createdAt'])) {
            $datesToValidate['createdAt'] = $data['createdAt'];
        }

        $dateFormatValidation = $this->dateFormatValidator->validate($datesToValidate);
        
        if (!$dateFormatValidation) {
            $dataValidatorResponse->setIsValid(false);
            $dataValidatorResponse->setMessage('Invalid date format for date fields. Expected format: `YYYY-MM-DDTHH:MM:SS`.');
            return $dataValidatorResponse;
        }

        return $dataValidatorResponse;
    }
}
