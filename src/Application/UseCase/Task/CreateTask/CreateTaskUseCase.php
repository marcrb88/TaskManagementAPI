<?php

namespace App\Application\UseCase\Task\CreateTask;

use App\Infrastructure\Repository\MySqlTaskRepository;
use App\Application\UseCase\Task\CreateTask\CreateTaskResponse;
use App\Domain\Model\Task;
use App\Domain\Repository\TaskRepositoryInterface;
use App\Domain\Repository\UserRepositoryInterface;
use App\Infrastructure\Repository\MySqlUserRepository;
use Symfony\Component\HttpFoundation\Response;
use Exception;

class CreateTaskUseCase
{
    private TaskRepositoryInterface $taskRepositoryInterface;
    private UserRepositoryInterface $userRepositoryInterface;

    public function __construct
    (
        TaskRepositoryInterface $taskRepositoryInterface,
        UserRepositoryInterface $userRepositoryInterface
    )
    {
        $this->taskRepositoryInterface = $taskRepositoryInterface;
        $this->userRepositoryInterface = $userRepositoryInterface;
    }

    public function execute(CreateTaskRequest $createTaskRequest): CreateTaskResponse
    {   
        $createTaskResponse = new CreateTaskResponse('Task created successfully');
        $createTaskResponse->setCodeStatus(Response::HTTP_CREATED);
        
        $task = new Task();
        $task->setTitle($createTaskRequest->getTitle())
            ->setDescription($createTaskRequest->getDescription())
            ->setStatus($createTaskRequest->getStatus())
            ->setPriority($createTaskRequest->getPriority())
            ->setDueDate($createTaskRequest->getDueDate())
            ->setCreatedAt($createTaskRequest->getCreatedAt())
            ->setUpdatedAt($createTaskRequest->getUpdatedAt());
            
        if ($createTaskRequest->getAssignedTo()) {
            $user = $this->userRepositoryInterface->findById($createTaskRequest->getAssignedTo());
            if (empty($user)) {
                $createTaskResponse->setCodeStatus(Response::HTTP_NOT_FOUND);
                $createTaskResponse->setMessage('User not found.');
                return $createTaskResponse;
            }

            $task->setAssignedTo($user);
        }

        try {
            $this->taskRepositoryInterface->save($task);
        } catch (Exception $e) {
            $createTaskResponse->setCodeStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
            $createTaskResponse->setMessage(
            'Error creating task (MySQL code ' . $e->getCode() . '): ' . $e->getMessage());
            return $createTaskResponse;
        }

        $createTaskResponse->setTask($task);

        //Return DTO response
        return $createTaskResponse;
    }
}