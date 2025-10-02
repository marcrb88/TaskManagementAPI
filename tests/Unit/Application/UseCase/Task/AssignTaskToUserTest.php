<?php

namespace App\Tests\Unit\Application\Task;

use App\Application\UseCase\Task\AssignTaskToUser\AssignTaskToUserUseCase;
use App\Application\UseCase\Task\AssignTaskToUser\AssignTaskToUserRequest;
use App\Domain\Model\Task;
use App\Domain\Model\User;
use App\Domain\Repository\TaskRepositoryInterface;
use App\Domain\Repository\UserRepositoryInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;
use PHPUnit\Framework\MockObject\MockObject;

class AssignTaskToUserTest extends TestCase
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
    *    Test we assign task to user successfully scenario.
    */

    public function testAssignTaskToUserSuccess(): void
    {
        $taskId = 'task-uuid';
        $userId = 'user-uuid';

        $task = $this->createMock(Task::class);
        $user = $this->createMock(User::class);

        $this->taskRepositoryInterface
            ->expects($this->once())
            ->method('findById')
            ->with($taskId)
            ->willReturn($task);

        $this->userRepositoryInterface
            ->expects($this->once())
            ->method('findById')
            ->with($userId)
            ->willReturn($user);

        $this->taskRepositoryInterface
            ->expects($this->once())
            ->method('save')
            ->with($task);

        $useCase = new AssignTaskToUserUseCase($this->taskRepositoryInterface, $this->userRepositoryInterface);
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

        $this->taskRepositoryInterface
            ->expects($this->once())
            ->method('findById')
            ->with($taskId)
            ->willReturn(null);

        $this->userRepositoryInterface
            ->expects($this->never())
            ->method('findById');

        $useCase = new AssignTaskToUserUseCase($this->taskRepositoryInterface, $this->userRepositoryInterface);
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

        $this->taskRepositoryInterface
            ->expects($this->once())
            ->method('findById')
            ->with($taskId)
            ->willReturn($task);

        $this->userRepositoryInterface
            ->expects($this->once())
            ->method('findById')
            ->with($userId)
            ->willReturn(null);

        $this->taskRepositoryInterface
            ->expects($this->never())
            ->method('save');

        $useCase = new AssignTaskToUserUseCase($this->taskRepositoryInterface, $this->userRepositoryInterface);
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

        $this->taskRepositoryInterface
            ->method('findById')
            ->willReturn($task);

        $this->userRepositoryInterface
            ->method('findById')
            ->willReturn($user);
            
        $this->taskRepositoryInterface
            ->method('save')
            ->will($this->throwException(new \Exception('Database error', Response::HTTP_INTERNAL_SERVER_ERROR)));

        $useCase = new AssignTaskToUserUseCase($this->taskRepositoryInterface, $this->userRepositoryInterface);
        $request = new AssignTaskToUserRequest($taskId, $userId);
        $response = $useCase->execute($request);

        $this->assertEquals('Error assigning user to task: Database error', $response->getMessage());
        $this->assertEquals(500, $response->getCodeStatus());
    }
}
