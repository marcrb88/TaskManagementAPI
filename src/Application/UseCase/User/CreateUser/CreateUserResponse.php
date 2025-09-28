<?php

namespace App\Application\UseCase\User\CreateUser;

use App\Application\Response\BaseResponse;
use App\Domain\Model\User;

class CreateUserResponse extends BaseResponse
{
    private ?User $user = null;
    private int $codeStatus;

    public function __construct(string $message)
    {
        parent::__construct($message);
    }

    public function getUser(): ?User
    {
        return $this->user;
    }
    public function setUser(?User $user = null): self
    {
        $this->user = $user;

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