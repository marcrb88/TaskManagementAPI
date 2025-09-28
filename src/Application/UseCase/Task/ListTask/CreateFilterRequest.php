<?php

namespace App\Application\UseCase\Task\ListTask;

use App\Domain\Repository\SerializeDtoAbstract;
use DateTime;
use App\Domain\ValueObject\Status;
use App\Domain\ValueObject\Priority;
    
class CreateFilterRequest extends SerializeDtoAbstract
{
    private ?string $id = null;
    private ?Status $status = null;
    private ?Priority $priority = null;
    private ?string $assignedTo = null;
    public ?DateTime $dueDate = null;
    private ?DateTime $createdAt = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(?string $id = null): self
    {
        $this->id = $id;

        return $this;
    }

    public function getStatus(): ?Status
    {
        return $this->status;
    }

    public function setStatus(?Status $status = null): self
    {
        $this->status = $status;
        
        return $this;
    }

    public function getPriority(): ?Priority
    {
        return $this->priority;
    }

    public function setPriority(?Priority $priority = null): self
    {
        $this->priority = $priority;

        return $this;
    }

    public function getAssignedTo(): ?string
    {
        return $this->assignedTo;
    }
    public function setAssignedTo(?string $assignedTo): self
    {
        $this->assignedTo = $assignedTo;

        return $this;
    }

    public function getDueDate(): ?DateTime
    {
        return $this->dueDate;
    }

    public function setDueDate(?DateTime $dueDate): self
    {
        $this->dueDate = $dueDate;

        return $this;
    }

    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?DateTime $createdAt = null): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
