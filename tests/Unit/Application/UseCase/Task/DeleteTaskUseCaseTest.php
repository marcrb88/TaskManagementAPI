<?php

namespace App\Tests\Unit\Application\UseCase\Task;

use App\Application\UseCase\Task\DeleteTask\DeleteTaskUseCase;
use App\Application\UseCase\Task\DeleteTask\DeleteTaskRequest;
use App\Infrastructure\Repository\MySqlTaskRepository;
use App\Domain\Model\Task;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;

class DeleteTaskUseCaseTest extends TestCase
{
    private $taskRepository;

    protected function setUp(): void
    {
        $this->taskRepository = $this->createMock(MySqlTaskRepository::class);
    }

    /**
     * Test delete status pending task successfully scenario.
     */
    public function testDeleteTaskSuccessfully(): void
    {
        $taskId = 'task-uuid';
        $task = $this->createMock(Task::class);

        $this->taskRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['id' => $taskId, 'status' => 'pending'])
            ->willReturn($task);

        $this->taskRepository
            ->expects($this->once())
            ->method('delete')
            ->with($task);

        $useCase = new DeleteTaskUseCase($this->taskRepository);
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

        $this->taskRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['id' => $taskId, 'status' => 'pending'])
            ->willReturn(null);

        $this->taskRepository
            ->expects($this->never())
            ->method('delete');

        $useCase = new DeleteTaskUseCase($this->taskRepository);
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

        $this->taskRepository
            ->method('findOneBy')
            ->willReturn($task);

        $this->taskRepository
            ->method('delete')
            ->will($this->throwException(new \Exception('Database error', Response::HTTP_INTERNAL_SERVER_ERROR)));

        $useCase = new DeleteTaskUseCase($this->taskRepository);
        $request = new DeleteTaskRequest($taskId);
        $response = $useCase->execute($request);

        $this->assertStringContainsString('Error obtaining tasks', $response->getMessage());
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getCodeStatus());
    }
}
