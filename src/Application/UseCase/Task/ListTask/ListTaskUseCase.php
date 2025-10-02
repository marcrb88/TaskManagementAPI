<?php

namespace App\Application\UseCase\Task\ListTask;

use Symfony\Component\HttpFoundation\Response;
use Throwable;
use App\Application\UseCase\Task\ListTask\CreateFilterRequest;
use App\Application\UseCase\Task\ListTask\ListTaskResponse;
use App\Domain\Repository\TaskRepositoryInterface;

class ListTaskUseCase
{
    private TaskRepositoryInterface $taskRepositoryInterface;

    public function __construct
    (
        TaskRepositoryInterface $taskRepositoryInterface
    )
    {
        $this->taskRepositoryInterface = $taskRepositoryInterface;
    }

    public function execute(CreateFilterRequest $filterRequest): ListTaskResponse
    {
        $listTaskResponse = new ListTaskResponse('Tasks obtained successfully');
        $listTaskResponse->setCodeStatus(Response::HTTP_OK);

        try {
            // Serialize filters request to an array and remove null values
            $filters = array_filter($filterRequest->toArray(), function($v) {
                return $v !== null;
            });
            // If there are filters, use them to find tasks, otherwise get all tasks
            if (!empty($filters)) {
                $tasks = $this->taskRepositoryInterface->findByFilters($filters);
            } else {
                $tasks = $this->taskRepositoryInterface->findAll();
            }
        } catch (Throwable $e) {
            $listTaskResponse->setCodeStatus($e->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
            $listTaskResponse->setMessage('Error obtaining tasks: ' . $e->getMessage());
            return $listTaskResponse;
        }

        if (empty($tasks)) {
            $listTaskResponse->setMessage('No tasks found');
            $listTaskResponse->setCodeStatus(Response::HTTP_NOT_FOUND);
            return $listTaskResponse;
        }

        $listTaskResponse->setTasks($tasks);

        //Return DTO response
        return $listTaskResponse;
    }
}
