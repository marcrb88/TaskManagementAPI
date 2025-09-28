<?php

namespace App\Application\Service\User;

use App\Application\UseCase\User\CreateUser\CreateUserRequest;
use DateTime;


class CreateUserRequestBuilder
{
    public function build(array $data): CreateUserRequest
    {
        $createUserRequest = new CreateUserRequest(
            $data['name'],
            $data['email']
        );

        if (!empty($data['createdAt'])) {
            $createUserRequest->setCreatedAt(new DateTime($data['createdAt']));
        }

        return $createUserRequest;
    }

}
