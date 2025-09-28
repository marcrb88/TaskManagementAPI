<?php

namespace App\Application\Service\User;

use App\Application\Service\Response\DataValidatorResponse;
use App\Domain\Repository\UserRepositoryInterface;

class UserDataValidator
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

        $existingUser = $this->userRepository->findByEmail($data['email']);
        if ($existingUser) {
            $dataValidatorResponse->setIsValid(false);
            $dataValidatorResponse->setMessage('Email is already in use.');
            return $dataValidatorResponse;
        }

        return $dataValidatorResponse;
    }
}
