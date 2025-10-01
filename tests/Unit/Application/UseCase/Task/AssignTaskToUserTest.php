<?php

namespace App\Tests\Unit\Application\Task;

use App\Application\UseCase\Task\AssignTaskToUser\AssignTaskToUserUseCase;
use App\Application\UseCase\Task\AssignTaskToUser\AssignTaskToUserRequest;
use App\Infrastructure\Repository\MySqlTaskRepository;
use App\Infrastructure\Repository\MySqlUserRepository;
use App\Domain\Model\Task;
use App\Domain\Model\User;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;

class AssignTaskToUserTest extends TestCase
{
    private $taskRepository;
    private $userRepository;

    protected function setUp(): void
    {
        $this->taskRepository = $this->createMock(MySqlTaskRepository::class);
        $this->userRepository = $this->createMock(MySqlUserRepository::class);
    }

    /*
        Test we assign task to user successfully scenario.
    */

    public function testAssignTaskToUserSuccess(): void
    {
        $taskId = 'task-uuid';
        $userId = 'user-uuid';

        $task = $this->createMock(Task::class);
        $user = $this->createMock(User::class);

        $this->taskRepository
            ->expects($this->once())
            ->method('findById')
            ->with($taskId)
            ->willReturn($task);

        $this->userRepository
            ->expects($this->once())
            ->method('findById')
            ->with($userId)
            ->willReturn($user);

        $this->taskRepository
            ->expects($this->once())
            ->method('save')
            ->with($task);

        $useCase = new AssignTaskToUserUseCase($this->taskRepository, $this->userRepository);
        $request = new AssignTaskToUserRequest($taskId, $userId);
        $response = $useCase->execute($request);

        $this->assertEquals('Task assigned to user succesfully', $response->getMessage());
        $this->assertEquals(Response::HTTP_OK, $response->getCodeStatus());
    }

    /**
     * Test we don't find task scenario.
     */
    public function testTaskNotFound(): void
    {
        $taskId = 'task-uuid';
        $userId = 'user-uuid';

        $this->taskRepository
            ->expects($this->once())
            ->method('findById')
            ->with($taskId)
            ->willReturn(null);

        $this->userRepository
            ->expects($this->never())
            ->method('findById');

        $useCase = new AssignTaskToUserUseCase($this->taskRepository, $this->userRepository);
        $request = new AssignTaskToUserRequest($taskId, $userId);
        $response = $useCase->execute($request);

        $this->assertEquals('No task found to assign to user', $response->getMessage());
        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getCodeStatus());
    }

    /**
     * Test we don't find user scenario.
     */
    public function testUserNotFound(): void
    {
        $taskId = 'task-uuid';
        $userId = 'user-uuid';

        $task = $this->createMock(Task::class);

        $this->taskRepository
            ->expects($this->once())
            ->method('findById')
            ->with($taskId)
            ->willReturn($task);

        $this->userRepository
            ->expects($this->once())
            ->method('findById')
            ->with($userId)
            ->willReturn(null);

        $this->taskRepository
            ->expects($this->never())
            ->method('save');

        $useCase = new AssignTaskToUserUseCase($this->taskRepository, $this->userRepository);
        $request = new AssignTaskToUserRequest($taskId, $userId);
        $response = $useCase->execute($request);

        $this->assertEquals('No user found to be assigned to task', $response->getMessage());
        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getCodeStatus());
    }

    /**
     * Test exception thrown when saving task scenario.
     */
    public function testExceptionDuringAssignment(): void
    {
        $taskId = 'task-uuid';
        $userId = 'user-uuid';

        $task = $this->createMock(Task::class);
        $user = $this->createMock(User::class);

        $this->taskRepository
            ->method('findById')
            ->willReturn($task);

        $this->userRepository
            ->method('findById')
            ->willReturn($user);
            
        $this->taskRepository
            ->method('save')
            ->will($this->throwException(new \Exception('Database error', Response::HTTP_INTERNAL_SERVER_ERROR)));

        $useCase = new AssignTaskToUserUseCase($this->taskRepository, $this->userRepository);
        $request = new AssignTaskToUserRequest($taskId, $userId);
        $response = $useCase->execute($request);

        $this->assertEquals('Error assigning user to task: Database error', $response->getMessage());
        $this->assertEquals(500, $response->getCodeStatus());
    }
}
