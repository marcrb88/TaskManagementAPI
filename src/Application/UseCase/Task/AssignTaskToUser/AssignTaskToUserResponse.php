<?php

namespace App\Application\UseCase\Task\AssignTaskToUser;
use App\Application\Response\BaseResponse;

class AssignTaskToUserResponse extends BaseResponse
{
    private int $codeStatus;

    public function __construct(string $message)
    {
        parent::__construct($message);
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

