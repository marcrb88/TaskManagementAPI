<?php

namespace App\Application\UseCase\Task\DeleteTask;


class DeleteTaskRequest
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
}
