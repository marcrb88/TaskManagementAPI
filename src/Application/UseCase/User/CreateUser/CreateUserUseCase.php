<?php

namespace App\Application\UseCase\User\CreateUser;

use App\Infrastructure\Repository\MySqlUserRepository;
use Symfony\Component\HttpFoundation\Response;
use App\Domain\Model\User;
use App\Application\UseCase\User\CreateUser\CreateUserRequest;
use App\Application\UseCase\User\CreateUser\CreateUserResponse;
use Throwable;

class CreateUserUseCase
{
    private MySqlUserRepository $userRepository;

    public function __construct
    (
        MySqlUserRepository $userRepository
    )
    {
        $this->userRepository = $userRepository;
    }

    public function execute(CreateUserRequest $request): CreateUserResponse
    {
        $createUserResponse = new CreateUserResponse('User created successfully');
        $createUserResponse->setCodeStatus(Response::HTTP_CREATED);

        $user = new User(
            $request->getName(),
            $request->getEmail()
        );

        //Save user entity in database
        try {
            $this->userRepository->save($user);
        } catch (Throwable $e) {
            $createUserResponse->setCodeStatus($e->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
            $createUserResponse->setMessage('Error creating user: ' . $e->getMessage());
            return $createUserResponse;
        }

        $createUserResponse->setUser($user);

        //Return DTO response
        return $createUserResponse;
    }
}