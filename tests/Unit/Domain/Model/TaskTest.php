<?php

namespace App\Tests\Unit\Domain\Model;

use App\Domain\Model\Task;
use App\Domain\ValueObject\Status;
use App\Domain\ValueObject\Priority;
use DateTime;
use PHPUnit\Framework\TestCase;

class TaskTest extends TestCase
{
    public function testTaskIsCreatedWithDefaultValues(): void
    {
        $task = new Task();

        $this->assertInstanceOf(Task::class, $task);
        $this->assertEquals(Status::Pending, $task->getStatus(), 'Initial state must be Pending');
        $this->assertEquals(Priority::Low, $task->getPriority(), 'Initial priority must be Low');
        $this->assertInstanceOf(DateTime::class, $task->getCreatedAt(), 'CreatedAt must be initializated');
        $this->assertInstanceOf(DateTime::class, $task->getUpdatedAt(), 'UpdatedAt must be initializated');
        $this->assertNull($task->getAssignedTo(), 'AssignedTo must be null');
    }

    public function testSettersAndGettersWorkCorrectly(): void
    {
        $task = new Task();

        $title = 'New Task';
        $description = 'Test description';
        $dueDate = new DateTime('+1 day');
        $createdAt = new DateTime('-1 day');
        $updatedAt = new DateTime();

        $task->setTitle($title)
             ->setDescription($description)
             ->setStatus(Status::InProgress)
             ->setPriority(Priority::High)
             ->setDueDate($dueDate)
             ->setCreatedAt($createdAt)
             ->setUpdatedAt($updatedAt);

        $this->assertEquals($title, $task->getTitle());
        $this->assertEquals($description, $task->getDescription());
        $this->assertEquals(Status::InProgress, $task->getStatus());
        $this->assertEquals(Priority::High, $task->getPriority());
        $this->assertEquals($dueDate, $task->getDueDate());
        $this->assertEquals($createdAt, $task->getCreatedAt());
        $this->assertEquals($updatedAt, $task->getUpdatedAt());
    }
}
