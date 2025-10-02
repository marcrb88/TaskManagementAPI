<?php

namespace App\Tests\Application\UseCase\Task\UpdateTask;

use App\Application\UseCase\Task\UpdateTask\UpdateTaskRequest;
use App\Application\UseCase\Task\UpdateTask\UpdateTaskUseCase;
use App\Domain\Model\Task;
use App\Domain\Repository\TaskRepositoryInterface;
use App\Domain\ValueObject\Status;
use App\Domain\ValueObject\Priority;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;
use PHPUnit\Framework\MockObject\MockObject;

class UpdateTaskUseCaseTest extends TestCase
{
    /** @var TaskRepositoryInterface&MockObject */
    private $taskRepositoryInterface;
    private UpdateTaskUseCase $useCase;


    protected function setUp(): void
    {
        $this->taskRepositoryInterface = $this->createMock(TaskRepositoryInterface::class);
        $this->useCase = new UpdateTaskUseCase($this->taskRepositoryInterface);
    }

    /**
     * Test update a non existing task scenario.
     */
    public function testUpdateNonExistingTask(): void
    {
        $this->taskRepositoryInterface->method('findById')->willReturn(null);

        $request = (new UpdateTaskRequest())
            ->setId("task-uuid")
            ->setTitle("Migrate hotel room images to image-manager service");

        $response = $this->useCase->execute($request);

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getCodeStatus());
        $this->assertEquals('Task not found.', $response->getMessage());
    }

    /**
     * Test a completed task that cannot change to another status scenario.
     */
    public function testCompletedTaskCannotChangeStatus(): void
    {
        $existingTask = (new Task())
            ->setId('task-uuid')
            ->setTitle("Implement AWS lambda integration")
            ->setDescription("Retrieve hotel info from Booking and store in S3")
            ->setStatus(Status::Completed);

        $this->taskRepositoryInterface->method('findById')->willReturn($existingTask);

        $request = (new UpdateTaskRequest())
            ->setId($existingTask->getId())
            ->setStatus(Status::InProgress);

        $response = $this->useCase->execute($request);

        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getCodeStatus());
        $this->assertEquals('A completed task cannot change to another status.', $response->getMessage());
    }

    /**
     * Test change status invalid transition scenario.
     */
    public function testInvalidStatusTransition(): void
    {
        $existingTask = (new Task())
            ->setId('task-uuid')
            ->setTitle("Prepare skeleton for AI integrations")
            ->setDescription("Prepare a structure to easily add AI API integrations")
            ->setStatus(Status::Pending);

        $this->taskRepositoryInterface->method('findById')->willReturn($existingTask);

        $request = (new UpdateTaskRequest())
            ->setId($existingTask->getId())
            ->setStatus(Status::Completed);

        $response = $this->useCase->execute($request);

        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getCodeStatus());
        $this->assertStringContainsString('Invalid status transition', $response->getMessage());
    }

    /**
     * Test successfully task update scenario.
     */
    public function testSuccessfulUpdate(): void
    {
        $existingTask = (new Task())
            ->setId('task-uuid')
            ->setTitle("Prepare infra to save user searches")
            ->setDescription("Save destinations that clients search in Amimir.com site")
            ->setStatus(Status::Pending)
            ->setPriority(Priority::Low);

        $this->taskRepositoryInterface->method('findById')->willReturn($existingTask);

        $this->taskRepositoryInterface
            ->expects($this->once())
            ->method('save');

        $request = (new UpdateTaskRequest())
            ->setId($existingTask->getId())
            ->setTitle("Updated task")
            ->setStatus(Status::InProgress);

        $response = $this->useCase->execute($request);

        $this->assertEquals(Response::HTTP_OK, $response->getCodeStatus());
        $this->assertEquals('Task updated successfully', $response->getMessage());

        $this->assertEquals("Updated task", $existingTask->getTitle());
        $this->assertEquals(Status::InProgress, $existingTask->getStatus());
    }

    /**
     * Test exception thrown during save task.
     */
    public function testRepositoryThrowsException(): void
    {
        $existingTask = (new Task())
            ->setId('task-uuid')
            ->setTitle("Title 1")
            ->setDescription("Description 1")
            ->setStatus(Status::Pending);

        $this->taskRepositoryInterface->method('findById')->willReturn($existingTask);

        $this->taskRepositoryInterface
            ->method('save')
            ->will($this->throwException(new \Exception('Database error', Response::HTTP_INTERNAL_SERVER_ERROR)));

        $request = (new UpdateTaskRequest())
            ->setId($existingTask->getId())
            ->setTitle("Title 2");

        $response = $this->useCase->execute($request);

        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getCodeStatus());
    }
}
