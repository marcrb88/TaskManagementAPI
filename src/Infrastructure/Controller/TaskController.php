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
use App\Application\Service\Task\UpdateTaskDataValidator;
use App\Application\UseCase\Task\AssignTaskToUser\AssignTaskToUserRequest;
use App\Application\UseCase\Task\AssignTaskToUser\AssignTaskToUserUseCase;
use App\Application\UseCase\Task\DeleteTask\DeleteTaskRequest;
use App\Application\UseCase\Task\GetTaskDetail\GetTaskDetailRequest;
use App\Application\UseCase\Task\GetTaskDetail\GetTaskDetailUseCase;
use App\Application\UseCase\Task\ListTask\ListTaskUseCase;
use App\Application\UseCase\Task\UpdateTask\UpdateTaskUseCase;
use Ramsey\Uuid\Nonstandard\Uuid;
use App\Application\UseCase\Task\DeleteTask\DeleteTaskUseCase;

class TaskController
{
    private CreateTaskUseCase $createTaskUseCase;
    private CreateTaskDataValidator $createTaskDataValidator;
    private ListTaskUseCase $listTasksUseCase;
    private TaskRequestBuilderFactory $taskRequestBuilderFactory;
    private FilterTaskDataValidator $filterTaskDataValidator;
    private GetTaskDetailUseCase $getTaskDetailUse;
    private UpdateTaskUseCase $updateTaskUseCase;
    private UpdateTaskDataValidator $updateTaskDataValidator;
    private DeleteTaskUseCase $deleteTaskUseCase;
    private AssignTaskToUserUseCase $assignTaskToUserUse;

    public function __construct
    (
        CreateTaskUseCase $createTaskUseCase,
        CreateTaskDataValidator $createTaskDataValidator,
        ListTaskUseCase $listTasksUseCase,
        TaskRequestBuilderFactory $taskRequestBuilderFactory,
        FilterTaskDataValidator $filterTaskDataValidator,
        GetTaskDetailUseCase $getTaskDetailUse,
        UpdateTaskUseCase $updateTaskUseCase,
        UpdateTaskDataValidator $updateTaskDataValidator,
        DeleteTaskUseCase $deleteTaskUseCase,
        AssignTaskToUserUseCase $assignTaskToUserUse
    )
    {
        $this->createTaskUseCase = $createTaskUseCase;
        $this->createTaskDataValidator = $createTaskDataValidator;
        $this->listTasksUseCase = $listTasksUseCase;
        $this->taskRequestBuilderFactory = $taskRequestBuilderFactory;
        $this->filterTaskDataValidator = $filterTaskDataValidator;
        $this->getTaskDetailUse = $getTaskDetailUse;
        $this->updateTaskUseCase = $updateTaskUseCase;
        $this->updateTaskDataValidator = $updateTaskDataValidator;
        $this->deleteTaskUseCase = $deleteTaskUseCase;
        $this->assignTaskToUserUse = $assignTaskToUserUse;
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

        if (empty($id)) {
            return new JsonResponse(
                [
                    'message' => 'Task ID is required.',
                    'statusCode' => Response::HTTP_BAD_REQUEST
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

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

    #[Route('/api/tasks/{id}', methods: ['PUT'])]
    public function updateTask(Request $request): JsonResponse
    {
        //Recieve data from PUT request
        $data = json_decode($request->getContent(), true);
        $id = $request->attributes->get('id');
        $data['id'] = $id;

        //Validate data to update task
        $updateTaskDataValidatorResponse = $this->updateTaskDataValidator->validate($data);

        if (!$updateTaskDataValidatorResponse->isValid()) {
            return new JsonResponse(
                [
                    'message' => $updateTaskDataValidatorResponse->getMessage(),
                    'statusCode' => Response::HTTP_BAD_REQUEST
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        $createUpdateTaskRequestBuilder = $this->taskRequestBuilderFactory->getBuilder(TaskRequestBuilderFactory::TYPE_UPDATE);
        $createUpdateTaskRequest = $createUpdateTaskRequestBuilder->build($data);
        
        //Execute use case to obtain task details
        $listTasksResponse = $this->updateTaskUseCase->execute($createUpdateTaskRequest);
        
        return new JsonResponse(
            [
                'message' => $listTasksResponse->getMessage(),
                'statusCode' => $listTasksResponse->getCodeStatus()
            ],
            $listTasksResponse->getCodeStatus()
        );
    }

    #[Route('/api/tasks/{id}', methods: ['DELETE'])]
    public function deleteTask(Request $request): JsonResponse
    {
        //Recieve id from path request
        $id = $request->attributes->get('id');

        if (empty($id)) {
            return new JsonResponse(
                [
                    'message' => 'Task ID is required.',
                    'statusCode' => Response::HTTP_BAD_REQUEST
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        if (!Uuid::isValid($id)) {
            return new JsonResponse(
                [
                    'message' => 'Task ID is not a valid UUID.',
                    'statusCode' => Response::HTTP_BAD_REQUEST
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        $deleteTaskRequest = new DeleteTaskRequest($id);
        
        //Execute use case to obtain task details
        $listTasksResponse = $this->deleteTaskUseCase->execute($deleteTaskRequest);
        
        return new JsonResponse(
            [
                'message' => $listTasksResponse->getMessage(),
                'statusCode' => $listTasksResponse->getCodeStatus()
            ],
            $listTasksResponse->getCodeStatus()
        );
    }

    #[Route('/api/tasks/{id}/assign', methods: ['PATCH'])]
    public function assignTaskToUser(Request $request): JsonResponse
    {
        //Recieve id from path request
        $taskId = $request->attributes->get('id');
        $data = json_decode($request->getContent(), true);
        $assignedToId = $data['assignedTo'];

        $assignTaskToUserRequest = new AssignTaskToUserRequest($taskId, $assignedToId);
        
        //Execute use case to assign task to user 
        $listTasksResponse = $this->assignTaskToUserUse->execute($assignTaskToUserRequest);
        
        return new JsonResponse(
            [
                'message' => $listTasksResponse->getMessage(),
                'statusCode' => $listTasksResponse->getCodeStatus()
            ],
            $listTasksResponse->getCodeStatus()
        );
    }
}