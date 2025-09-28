<?php

namespace App\Application\UseCase\Task\ListTask;
    
use App\Application\Response\BaseResponse;

class ListTaskResponse extends BaseResponse
{
    private array $tasks = [];
    private int $codeStatus;

    public function __construct(string $message)
    {
        parent::__construct($message);
    }

    public function getTasks(): array
    {
        return $this->tasks;
    }
    public function setTasks(array $tasks): self
    {
        $this->tasks = $tasks;

        return $this;
    }
    public function getCodeStatus(): int
    {
        return $this->codeStatus;
    }
    public function setCodeStatus(int $codeStatus): self
    {
        $this->codeStatus = $codeStatus;

        return $this;
    }
}
