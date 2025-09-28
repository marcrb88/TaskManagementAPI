<?php

namespace App\Infrastructure\Controller;

use App\Application\Service\CreateTaskRequestBuilder;
use App\Application\UseCase\Task\CreateTask\CreateTaskUseCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Application\Service\TaskDataValidator;


class TaskController
{
    private CreateTaskUseCase $createTaskUseCase;
    private CreateTaskRequestBuilder $createTaskRequestBuilder;
    private TaskDataValidator $taskDataValidator;

    public function __construct
    (
         CreateTaskUseCase $createTaskUseCase,
         CreateTaskRequestBuilder $createTaskRequestBuilder,
            TaskDataValidator $taskDataValidator
    )
    {
        $this->createTaskUseCase = $createTaskUseCase;
        $this->createTaskRequestBuilder = $createTaskRequestBuilder;
        $this->taskDataValidator = $taskDataValidator;
    }
    
    #[Route('/api/tasks', methods: ['POST'])]
    public function createTask(Request $request): JsonResponse
    {
        //Recieve data from POST request
        $data = json_decode($request->getContent(), true);

        //Validate data service
        $taskDataValidatorResponse = $this->taskDataValidator->validate($data);
        if (!$taskDataValidatorResponse->isValid()) {
            return new JsonResponse(
                [
                    'message' => $taskDataValidatorResponse->getMessage(),
                    'statusCode' => Response::HTTP_BAD_REQUEST
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        //Build CreateTaskRequest
        $createTaskRequest = $this->createTaskRequestBuilder->build($data);

        //Execute use case to create the task
        $createTaskResponse = $this->createTaskUseCase->execute($createTaskRequest);

        return new JsonResponse(
            [
                'id' => $createTaskResponse->getTask()?->getId(),
                'message' => $createTaskResponse->getMessage(),
                'statusCode' => $createTaskResponse->getCodeStatus()
            ],
            $createTaskResponse->getCodeStatus()
        );
    }

}