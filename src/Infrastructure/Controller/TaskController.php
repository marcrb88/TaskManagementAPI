<?php

namespace App\Infrastructure\Controller;

use App\Application\UseCase\Task\CreateTask\CreateTaskUseCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Application\Service\Task\TaskDataValidator;
use App\Application\Factory\TaskRequestBuilderFactory;
use App\Application\UseCase\Task\ListTask\ListTaskUseCase;
use InvalidArgumentException;

class TaskController
{
    private CreateTaskUseCase $createTaskUseCase;
    private TaskDataValidator $taskDataValidator;
    private ListTaskUseCase $listTasksUseCase;
    private TaskRequestBuilderFactory $taskRequestBuilderFactory;

    public function __construct
    (
        CreateTaskUseCase $createTaskUseCase,
        TaskDataValidator $taskDataValidator,
        ListTaskUseCase $listTasksUseCase,
        TaskRequestBuilderFactory $taskRequestBuilderFactory
    )
    {
        $this->createTaskUseCase = $createTaskUseCase;
        $this->taskDataValidator = $taskDataValidator;
        $this->listTasksUseCase = $listTasksUseCase;
        $this->taskRequestBuilderFactory = $taskRequestBuilderFactory;
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

        //Obtain the builder to create the task request
        $createTaskBuilder = $this->taskRequestBuilderFactory->getBuilder(TaskRequestBuilderFactory::TYPE_CREATE);

        //Build CreateTaskRequest
        $createTaskRequest = $createTaskBuilder->build($data);

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

    #[Route('/api/tasks', methods: ['GET'])]
    public function listTasks(Request $request): JsonResponse
    {

        //Obtain the builder to create the filter task request
        $filterTaskBuilder = $this->taskRequestBuilderFactory->getBuilder(TaskRequestBuilderFactory::TYPE_FILTER);
       
        //Build CreateFilterRequest
        try {
            $filterTaskRequest = $filterTaskBuilder->build($request->query->all());
        } catch (InvalidArgumentException $e) {
            return new JsonResponse(
                [
                    'message' => 'Invalid filter value: ' . $e->getMessage(),
                    'statusCode' => Response::HTTP_BAD_REQUEST
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        //Execute use case to list tasks
        $listTasksResponse = $this->listTasksUseCase->execute($filterTaskRequest);
        
        // Convert tasks to array format for JSON response
        $tasksArray = array_map(fn($task) => $task->toArray(), $listTasksResponse->getTasks());
        
        return new JsonResponse(
            [
                'tasks' => $tasksArray,
                'message' => $listTasksResponse->getMessage(),
                'statusCode' => $listTasksResponse->getCodeStatus()
            ],
            $listTasksResponse->getCodeStatus()
        );
    }
}