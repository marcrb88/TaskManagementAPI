<?php

namespace App\Application\UseCase\User\ListUser;

use App\Infrastructure\Repository\MySqlUserRepository;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ListUserUseCase
{
    private MySqlUserRepository $userRepository;

    public function __construct
    (
        MySqlUserRepository $userRepository
    )
    {
        $this->userRepository = $userRepository;
    }

    public function execute(): ListUserResponse
    {
        $listUserResponse = new ListUserResponse('Users obtained successfully');
        $listUserResponse->setCodeStatus(Response::HTTP_OK);

        try {
            $users = $this->userRepository->findAll();
        } catch (Throwable $e) {
            $listUserResponse->setCodeStatus($e->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
            $listUserResponse->setMessage('Error obtaining users: ' . $e->getMessage());
            return $listUserResponse;
        }

        if (empty($users)) {
            $listUserResponse->setMessage('No users found');
            $listUserResponse->setCodeStatus(Response::HTTP_NOT_FOUND);
            return $listUserResponse;
        }

        $listUserResponse->setUsers($users);

        //Return DTO response
        return $listUserResponse;
    }
}