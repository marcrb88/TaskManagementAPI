<?php

namespace App\Application\UseCase\Task\GetTaskDetail;

use App\Infrastructure\Repository\MySqlTaskRepository;
use App\Application\UseCase\Task\GetTaskDetail\GetTaskDetailResponse;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class GetTaskDetailUseCase
{
    private MySqlTaskRepository $taskRepository;

    public function __construct
    (
        MySqlTaskRepository $taskRepository
    )
    {
        $this->taskRepository = $taskRepository;
        
    }
   public function execute(GetTaskDetailRequest $getTaskDetailRequest): GetTaskDetailResponse
   {
        $getTaskDetailResponse = new GetTaskDetailResponse('Task details obtained successfully');
        $getTaskDetailResponse->setCodeStatus(Response::HTTP_OK);

        try {
            $task = $this->taskRepository->findById($getTaskDetailRequest->getId());
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
