<?php

namespace App\Application\UseCase\Task\ListTask;

use App\Domain\Repository\SerializeDtoAbstract;
use DateTime;
use App\Domain\ValueObject\Status;
use App\Domain\ValueObject\Priority;
    
class CreateFilterRequest extends SerializeDtoAbstract
{
    private ?Status $status = null;
    private ?Priority $priority = null;
    private ?string $assignedTo = null;
    public ?DateTime $dueDateFrom = null;
    public ?DateTime $dueDateTo = null;
    private ?DateTime $createdAtFrom = null;
    private ?DateTime $createdAtTo = null;


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

    public function getDueDateFrom(): ?DateTime
    {
        return $this->dueDateFrom;
    }

    public function setDueDateFrom(?DateTime $dueDateFrom): self
    {
        $this->dueDateFrom = $dueDateFrom;

        return $this;
    }

    public function getDueDateTo(): ?DateTime
    {
        return $this->dueDateTo;
    }

    public function setDueDateTo(?DateTime $dueDateTo): self
    {
        $this->dueDateTo= $dueDateTo;

        return $this;
    }

    public function getCreatedAtFrom(): ?DateTime
    {
        return $this->createdAtFrom;
    }

    public function setCreatedAtFrom(?DateTime $createdAtFrom = null): self
    {
        $this->createdAtFrom = $createdAtFrom;

        return $this;
    }

    public function getCreatedAtTo(): ?DateTime
    {
        return $this->createdAtTo;
    }

    public function setCreatedAtTo(?DateTime $createdAtTo = null): self
    {
        $this->createdAtTo = $createdAtTo;

        return $this;
    }
}
