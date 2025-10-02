<?php

namespace App\Tests\Unit\Application\UseCase\Task;

use App\Application\UseCase\Task\DeleteTask\DeleteTaskUseCase;
use App\Application\UseCase\Task\DeleteTask\DeleteTaskRequest;
use App\Domain\Model\Task;
use App\Domain\Repository\TaskRepositoryInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;
use PHPUnit\Framework\MockObject\MockObject;

class DeleteTaskUseCaseTest extends TestCase
{
    /** @var TaskRepositoryInterface&MockObject */
    private $taskRepositoryInterface;

    protected function setUp(): void
    {
        $this->taskRepositoryInterface = $this->createMock(TaskRepositoryInterface::class);
    }

    /**
     * Test delete status pending task successfully scenario.
     */
    public function testDeleteTaskSuccessfully(): void
    {
        $taskId = 'task-uuid';
        $task = $this->createMock(Task::class);

        $this->taskRepositoryInterface
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['id' => $taskId, 'status' => 'pending'])
            ->willReturn($task);

        $this->taskRepositoryInterface
            ->expects($this->once())
            ->method('delete')
            ->with($task);

        $useCase = new DeleteTaskUseCase($this->taskRepositoryInterface);
        $request = new DeleteTaskRequest($taskId);
        $response = $useCase->execute($request);

        $this->assertEquals('Task deleted succesfully', $response->getMessage());
        $this->assertEquals(Response::HTTP_OK, $response->getCodeStatus());
    }

    /**
     * Test no pending task is found scenario.
     */
    public function testTaskNotFound(): void
    {
        $taskId = 'task-uuid';

        $this->taskRepositoryInterface
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['id' => $taskId, 'status' => 'pending'])
            ->willReturn(null);

        $this->taskRepositoryInterface
            ->expects($this->never())
            ->method('delete');

        $useCase = new DeleteTaskUseCase($this->taskRepositoryInterface);
        $request = new DeleteTaskRequest($taskId);
        $response = $useCase->execute($request);

        $this->assertEquals('No task found to delete', $response->getMessage());
        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getCodeStatus());
    }

    /**
     * Test exception during deletion scenario.
     */
    public function testExceptionDuringDeletion(): void
    {
        $taskId = 'task-uuid';
        $task = $this->createMock(Task::class);

        $this->taskRepositoryInterface
            ->method('findOneBy')
            ->willReturn($task);

        $this->taskRepositoryInterface
            ->method('delete')
            ->will($this->throwException(new \Exception('Database error', Response::HTTP_INTERNAL_SERVER_ERROR)));

        $useCase = new DeleteTaskUseCase($this->taskRepositoryInterface);
        $request = new DeleteTaskRequest($taskId);
        $response = $useCase->execute($request);

        $this->assertStringContainsString('Error obtaining tasks', $response->getMessage());
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getCodeStatus());
    }
}
