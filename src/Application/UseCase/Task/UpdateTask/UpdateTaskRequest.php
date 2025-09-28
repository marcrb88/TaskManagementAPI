<?php

namespace App\Application\UseCase\Task\UpdateTask;

use App\Domain\Model\Task;

class UpdateTaskRequest
{
    private Task $task;

    public function __construct
    (
        Task $task
    )
    {
        $this->task = $task;
    }

    public function getTask(): Task
    {
        return $this->task;
    }
    public function setTask(Task $task): self
    {
        $this->task = $task;
        
        return $this;
    }
}
