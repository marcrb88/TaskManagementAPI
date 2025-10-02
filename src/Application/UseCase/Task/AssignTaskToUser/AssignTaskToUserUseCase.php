<?php

namespace App\Application\UseCase\Task\AssignTaskToUser;

use App\Domain\Repository\TaskRepositoryInterface;
use App\Domain\Repository\UserRepositoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class AssignTaskToUserUseCase
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

   public function execute(AssignTaskToUserRequest $assignTaskToUserRequest): AssignTaskToUserResponse
   {
        $assignTaskToUserResponse = new AssignTaskToUserResponse('Task assigned to user succesfully');
        $assignTaskToUserResponse->setCodeStatus(Response::HTTP_OK);

        try {

            $task = $this->taskRepositoryInterface->findById($assignTaskToUserRequest->getId());

            if (empty($task)) {
                $assignTaskToUserResponse->setMessage('No task found to assign to user');
                $assignTaskToUserResponse->setCodeStatus(Response::HTTP_NOT_FOUND);
                return $assignTaskToUserResponse;
            }

            $user = $this->userRepositoryInterface->findById($assignTaskToUserRequest->getAssignedToId());

            if (empty($user)) {
                $assignTaskToUserResponse->setMessage('No user found to be assigned to task');
                $assignTaskToUserResponse->setCodeStatus(Response::HTTP_NOT_FOUND);
                return $assignTaskToUserResponse;
            }

            $task->setAssignedTo($user);

            $this->taskRepositoryInterface->save($task);
            
        } catch (Throwable $e) {
            $assignTaskToUserResponse->setCodeStatus($e->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
            $assignTaskToUserResponse->setMessage('Error assigning user to task: ' . $e->getMessage());
            return $assignTaskToUserResponse;
        }

        return $assignTaskToUserResponse;
    }
    
}
