<?php

namespace App\Application\UseCase\User\CreateUser;

use DateTime;

class CreateUserRequest
{
    private string $name;
    private string $email;
    private DateTime $createdAt;

    public function __construct
    (
        string $name,
        string $email
    )
    {
        $this->name = $name;
        $this->email = $email;
        $this->createdAt = new DateTime();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
