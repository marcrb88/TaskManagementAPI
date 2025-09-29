<?php

namespace App\Application\UseCase\Task\UpdateTask;

use App\Application\UseCase\Task\CreateTask\CreateUpdateTaskRequest;
use App\Infrastructure\Repository\MySqlTaskRepository;
use Symfony\Component\HttpFoundation\Response;
use Throwable;
use App\Domain\ValueObject\Status;

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

        if (empty($task)) {
            $updateTaskResponse->setMessage('Task not found.');
            $updateTaskResponse->setCodeStatus(Response::HTTP_NOT_FOUND);
            return $updateTaskResponse;
        }

        $currentStatus = $task->getStatus();

        //Business rules of the technical test: a completed task cannot transition to another status.
        if (!empty($createUpdateTaskRequest->getStatus()->value) && $currentStatus->value === Status::Completed && $createUpdateTaskRequest->getStatus()->value !== Status::Completed) {
            $updateTaskResponse->setMessage('A completed task cannot change to another status.');
            $updateTaskResponse->setCodeStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
            return $updateTaskResponse;
        }

        //Business rules of the technical test: the status change has to follow the flow: pending -> in_progress -> completed
        $validTransitions = [
            Status::Pending->value     => [Status::InProgress->value],
            Status::InProgress->value => [Status::Completed->value],
            Status::Completed->value   => [] 
        ];

        if (!empty($createUpdateTaskRequest->getStatus()->value) && !in_array($createUpdateTaskRequest->getStatus()->value, $validTransitions[$currentStatus->value])) {
            $updateTaskResponse->setMessage("Invalid status transition: The current task status is: ". $currentStatus->value. " and you want to change status to ".$createUpdateTaskRequest->getStatus()->value .". Operation not allowed. The task has to follow the following schema: pending -> in_progress -> completed");
            $updateTaskResponse->setCodeStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
            return $updateTaskResponse;
        }

        //Fulfill Task entity with request values
        $this->updateTaskFields($task, $createUpdateTaskRequest);

        try {
            $this->taskRepository->save($task);
        } catch (Throwable $e) {
            $updateTaskResponse->setCodeStatus($e->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
            $updateTaskResponse->setMessage('Error obtaining tasks: ' . $e->getMessage());
            return $updateTaskResponse;
        }

        return $updateTaskResponse;
    }

    private function updateTaskFields($task, CreateUpdateTaskRequest $createUpdateTaskRequest): void
    {
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
    }
}

