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

    public function execute(CreateTaskRequest $request): CreateTaskResponse
    {   
        $createTaskResponse = new CreateTaskResponse('Task created successfully');
        $createTaskResponse->setCodeStatus(Response::HTTP_CREATED);
        
        $task = new Task(
            $request->getTitle(),
            $request->getDescription()
        );

        if ($request->getAssignedTo()) {
            $user = $this->userRepository->findById($request->getAssignedTo());
            if (empty($user)) {
                $createTaskResponse->setCodeStatus(Response::HTTP_NOT_FOUND);
                $createTaskResponse->setMessage('User not found.');
                return $createTaskResponse;
            }

            $task->setAssignedTo($user);
        }

<<<<<<< Updated upstream
        $task->setStatus($request->getStatus());
        $task->setPriority($request->getPriority());
        $task->setDueDate($request->getDueDate());
        $task->setCreatedAt($request->getCreatedAt());
        $task->setUpdatedAt($request->getUpdatedAt());


=======
>>>>>>> Stashed changes
        try {
            $this->taskRepository->save($task);
        } catch (Exception $e) {
            $createTaskResponse->setCodeStatus($e->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
            $createTaskResponse->setMessage('Error creating task: ' . $e->getMessage());
            return $createTaskResponse;
        }

        $createTaskResponse->setTask($task);

        //Return DTO response
        return $createTaskResponse;
    }
}