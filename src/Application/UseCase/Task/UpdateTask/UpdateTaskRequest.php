<?php

namespace App\Application\UseCase\Task\UpdateTask;

use DateTime;
use App\Domain\ValueObject\Status;
use App\Domain\ValueObject\Priority;

class UpdateTaskRequest
{
    private ?string $id = null;
    private ?string $title = null;
    private ?string $description = null;
    private ?Status $status = null;
    private ?Priority $priority = null;
    private ?string $assignedTo = null;
    private ?DateTime $dueDate = null;
    private ?DateTime $createdAt = null;
    private ?DateTime $updatedAt = null;

    public function __construct()
    {
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(?string $id = null): self
    {
        $this->id = $id;
        
        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }
    public function setTitle(?string $title = null): self
    {
        $this->title = $title;

        return $this;
    }
    public function getDescription(): ?string
    {
        return $this->description;
    }
    public function setDescription(?string $description = null): self
    {
        $this->description = $description;

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
    public function setAssignedTo(?string $assignedTo = null): self
    {
        $this->assignedTo = $assignedTo;

        return $this;
    }
    public function getDueDate(): ?DateTime
    {
        return $this->dueDate;
    }
    public function setDueDate(?DateTime $dueDate = null): self
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
    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt;
    }
    public function setUpdatedAt(?DateTime $updatedAt = null): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
