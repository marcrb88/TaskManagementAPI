<?php

namespace App\Infrastructure\Controller;

use App\Application\UseCase\Task\CreateTask\CreateTaskUseCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Application\Factory\TaskRequestBuilderFactory;
use App\Application\Service\Task\CreateTaskDataValidator;
use App\Application\Service\Task\FilterTaskDataValidator;
use App\Application\UseCase\Task\GetTaskDetail\GetTaskDetailRequest;
use App\Application\UseCase\Task\GetTaskDetail\GetTaskDetailUseCase;
use App\Application\UseCase\Task\ListTask\ListTaskUseCase;
use InvalidArgumentException;
use Ramsey\Uuid\Nonstandard\Uuid;

class TaskController
{
    private CreateTaskUseCase $createTaskUseCase;
    private CreateTaskDataValidator $createTaskDataValidator;
    private ListTaskUseCase $listTasksUseCase;
    private TaskRequestBuilderFactory $taskRequestBuilderFactory;
    private FilterTaskDataValidator $filterTaskDataValidator;
    private GetTaskDetailUseCase $getTaskDetailUse;

    public function __construct
    (
        CreateTaskUseCase $createTaskUseCase,
        CreateTaskDataValidator $createTaskDataValidator,
        ListTaskUseCase $listTasksUseCase,
        TaskRequestBuilderFactory $taskRequestBuilderFactory,
        FilterTaskDataValidator $filterTaskDataValidator,
        GetTaskDetailUseCase $getTaskDetailUse
    )
    {
        $this->createTaskUseCase = $createTaskUseCase;
        $this->createTaskDataValidator = $createTaskDataValidator;
        $this->listTasksUseCase = $listTasksUseCase;
        $this->taskRequestBuilderFactory = $taskRequestBuilderFactory;
        $this->filterTaskDataValidator = $filterTaskDataValidator;
        $this->getTaskDetailUse = $getTaskDetailUse;
    }
    
    #[Route('/api/tasks', methods: ['POST'])]
    public function createTask(Request $request): JsonResponse
    {
        //Recieve data from POST request
        $data = json_decode($request->getContent(), true);

        //Validate data service
        $createTaskDataValidatorResponse = $this->createTaskDataValidator->validate($data);
        if (!$createTaskDataValidatorResponse->isValid()) {
            return new JsonResponse(
                [
                    'message' => $createTaskDataValidatorResponse->getMessage(),
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

        //Validate filter data service
        $filterTaskDataValidatorResponse = $this->filterTaskDataValidator->validate($request->query->all());

        if (!$filterTaskDataValidatorResponse->isValid()) {
            return new JsonResponse(
                [
                    'message' => $filterTaskDataValidatorResponse->getMessage(),
                    'statusCode' => Response::HTTP_BAD_REQUEST
                ],
                Response::HTTP_BAD_REQUEST
            );
        }
        
        //Obtain the builder to create the filter task request
        $filterTaskBuilder = $this->taskRequestBuilderFactory->getBuilder(TaskRequestBuilderFactory::TYPE_FILTER);
       
        //Build CreateFilterRequest
        $filterTaskRequest = $filterTaskBuilder->build($request->query->all());

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

    #[Route('/api/tasks/{id}', methods: ['GET'])]
    public function obtainTaskDetail(Request $request): JsonResponse
    {
        $id = $request->attributes->get('id');

        if (!Uuid::isValid($id)) {
            return new JsonResponse(
                [
                    'message' => "Invalid task id",
                    'statusCode' => Response::HTTP_BAD_REQUEST
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        $getTaskDetailRequest = new GetTaskDetailRequest($id);

        //Execute use case to obtain task details
        $listTasksResponse = $this->getTaskDetailUse->execute($getTaskDetailRequest);
        
        return new JsonResponse(
            [
                'task' => $listTasksResponse->getTask()?->toArray(),
                'message' => $listTasksResponse->getMessage(),
                'statusCode' => $listTasksResponse->getCodeStatus()
            ],
            $listTasksResponse->getCodeStatus()
        );
    }
}