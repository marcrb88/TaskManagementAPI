<?php

namespace App\Application\UseCase\Task\DeleteTask;

use App\Domain\Repository\TaskRepositoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class DeleteTaskUseCase
{
    private TaskRepositoryInterface $taskRepositoryInterface;

    public function __construct
    (
        TaskRepositoryInterface $taskRepositoryInterface
    )
    {
        $this->taskRepositoryInterface = $taskRepositoryInterface;
        
    }
   public function execute(DeleteTaskRequest $deleteTaskRequest): DeleteTaskResponse
   {
        $deleteTaskResponse = new DeleteTaskResponse('Task deleted succesfully');
        $deleteTaskResponse->setCodeStatus(Response::HTTP_OK);

        try {
            $task = $this->taskRepositoryInterface->findOneBy([
                'id'     => $deleteTaskRequest->getId(),
                'status' => 'pending',
            ]);
            
            if (empty($task)) {
                $deleteTaskResponse->setMessage('No task found to delete. Remember you only can delete pending tasks');
                $deleteTaskResponse->setCodeStatus(Response::HTTP_NOT_FOUND);
                return $deleteTaskResponse;
            }

            $this->taskRepositoryInterface->delete($task);
            
        } catch (Throwable $e) {
            $deleteTaskResponse->setCodeStatus($e->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
            $deleteTaskResponse->setMessage('Error obtaining tasks: ' . $e->getMessage());
            return $deleteTaskResponse;
        }

        return $deleteTaskResponse;
    }
}

