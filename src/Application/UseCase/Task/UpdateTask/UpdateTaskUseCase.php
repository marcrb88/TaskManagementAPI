<?php

namespace App\Application\UseCase\Task\UpdateTask;

use App\Application\UseCase\Task\CreateTask\CreateUpdateTaskRequest;
use App\Infrastructure\Repository\MySqlTaskRepository;
use Symfony\Component\HttpFoundation\Response;
use Throwable;
use App\Domain\Model\Task;

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
   public function execute(CreateUpdateTaskRequest $createUpdateTaskRequest): UpdateTaskResponse
   {
        $updateTaskResponse = new UpdateTaskResponse('Task updated successfully');
        $updateTaskResponse->setCodeStatus(Response::HTTP_OK);

        $task = $this->taskRepository->findById($createUpdateTaskRequest->getId());

        if (!empty($createUpdateTaskRequest->getTitle())) {
            $task->setTitle($createUpdateTaskRequest->getTitle());
        }
        if (!empty($createUpdateTaskRequest->getDescription())) {
            $task->setDescription($createUpdateTaskRequest->getDescription());
        }
        if (!empty($createUpdateTaskRequest->getStatus())) {
            $task->setStatus($createUpdateTaskRequest->getStatus());
        }
        if (!empty($createUpdateTaskRequest->getPriority())) {
            $task->setPriority($createUpdateTaskRequest->getPriority());
        }
        if (!empty($createUpdateTaskRequest->getAssignedTo())){
            $task->setAssignedTo($createUpdateTaskRequest->getAssignedTo());
        }
        if (!empty($createUpdateTaskRequest->getDueDate())){
            $task->setDueDate($createUpdateTaskRequest->getDueDate());
        }
        if (!empty($createUpdateTaskRequest->getCreatedAt())) {
            $task->setCreatedAt($createUpdateTaskRequest->getCreatedAt());
        }
        if (!empty($createUpdateTaskRequest->getUpdatedAt())) {
            $task->setUpdatedAt($createUpdateTaskRequest->getUpdatedAt());
        }

        try {
            $this->taskRepository->save($task);
        } catch (Throwable $e) {
            $updateTaskResponse->setCodeStatus($e->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
            $updateTaskResponse->setMessage('Error obtaining tasks: ' . $e->getMessage());
            return $updateTaskResponse;
        }

        return $updateTaskResponse;
    }
}

