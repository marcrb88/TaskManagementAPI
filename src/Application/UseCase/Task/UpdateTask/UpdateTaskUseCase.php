<?php

namespace App\Application\UseCase\Task\UpdateTask;

use App\Infrastructure\Repository\MySqlTaskRepository;
use App\Application\UseCase\Task\GetTaskDetail\GetTaskDetailResponse;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class UpdateTaskUseCase
{
    private MySqlTaskRepository $taskRepository;

    public function __construct
    (
        MySqlTaskRepository $taskRepository
    )
    {
        $this->taskRepository = $taskRepository;
        
    }
   public function execute(UpdateTaskRequest $updateTaskRequest): UpdateTaskResponse
   {
        $updateTaskResponse = new UpdateTaskResponse('Task updated successfully');
        $updateTaskResponse->setCodeStatus(Response::HTTP_OK);

        try {
            $task = $this->taskRepository->save($updateTaskRequest->getTask());
        } catch (Throwable $e) {
            $updateTaskResponse->setCodeStatus($e->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
            $updateTaskResponse->setMessage('Error obtaining tasks: ' . $e->getMessage());
            return $updateTaskResponse;
        }

        if (empty($task)) {
            $updateTaskResponse->setMessage('No tasks found');
            $updateTaskResponse->setCodeStatus(Response::HTTP_NOT_FOUND);
            return $updateTaskResponse;
        }

        return $updateTaskResponse;
    }
}

