<?php

namespace App\Application\UseCase\Task\CreateTask;

use App\Application\UseCase\Response\BaseResponse;
use App\Domain\Model\Task;

class CreateTaskResponse extends BaseResponse
{
    private ?Task $task = null;
    private int $codeStatus;

    public function __construct(string $message)
    {
        parent::__construct($message);
    }

    public function getTask(): ?Task
    {
        return $this->task;
    }
    public function setTask(?Task $task = null): self
    {
        $this->task = $task;

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
    public function getMessage(): string
    {
        return $this->message;
    }
    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }
}