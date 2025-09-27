<?php

namespace App\Infrastructure\Controller;

use App\Application\UseCase\Task\CreateTask\CreateTaskUseCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use DateTime;
use App\Application\UseCase\Task\CreateTask\CreateTaskRequest;


class CreateTaskController
{
    private CreateTaskUseCase $createTaskUseCase;

    public function __construct
    (
         CreateTaskUseCase $createTaskUseCase

    )
    {
        $this->createTaskUseCase = $createTaskUseCase;
    }
    
    #[Route('/api/tasks', methods: ['POST'])]
    public function createTask(Request $request): JsonResponse
    {
        //Recieve data from POST request
        $data = json_decode($request->getContent(), true);

        $title = $data['title'] ?? null;
        $description = $data['description'] ?? null;
        $dueDate = $data['dueDate'] ?? null;
        $status = $data['status'] ?? null;
        $priority = $data['priority'] ?? null;
        $assignedTo = $data['assignedTo'] ?? null;
        $createdAt = $data['createdAt'] ?? null;

        //Validate data
        if (empty($title) || empty($description) || empty($dueDate)) {
            return new JsonResponse(['error' => 'Incomplete data'], Response::HTTP_BAD_REQUEST);
        }

        //Build the Task Request DTO
        $createTaskRequest = new CreateTaskRequest(
            $title,
            $description,
            new DateTime($dueDate)
        );
        
        if (!empty($status)) {
            $createTaskRequest->setStatus($status);
        }
        if (!empty($priority)) {
            $createTaskRequest->setPriority($priority);
        }
        if (!empty($assignedTo)) {
            $createTaskRequest->setAssignedTo($assignedTo);
        }
        if (!empty($createdAt)) {
            $createTaskRequest->setCreatedAt(new DateTime($createdAt));
        }

        $createTaskResponse = $this->createTaskUseCase->execute($createTaskRequest);

        return new JsonResponse(
            [
                'id' => $createTaskResponse->getTask()->getId(),
                'message' => $createTaskResponse->getMessage()
            ],
            $createTaskResponse->getCodeStatus()
        );
    }

}