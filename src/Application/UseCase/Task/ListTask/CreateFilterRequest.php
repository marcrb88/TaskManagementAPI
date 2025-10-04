<?php

namespace App\Application\UseCase\Task\ListTask;

use App\Domain\Repository\SerializeDtoAbstract;
use DateTime;
use App\Domain\ValueObject\Status;
use App\Domain\ValueObject\Priority;

class CreateFilterRequest extends SerializeDtoAbstract
{
    private ?string $title = null;
    private ?string $description = null;
    private ?Status $status = null;
    private ?Priority $priority = null;
    public ?DateTime $dueDateFrom = null;
    public ?DateTime $dueDateTo = null;
    private ?DateTime $createdAtFrom = null;
    private ?DateTime $createdAtTo = null;
    private ?DateTime $updatedAtFrom = null;
    private ?DateTime $updatedAtTo = null;

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title = null): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): string
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

    public function getUpdatedAtFrom(): ?DateTime
    {
        return $this->updatedAtFrom;
    }

    public function setUpdatedAtFrom(?DateTime $updatedAtFrom = null): self
    {
        $this->updatedAtFrom = $updatedAtFrom;

        return $this;
    }

    public function getUpdatedAtTo(): ?DateTime
    {
        return $this->updatedAtTo;
    }

    public function setUpdatedAtTo(?DateTime $updatedAtTo = null): self
    {
        $this->updatedAtTo = $updatedAtTo;

        return $this;
    }
}
