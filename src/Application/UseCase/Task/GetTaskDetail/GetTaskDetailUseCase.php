<?php

namespace App\Application\UseCase\Task\GetTaskDetail;

use App\Application\UseCase\Task\GetTaskDetail\GetTaskDetailResponse;
use App\Domain\Repository\TaskRepositoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class GetTaskDetailUseCase
{
    private TaskRepositoryInterface $taskRepositoryInterface;

    public function __construct
    (
        TaskRepositoryInterface $taskRepositoryInterface
    )
    {
        $this->taskRepositoryInterface = $taskRepositoryInterface;
        
    }
   public function execute(GetTaskDetailRequest $getTaskDetailRequest): GetTaskDetailResponse
   {
        $getTaskDetailResponse = new GetTaskDetailResponse('Task details obtained successfully');
        $getTaskDetailResponse->setCodeStatus(Response::HTTP_OK);

        try {
            $task = $this->taskRepositoryInterface->findById($getTaskDetailRequest->getId());
        } catch (Throwable $e) {
            $getTaskDetailResponse->setCodeStatus($e->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
            $getTaskDetailResponse->setMessage('Error obtaining tasks: ' . $e->getMessage());
            return $getTaskDetailResponse;
        }

        if (empty($task)) {
            $getTaskDetailResponse->setMessage('No tasks found');
            $getTaskDetailResponse->setCodeStatus(Response::HTTP_NOT_FOUND);
            return $getTaskDetailResponse;
        }

        $getTaskDetailResponse->setTask($task);

        return $getTaskDetailResponse;
    }
}
