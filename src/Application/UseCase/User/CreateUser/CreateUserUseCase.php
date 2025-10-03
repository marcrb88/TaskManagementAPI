<?php

namespace App\Application\UseCase\User\CreateUser;

use App\Infrastructure\Repository\MySqlUserRepository;
use Symfony\Component\HttpFoundation\Response;
use App\Domain\Model\User;
use App\Application\UseCase\User\CreateUser\CreateUserRequest;
use App\Application\UseCase\User\CreateUser\CreateUserResponse;
use App\Domain\Repository\UserRepositoryInterface;
use Throwable;

class CreateUserUseCase
{
    private UserRepositoryInterface $userRepositoryInterface;

    public function __construct
    (
        UserRepositoryInterface $userRepositoryInterface
    )
    {
        $this->userRepositoryInterface = $userRepositoryInterface;
    }

    public function execute(CreateUserRequest $request): CreateUserResponse
    {
        $createUserResponse = new CreateUserResponse('User created successfully');
        $createUserResponse->setCodeStatus(Response::HTTP_CREATED);

        $user = new User(
            $request->getName(),
            $request->getEmail()
        );

        $existingUser = $this->userRepositoryInterface->findByEmail($user->getEmail());

        if (!empty($existingUser)) {
            $createUserResponse->setMessage("User with this email already exists.");
            $createUserResponse->setCodeStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
            return $createUserResponse;
        }

        //Save user entity in database
        try {
            $this->userRepositoryInterface->save($user);
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