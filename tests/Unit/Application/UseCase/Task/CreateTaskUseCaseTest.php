<?php

namespace App\Tests\Unit\Application\UseCase\Task;

use App\Application\UseCase\Task\CreateTask\CreateTaskUseCase;
use App\Application\UseCase\Task\CreateTask\CreateTaskRequest;
use App\Infrastructure\Repository\MySqlTaskRepository;
use App\Infrastructure\Repository\MySqlUserRepository;
use App\Domain\Model\Task;
use App\Domain\Model\User;
use App\Domain\ValueObject\Priority;
use App\Domain\ValueObject\Status;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;

class CreateTaskUseCaseTest extends TestCase
{
    private $taskRepository;
    private $userRepository;

    protected function setUp(): void
    {
        $this->taskRepository = $this->createMock(MySqlTaskRepository::class);
        $this->userRepository = $this->createMock(MySqlUserRepository::class);
    }

    /**
     * Test task creation without user assigned successfully scenario.
     */
    public function testCreateTaskSuccessfullyWithoutAssignedUser(): void
    {
        $request = $this->createMock(CreateTaskRequest::class);
        $request->method('getTitle')->willReturn('Test Task');
        $request->method('getDescription')->willReturn('Task description');
        $request->method('getStatus')->willReturn(Status::Pending);
        $request->method('getPriority')->willReturn(Priority::Low);
        $request->method('getDueDate')->willReturn(null);
        $request->method('getCreatedAt')->willReturn(new \DateTime());
        $request->method('getUpdatedAt')->willReturn(new \DateTime());
        $request->method('getAssignedTo')->willReturn(null);

        $this->taskRepository
            ->expects($this->once())
            ->method('save');

        $useCase = new CreateTaskUseCase($this->taskRepository, $this->userRepository);
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

        $request = $this->createMock(CreateTaskRequest::class);
        $request->method('getTitle')->willReturn('Test Task');
        $request->method('getDescription')->willReturn('Task description');
        $request->method('getStatus')->willReturn(Status::Pending);
        $request->method('getPriority')->willReturn(Priority::High);
        $request->method('getDueDate')->willReturn(null);
        $request->method('getCreatedAt')->willReturn(new \DateTime());
        $request->method('getUpdatedAt')->willReturn(new \DateTime());
        $request->method('getAssignedTo')->willReturn($userId);

        $this->userRepository
            ->expects($this->once())
            ->method('findById')
            ->with($userId)
            ->willReturn($user);

        $this->taskRepository
            ->expects($this->once())
            ->method('save');

        $useCase = new CreateTaskUseCase($this->taskRepository, $this->userRepository);
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

        $request = $this->createMock(CreateTaskRequest::class);
        $request->method('getTitle')->willReturn('Test Task');
        $request->method('getDescription')->willReturn('Task description');
        $request->method('getStatus')->willReturn(Status::InProgress);
        $request->method('getPriority')->willReturn(Priority::Low);
        $request->method('getDueDate')->willReturn(null);
        $request->method('getCreatedAt')->willReturn(new \DateTime());
        $request->method('getUpdatedAt')->willReturn(new \DateTime());
        $request->method('getAssignedTo')->willReturn($userId);

        $this->userRepository
            ->expects($this->once())
            ->method('findById')
            ->with($userId)
            ->willReturn(null);

        $this->taskRepository
            ->expects($this->never())
            ->method('save');

        $useCase = new CreateTaskUseCase($this->taskRepository, $this->userRepository);
        $response = $useCase->execute($request);

        $this->assertEquals('User not found.', $response->getMessage());
        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getCodeStatus());
    }

    /**
     * Test exception thrown during task saving scenario.
     */
    public function testExceptionDuringTaskCreation(): void
    {
        $request = $this->createMock(CreateTaskRequest::class);
        $request->method('getTitle')->willReturn('Test Task');
        $request->method('getDescription')->willReturn('Task description');
        $request->method('getStatus')->willReturn(Status::InProgress);
        $request->method('getPriority')->willReturn(Priority::Medium);
        $request->method('getDueDate')->willReturn(null);
        $request->method('getCreatedAt')->willReturn(new \DateTime());
        $request->method('getUpdatedAt')->willReturn(new \DateTime());
        $request->method('getAssignedTo')->willReturn(null);

        $this->taskRepository
            ->method('save')
            ->will($this->throwException(new \Exception('Database error', Response::HTTP_INTERNAL_SERVER_ERROR)));

        $useCase = new CreateTaskUseCase($this->taskRepository, $this->userRepository);
        $response = $useCase->execute($request);

        $this->assertStringContainsString('Error creating task', $response->getMessage());
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getCodeStatus());
    }
}
