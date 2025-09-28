<?php

namespace App\Application\UseCase\Task\GetTaskDetail;
    
use App\Application\Response\BaseResponse;
use App\Domain\Model\Task;

class GetTaskDetailResponse extends BaseResponse
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
}
