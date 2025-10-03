<?php

namespace App\Application\Service\User;

use App\Application\Service\Response\DataValidatorResponse;
use App\Domain\Repository\DataValidatorInterface;

class UserDataValidator implements DataValidatorInterface
{

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

        return $dataValidatorResponse;
    }
}
