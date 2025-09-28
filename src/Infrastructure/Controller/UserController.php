<?php

namespace App\Infrastructure\Controller;

use App\Application\Service\User\UserDataValidator;
use App\Application\Service\User\CreateUserRequestBuilder;
use App\Application\UseCase\User\CreateUser\CreateUserUseCase;
use App\Application\UseCase\User\ListUser\ListUserUseCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;


class UserController
{
    private CreateUserUseCase $createUserUseCase;
    private CreateUserRequestBuilder $createUserRequestBuilder;
    private UserDataValidator $userDataValidator;
    private ListUserUseCase $listUsersUseCase;

    public function __construct
    (
        CreateUserUseCase $createUserUseCase,
        CreateUserRequestBuilder $createUserRequestBuilder,
        UserDataValidator $userDataValidator,
        ListUserUseCase $listUsersUseCase
    )
    {
        $this->createUserUseCase = $createUserUseCase;
        $this->createUserRequestBuilder = $createUserRequestBuilder;
        $this->userDataValidator = $userDataValidator;
        $this->listUsersUseCase = $listUsersUseCase;
    }

    #[Route('/api/users', methods: ['POST'])]
    public function createUser(Request $request): JsonResponse
    {
        //Receive data from POST request
        $data = json_decode($request->getContent(), true);

        //Validate data service
        $userDataValidatorResponse = $this->userDataValidator->validate($data);
        if (!$userDataValidatorResponse->isValid()) {
            return new JsonResponse(
                [
                    'message' => $userDataValidatorResponse->getMessage(),
                    'statusCode' => Response::HTTP_BAD_REQUEST
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        //Build CreateUserRequest
        $createUserRequest = $this->createUserRequestBuilder->build($data);

        //Execute use case to create the user
        $createUserResponse = $this->createUserUseCase->execute($createUserRequest);

        return new JsonResponse(
            [
                'id' => $createUserResponse->getUser()?->getId(),
                'message' => $createUserResponse->getMessage(),
                'statusCode' => $createUserResponse->getCodeStatus()
            ],
            $createUserResponse->getCodeStatus()
        );
    }

    #[Route('/api/users', methods: ['GET'])]
    public function listUsers(Request $request): JsonResponse
    {
        //Execute use case to list users
        $listUsersResponse = $this->listUsersUseCase->execute();

        return new JsonResponse(
    [
        'users' => array_map(fn($user) => [
                'id' => $user->getId(),
                'name' => $user->getName(),
                'email' => $user->getEmail(),
            ], $listUsersResponse->getUsers()),
        'message' => $listUsersResponse->getMessage(),
        'statusCode' => $listUsersResponse->getCodeStatus()
    ],
    $listUsersResponse->getCodeStatus()
);
    }
}