<?php

namespace App\Application\UseCase\Task\CreateTask;

use DateTime;
use App\Domain\ValueObject\Status;
use App\Domain\ValueObject\Priority;

class CreateTaskRequest
{
    public string $title;
    public string $description;
    private Status $status = Status::Pending;
    private Priority $priority = Priority::Low;
    private ?string $assignedTo = null;
    public DateTime $dueDate;
    private DateTime $createdAt;
    private DateTime $updatedAt;

    public function __construct
    (
        string $title,
        string $description,
        DateTime $dueDate
    )
    {
        $this->title = $title;
        $this->description = $description;
        $this->dueDate = $dueDate;
    }

    public function getTitle(): string
    {
        return $this->title;
    }
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }
    public function getDescription(): string
    {
        return $this->description;
    }
    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }
    public function getStatus(): Status
    {
        return $this->status;
    }
    public function setStatus(Status $status): self
    {
        $this->status = $status;
        
        return $this;
    }
    public function getPriority(): Priority
    {
        return $this->priority;
    }
    public function setPriority(Priority $priority): self
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
    public function getDueDate(): DateTime
    {
        return $this->dueDate;
    }
    public function setDueDate(DateTime $dueDate): self
    {
        $this->dueDate = $dueDate;

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
    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }
    public function setUpdatedAt(DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
