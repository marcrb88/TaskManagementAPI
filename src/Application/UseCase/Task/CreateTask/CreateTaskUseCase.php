<?php

namespace App\Application\UseCase\Task\CreateTask;

use App\Infrastructure\Repository\MySqlTaskRepository;
use App\Application\UseCase\Task\CreateTask\CreateTaskRequest;
use App\Application\UseCase\Task\CreateTask\CreateTaskResponse;
use App\Domain\Model\Task;
use Symfony\Component\HttpFoundation\Response;
use Exception;

class CreateTaskUseCase
{
    private MySqlTaskRepository $mySqlTaskRepository;

    public function __construct
    (
        MySqlTaskRepository $mySqlTaskRepository
    )
    {
        $this->mySqlTaskRepository = $mySqlTaskRepository;
    }

    public function execute(CreateTaskRequest $request): CreateTaskResponse
    {   
        $createTaskResponse = new CreateTaskResponse();
        $createTaskResponse->setCodeStatus(Response::HTTP_CREATED);
        $createTaskResponse->setMessage('Task created successfully');
        
        $task = new Task(
            $request->getTitle(),
            $request->getDescription(),
            $request->getDueDate()
        );

        //Check if the user exists before saving the task
        if ($request->getAssignedTo()) {
            $user = $this->mySqlTaskRepository->findById($request->getAssignedTo());
            if (!$user) {
                $createTaskResponse->setCodeStatus(Response::HTTP_BAD_REQUEST);
                $createTaskResponse->setMessage('Assigned user does not exist');
                return $createTaskResponse;
            }
            $task->setAssignedTo($request->getAssignedTo());
        }

        try {
            $this->mySqlTaskRepository->save($task);
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