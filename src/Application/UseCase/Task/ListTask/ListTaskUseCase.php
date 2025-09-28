<?php

namespace App\Application\UseCase\Task\ListTask;

use App\Infrastructure\Repository\MySqlTaskRepository;
use Symfony\Component\HttpFoundation\Response;
use Throwable;
use App\Application\UseCase\Task\ListTask\CreateFilterRequest;
use App\Application\UseCase\Task\ListTask\ListTaskResponse;

class ListTaskUseCase
{
    private MySqlTaskRepository $taskRepository;

    public function __construct
    (
        MySqlTaskRepository $taskRepository
    )
    {
        $this->taskRepository = $taskRepository;
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
                $tasks = $this->taskRepository->findByFilters($filters);
            } else {
                $tasks = $this->taskRepository->findAll();
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
