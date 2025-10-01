<?php

namespace App\Application\UseCase\Task\UpdateTask;

use App\Application\UseCase\Task\UpdateTask\UpdateTaskRequest;
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
   public function execute(UpdateTaskRequest $updateTaskRequest): UpdateTaskResponse
   {
        $updateTaskResponse = new UpdateTaskResponse('Task updated successfully');
        $updateTaskResponse->setCodeStatus(Response::HTTP_OK);

        $task = $this->taskRepository->findById($updateTaskRequest->getId());

        if (empty($task)) {
            $updateTaskResponse->setMessage('Task not found.');
            $updateTaskResponse->setCodeStatus(Response::HTTP_NOT_FOUND);
            return $updateTaskResponse;
        }

        $currentStatus = $task->getStatus();

        //Business rules of the technical test: a completed task cannot transition to another status.
        if (!empty($updateTaskRequest->getStatus()->value)
             && $currentStatus->value === Status::Completed->value
             && $updateTaskRequest->getStatus()->value !== Status::Completed->value) {

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

        if (!empty($updateTaskRequest->getStatus()->value) && !in_array($updateTaskRequest->getStatus()->value, $validTransitions[$currentStatus->value])) {
            $updateTaskResponse->setMessage("Invalid status transition: The current task status is: ". $currentStatus->value. " and you want to change status to ".$updateTaskRequest->getStatus()->value .". Operation not allowed. The task has to follow the following schema: pending -> in_progress -> completed");
            $updateTaskResponse->setCodeStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
            return $updateTaskResponse;
        }

        //Fulfill Task entity with request values
        $this->updateTaskFields($task, $updateTaskRequest);

        try {
            $this->taskRepository->save($task);
        } catch (Throwable $e) {
            $updateTaskResponse->setCodeStatus($e->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
            $updateTaskResponse->setMessage('Error obtaining tasks: ' . $e->getMessage());
            return $updateTaskResponse;
        }

        return $updateTaskResponse;
    }

    private function updateTaskFields($task, UpdateTaskRequest $updateTaskRequest): void
    {
        if (!empty($updateTaskRequest->getTitle())) {
            $task->setTitle($updateTaskRequest->getTitle());
        }
        if (!empty($updateTaskRequest->getDescription())) {
            $task->setDescription($updateTaskRequest->getDescription());
        }
        if (!empty($updateTaskRequest->getStatus())) {
            $task->setStatus($updateTaskRequest->getStatus());
        }
        if (!empty($updateTaskRequest->getPriority())) {
            $task->setPriority($updateTaskRequest->getPriority());
        }
        if (!empty($updateTaskRequest->getAssignedTo())){
            $task->setAssignedTo($updateTaskRequest->getAssignedTo());
        }
        if (!empty($updateTaskRequest->getDueDate())){
            $task->setDueDate($updateTaskRequest->getDueDate());
        }
        if (!empty($updateTaskRequest->getCreatedAt())) {
            $task->setCreatedAt($updateTaskRequest->getCreatedAt());
        }
        if (!empty($updateTaskRequest->getUpdatedAt())) {
            $task->setUpdatedAt($updateTaskRequest->getUpdatedAt());
        }
    }
}

