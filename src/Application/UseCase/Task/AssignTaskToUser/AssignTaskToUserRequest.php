<?php

namespace App\Application\UseCase\Task\AssignTaskToUser;


class AssignTaskToUserRequest
{
    private string $id;
    private string $assignedToId;

    public function __construct
    (
        string $id,
        string $assignedToId
    )
    {
        $this->id = $id;
        $this->assignedToId = $assignedToId;
    }

    public function getId(): string 
    {
        return $this->id;
    }

    public function getAssignedToId(): string
    {
        return $this->assignedToId;
    }
}
