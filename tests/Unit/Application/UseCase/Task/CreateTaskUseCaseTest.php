<?php

namespace App\Tests\Unit\Application\UseCase\Task;

use App\Application\UseCase\Task\CreateTask\CreateTaskUseCase;
use App\Application\UseCase\Task\CreateTask\CreateTaskRequest;
use App\Domain\Model\Task;
use App\Domain\Model\User;
use App\Domain\Repository\TaskRepositoryInterface;
use App\Domain\Repository\UserRepositoryInterface;
use App\Domain\ValueObject\Priority;
use App\Domain\ValueObject\Status;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;
use PHPUnit\Framework\MockObject\MockObject;

class CreateTaskUseCaseTest extends TestCase
{
    /** @var TaskRepositoryInterface&MockObject */
    private $taskRepositoryInterface;
    /** @var UserRepositoryInterface&MockObject */
    private $userRepositoryInterface;

    protected function setUp(): void
    {
        $this->taskRepositoryInterface = $this->createMock(TaskRepositoryInterface::class);
        $this->userRepositoryInterface = $this->createMock(UserRepositoryInterface::class);
    }

    /**
     * Test task creation without user assigned successfully scenario.
     */
    public function testCreateTaskSuccessfullyWithoutAssignedUser(): void
    {
        /** @var CreateTaskRequest&MockObject */
        $request = $this->createMock(CreateTaskRequest::class);
        $request->method('getTitle')->willReturn('Test Task');
        $request->method('getDescription')->willReturn('Task description');
        $request->method('getStatus')->willReturn(Status::Pending);
        $request->method('getPriority')->willReturn(Priority::Low);
        $request->method('getDueDate')->willReturn(null);
        $request->method('getCreatedAt')->willReturn(new \DateTime());
        $request->method('getUpdatedAt')->willReturn(new \DateTime());
        $request->method('getAssignedTo')->willReturn(null);

        $this->taskRepositoryInterface
            ->expects($this->once())
            ->method('save');

        $useCase = new CreateTaskUseCase($this->taskRepositoryInterface, $this->userRepositoryInterface);
        $response = $useCase->execute($request);

        $this->assertEquals('Task created successfully', $response->getMessage());
        $this->assertEquals(Response::HTTP_CREATED, $response->getCodeStatus());
        $this->assertInstanceOf(Task::class, $response->getTask());
    }

    /**
     * Test task creation with assigned user scenario.
     */
    public function testCreateTaskWithAssignedUser(): void
    {
        $userId = 'user-uuid';
        $user = $this->createMock(User::class);

        /** @var CreateTaskRequest&MockObject */
        $request = $this->createMock(CreateTaskRequest::class);
        $request->method('getTitle')->willReturn('Test Task');
        $request->method('getDescription')->willReturn('Task description');
        $request->method('getStatus')->willReturn(Status::Pending);
        $request->method('getPriority')->willReturn(Priority::High);
        $request->method('getDueDate')->willReturn(null);
        $request->method('getCreatedAt')->willReturn(new \DateTime());
        $request->method('getUpdatedAt')->willReturn(new \DateTime());
        $request->method('getAssignedTo')->willReturn($userId);

        $this->userRepositoryInterface
            ->expects($this->once())
            ->method('findById')
            ->with($userId)
            ->willReturn($user);

        $this->taskRepositoryInterface
            ->expects($this->once())
            ->method('save');

        $useCase = new CreateTaskUseCase($this->taskRepositoryInterface, $this->userRepositoryInterface);
        $response = $useCase->execute($request);

        $this->assertEquals('Task created successfully', $response->getMessage());
        $this->assertEquals(Response::HTTP_CREATED, $response->getCodeStatus());
        $this->assertInstanceOf(Task::class, $response->getTask());
    }

    /**
     * Test task creation with a non-existing user scenario.
     */
    public function testCreateTaskFailsIfUserNotFound(): void
    {
        $userId = 'user-uuid';

        /** @var CreateTaskRequest&MockObject */
        $request = $this->createMock(CreateTaskRequest::class);
        $request->method('getTitle')->willReturn('Test Task');
        $request->method('getDescription')->willReturn('Task description');
        $request->method('getStatus')->willReturn(Status::InProgress);
        $request->method('getPriority')->willReturn(Priority::Low);
        $request->method('getDueDate')->willReturn(null);
        $request->method('getCreatedAt')->willReturn(new \DateTime());
        $request->method('getUpdatedAt')->willReturn(new \DateTime());
        $request->method('getAssignedTo')->willReturn($userId);

        $this->userRepositoryInterface
            ->expects($this->once())
            ->method('findById')
            ->with($userId)
            ->willReturn(null);

        $this->taskRepositoryInterface
            ->expects($this->never())
            ->method('save');

        $useCase = new CreateTaskUseCase($this->taskRepositoryInterface, $this->userRepositoryInterface);
        $response = $useCase->execute($request);

        $this->assertEquals('User not found.', $response->getMessage());
        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getCodeStatus());
    }

    /**
     * Test exception thrown during task saving scenario.
     */
    public function testExceptionDuringTaskCreation(): void
    {
        /** @var CreateTaskRequest&MockObject */
        $request = $this->createMock(CreateTaskRequest::class);
        $request->method('getTitle')->willReturn('Test Task');
        $request->method('getDescription')->willReturn('Task description');
        $request->method('getStatus')->willReturn(Status::InProgress);
        $request->method('getPriority')->willReturn(Priority::Medium);
        $request->method('getDueDate')->willReturn(null);
        $request->method('getCreatedAt')->willReturn(new \DateTime());
        $request->method('getUpdatedAt')->willReturn(new \DateTime());
        $request->method('getAssignedTo')->willReturn(null);

        $this->taskRepositoryInterface
            ->method('save')
            ->will($this->throwException(new \Exception('Database error', Response::HTTP_INTERNAL_SERVER_ERROR)));

        $useCase = new CreateTaskUseCase($this->taskRepositoryInterface, $this->userRepositoryInterface);
        $response = $useCase->execute($request);

        $this->assertStringContainsString('Error creating task', $response->getMessage());
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getCodeStatus());
    }
}
