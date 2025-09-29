<?php

namespace App\Application\UseCase\Task\AssignTaskToUser;

use App\Infrastructure\Repository\MySqlTaskRepository;
use App\Infrastructure\Repository\MySqlUserRepository;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class AssignTaskToUserUseCase
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

   public function execute(AssignTaskToUserRequest $assignTaskToUserRequest): AssignTaskToUserResponse
   {
        $assignTaskToUserResponse = new AssignTaskToUserResponse('Task assigned to user succesfully');
        $assignTaskToUserResponse->setCodeStatus(Response::HTTP_OK);

        try {

            $task = $this->taskRepository->findById($assignTaskToUserRequest->getId());

            if (empty($task)) {
                $assignTaskToUserResponse->setMessage('No task found to assign to user');
                $assignTaskToUserResponse->setCodeStatus(Response::HTTP_NOT_FOUND);
                return $assignTaskToUserResponse;
            }

            $user = $this->userRepository->findById($assignTaskToUserRequest->getAssignedToId());

            if (empty($user)) {
                $assignTaskToUserResponse->setMessage('No user found to be assigned to task');
                $assignTaskToUserResponse->setCodeStatus(Response::HTTP_NOT_FOUND);
                return $assignTaskToUserResponse;
            }

            $task->setAssignedTo($user);

            $this->taskRepository->save($task);
            
        } catch (Throwable $e) {
            $assignTaskToUserResponse->setCodeStatus($e->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
            $assignTaskToUserResponse->setMessage('Error assigning user to task: ' . $e->getMessage());
            return $assignTaskToUserResponse;
        }

        return $assignTaskToUserResponse;
    }
    
}
