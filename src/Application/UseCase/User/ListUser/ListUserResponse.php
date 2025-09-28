<?php

namespace App\Application\UseCase\User\ListUser;

use App\Application\Response\BaseResponse;

class ListUserResponse extends BaseResponse
{
    private array $users = [];
    private int $codeStatus;

    public function __construct(string $message)
    {
        parent::__construct($message);
    }

    public function getUsers(): array
    {
        return $this->users;
    }
    public function setUsers(array $users): self
    {
        $this->users = $users;

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