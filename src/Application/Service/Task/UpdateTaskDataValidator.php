<?php

namespace App\Application\Service\Task;

use App\Application\Service\Response\TaskDataValidatorResponse;
use DateTime;
use App\Domain\ValueObject\Status;
use App\Infrastructure\Repository\MySqlTaskRepository;
use Ramsey\Uuid\Uuid;
use App\Domain\Repository\DataValidatorInterface;

class UpdateTaskDataValidator implements DataValidatorInterface
{
    private MySqlTaskRepository $mySqlTaskRepository;

    public function __construct
    (
        MySqlTaskRepository $mySqlTaskRepository
    )
    {
        $this->mySqlTaskRepository = $mySqlTaskRepository;
    }
    public function validate(array $data): TaskDataValidatorResponse
    {
        $updateTaskDataValidatorResponse = new TaskDataValidatorResponse(true);

        if (!empty($data['id'])) {
            if (!Uuid::isValid($data['id'])) {
                $updateTaskDataValidatorResponse->setIsValid(false);
                $updateTaskDataValidatorResponse->setMessage('Task ID is not a valid UUID.');
                return $updateTaskDataValidatorResponse;
            }
        }

        if (isset($data['title']) && empty($data['title'])) {
            $updateTaskDataValidatorResponse->setIsValid(false);
            $updateTaskDataValidatorResponse->setMessage('Title cannot be empty.');
            return $updateTaskDataValidatorResponse;
        }

        if (isset($data['description']) && empty($data['description'])) {
            $updateTaskDataValidatorResponse->setIsValid(false);
            $updateTaskDataValidatorResponse->setMessage('Description cannot be empty.');
            return $updateTaskDataValidatorResponse;
        }

        if (!empty($data['dueDate'])) {
            $dueDate = new DateTime($data['dueDate']);
            $now = new DateTime();
            if ($dueDate < $now) {
                $updateTaskDataValidatorResponse->setIsValid(false);
                $updateTaskDataValidatorResponse->setMessage('Due date must be a future date.');
                return $updateTaskDataValidatorResponse;
            }
        }

        if (!empty($data['status'])) {
            $newStatus = Status::tryFrom($data['status']);
            if (!$newStatus) {
                $updateTaskDataValidatorResponse->setIsValid(false);
                $updateTaskDataValidatorResponse->setMessage('Invalid status value.');
                return $updateTaskDataValidatorResponse;
            }
        }
 
        $existingTask = $this->mySqlTaskRepository->findById($data['id']);

        if (empty($existingTask)) {
            $updateTaskDataValidatorResponse->setIsValid(false);
            $updateTaskDataValidatorResponse->setMessage('Task not found.');
            return $updateTaskDataValidatorResponse;
        }

        $currentStatus = $existingTask->getStatus();

        //Business rules of the technical test: a completed task cannot transition to another status.
        if (!empty($data['status']) && $currentStatus === Status::Completed && $newStatus !== Status::Completed) {
            $updateTaskDataValidatorResponse->setIsValid(false);
            $updateTaskDataValidatorResponse->setMessage('A completed task cannot change to another status.');
            return $updateTaskDataValidatorResponse;
        }

        //Business rules of the technical test: the status change has to follow the flow: pending -> in_progress -> completed
        $validTransitions = [
            Status::Pending->value     => [Status::InProgress->value],
            Status::InProgress->value => [Status::Completed->value],
            Status::Completed->value   => [] 
        ];

        if (!empty($data['status']) && !in_array($newStatus->value, $validTransitions[$currentStatus->value])) {
            $updateTaskDataValidatorResponse->setIsValid(false);
            $updateTaskDataValidatorResponse->setMessage("Invalid status transition: The current task status is: ". $currentStatus->value. " and you want to change status to ".$newStatus->value .". Operation not allowed. The task has to follow the following schema: pending -> in_progress -> completed");
            return $updateTaskDataValidatorResponse;
        }

        return $updateTaskDataValidatorResponse;
    }
}
