<?php

namespace App\Tests\Unit\Application\UseCase\Task;

use App\Application\UseCase\Task\GetTaskDetail\GetTaskDetailUseCase;
use App\Application\UseCase\Task\GetTaskDetail\GetTaskDetailRequest;
use App\Domain\Model\Task;
use App\Domain\Repository\TaskRepositoryInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;
use PHPUnit\Framework\MockObject\MockObject;

class GetTaskDetailUseCaseTest extends TestCase
{
    /** @var TaskRepositoryInterface&MockObject */
    private $taskRepositoryInterface;

    protected function setUp(): void
    {
        $this->taskRepositoryInterface = $this->createMock(TaskRepositoryInterface::class);
    }

    /**
     * Test successfull obtained task details scenario.
     */
    public function testGetTaskDetailSuccessfully(): void
    {
        $taskId = 'task-uuid';
        $task = $this->createMock(Task::class);

        $this->taskRepositoryInterface
            ->expects($this->once())
            ->method('findById')
            ->with($taskId)
            ->willReturn($task);

        $useCase = new GetTaskDetailUseCase($this->taskRepositoryInterface);
        $request = new GetTaskDetailRequest($taskId);
        $response = $useCase->execute($request);

        $this->assertEquals('Task details obtained successfully', $response->getMessage());
        $this->assertEquals(Response::HTTP_OK, $response->getCodeStatus());
        $this->assertInstanceOf(Task::class, $response->getTask());
    }

    /**
     * Test task not found scenario.
     */
    public function testTaskNotFound(): void
    {
        $taskId = 'task-uuid';

        $this->taskRepositoryInterface
            ->expects($this->once())
            ->method('findById')
            ->with($taskId)
            ->willReturn(null);

        $useCase = new GetTaskDetailUseCase($this->taskRepositoryInterface);
        $request = new GetTaskDetailRequest($taskId);
        $response = $useCase->execute($request);

        $this->assertEquals('No tasks found', $response->getMessage());
        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getCodeStatus());
        $this->assertNull($response->getTask());
    }

    /**
     * Test exception thrown during task find.
     */
    public function testExceptionDuringGetTask(): void
    {
        $taskId = 'task-uuid';

        $this->taskRepositoryInterface
            ->method('findById')
            ->will($this->throwException(new \Exception('Database error', Response::HTTP_INTERNAL_SERVER_ERROR)));

        $useCase = new GetTaskDetailUseCase($this->taskRepositoryInterface);
        $request = new GetTaskDetailRequest($taskId);
        $response = $useCase->execute($request);

        $this->assertStringContainsString('Error obtaining tasks', $response->getMessage());
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getCodeStatus());
        $this->assertNull($response->getTask());
    }
}
