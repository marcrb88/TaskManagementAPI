<?php

namespace App\Application\UseCase\Task\GetTaskDetail;


class GetTaskDetailRequest
{
    private string $id;

    public function __construct
    (
        string $id
    )
    {
        $this->id = $id;
    }

    public function getId(): string
    {
        return $this->id;
    }
    public function setId(string $id): self
    {
        $this->id = $id;
        
        return $this;
    }
}
