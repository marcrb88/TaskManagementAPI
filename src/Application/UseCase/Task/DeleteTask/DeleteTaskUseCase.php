<?php

namespace App\Application\UseCase\Task\DeleteTask;

use App\Infrastructure\Repository\MySqlTaskRepository;
use App\Application\UseCase\Task\GetTaskDetail\GetTaskDetailResponse;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class DeleteTaskUseCase
{
    private MySqlTaskRepository $taskRepository;

    public function __construct
    (
        MySqlTaskRepository $taskRepository
    )
    {
        $this->taskRepository = $taskRepository;
        
    }
   public function execute(DeleteTaskRequest $deleteTaskRequest): DeleteTaskResponse
   {
        $deleteTaskResponse = new DeleteTaskResponse('Task deleted succesfully');
        $deleteTaskResponse->setCodeStatus(Response::HTTP_OK);

        try {
            $task = $this->taskRepository->findOneBy([
                'id'     => $deleteTaskRequest->getId(),
                'status' => 'pending',
            ]);
            
            if (empty($task)) {
                $deleteTaskResponse->setMessage('No task found to delete');
                $deleteTaskResponse->setCodeStatus(Response::HTTP_NOT_FOUND);
                return $deleteTaskResponse;
            }

            $this->taskRepository->delete($task);
            
        } catch (Throwable $e) {
            $deleteTaskResponse->setCodeStatus($e->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
            $deleteTaskResponse->setMessage('Error obtaining tasks: ' . $e->getMessage());
            return $deleteTaskResponse;
        }

        return $deleteTaskResponse;
    }
}

