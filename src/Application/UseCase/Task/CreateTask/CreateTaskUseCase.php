<?php

namespace App\Application\UseCase\Task\CreateTask;

use App\Infrastructure\Repository\MySqlTaskRepository;
use App\Application\UseCase\Task\CreateTask\CreateTaskResponse;
use App\Domain\Model\Task;
use App\Infrastructure\Repository\MySqlUserRepository;
use Symfony\Component\HttpFoundation\Response;
use Exception;

class CreateTaskUseCase
{
    private MySqlTaskRepository $taskRepository;
    private MySqlUserRepository $userRepository;

    public function __construct
    (
        MySqlTaskRepository $taskRepository,
        MySqlUserRepository $userRepository
    )
    {
        $this->taskRepository = $taskRepository;
        $this->userRepository = $userRepository;
    }

    public function execute(CreateUpdateTaskRequest $request): CreateTaskResponse
    {   
        $createTaskResponse = new CreateTaskResponse('Task created successfully');
        $createTaskResponse->setCodeStatus(Response::HTTP_CREATED);
        
        $task = new Task();
        $task->setTitle($request->getTitle())
            ->setDescription($request->getDescription())
            ->setStatus($request->getStatus())
            ->setPriority($request->getPriority())
            ->setDueDate($request->getDueDate())
            ->setCreatedAt($request->getCreatedAt())
            ->setUpdatedAt($request->getUpdatedAt());
            
        if ($request->getAssignedTo()) {
            $user = $this->userRepository->findById($request->getAssignedTo());
            if (empty($user)) {
                $createTaskResponse->setCodeStatus(Response::HTTP_NOT_FOUND);
                $createTaskResponse->setMessage('User not found.');
                return $createTaskResponse;
            }

            $task->setAssignedTo($user);
        }

        try {
            $this->taskRepository->save($task);
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