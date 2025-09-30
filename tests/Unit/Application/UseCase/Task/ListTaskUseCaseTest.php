<?php

namespace App\Tests\Unit\Application\UseCase\Task;

use App\Application\UseCase\Task\ListTask\ListTaskUseCase;
use App\Application\UseCase\Task\ListTask\CreateFilterRequest;
use App\Infrastructure\Repository\MySqlTaskRepository;
use App\Domain\Model\Task;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;

class ListTaskUseCaseTest extends TestCase
{
    private $taskRepository;

    protected function setUp(): void
    {
        $this->taskRepository = $this->createMock(MySqlTaskRepository::class);
    }

    /**
     * Test list tasks successfully without filters scenario.
     */
    public function testListTasksSuccessfullyWithoutFilters(): void
    {
        $tasks = [
            $this->createMock(Task::class),
            $this->createMock(Task::class),
        ];

        $this->taskRepository
            ->expects($this->once())
            ->method('findAll')
            ->willReturn($tasks);

        $filterRequest = $this->createMock(CreateFilterRequest::class);
        $filterRequest->method('toArray')->willReturn([]);

        $useCase = new ListTaskUseCase($this->taskRepository);
        $response = $useCase->execute($filterRequest);

        $this->assertEquals('Tasks obtained successfully', $response->getMessage());
        $this->assertEquals(Response::HTTP_OK, $response->getCodeStatus());
        $this->assertEquals($tasks, $response->getTasks());
    }

    /**
     * Test list tasks successfully with filters escenario.
     */
    public function testListTasksSuccessfullyWithFilters(): void
    {
        $tasks = [
            $this->createMock(Task::class),
        ];

        $filters = ['status' => 'pending'];

        $this->taskRepository
            ->expects($this->once())
            ->method('findByFilters')
            ->with($filters)
            ->willReturn($tasks);

        $filterRequest = $this->createMock(CreateFilterRequest::class);
        $filterRequest->method('toArray')->willReturn($filters);

        $useCase = new ListTaskUseCase($this->taskRepository);
        $response = $useCase->execute($filterRequest);

        $this->assertEquals('Tasks obtained successfully', $response->getMessage());
        $this->assertEquals(Response::HTTP_OK, $response->getCodeStatus());
        $this->assertEquals($tasks, $response->getTasks());
    }

    /**
     * Test no tasks found scenario.
     */
    public function testNoTasksFound(): void
    {
        $this->taskRepository
            ->method('findAll')
            ->willReturn([]);

        $filterRequest = $this->createMock(CreateFilterRequest::class);
        $filterRequest->method('toArray')->willReturn([]);

        $useCase = new ListTaskUseCase($this->taskRepository);
        $response = $useCase->execute($filterRequest);

        $this->assertEquals('No tasks found', $response->getMessage());
        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getCodeStatus());
        $this->assertEmpty($response->getTasks());
    }

    /**
     * Test exception thrown during task retrieval scenario.
     */
    public function testExceptionDuringListTasks(): void
    {
        $this->taskRepository
            ->method('findAll')
            ->will($this->throwException(new \Exception('Database error', Response::HTTP_INTERNAL_SERVER_ERROR)));

        $filterRequest = $this->createMock(CreateFilterRequest::class);
        $filterRequest->method('toArray')->willReturn([]);

        $useCase = new ListTaskUseCase($this->taskRepository);
        $response = $useCase->execute($filterRequest);

        $this->assertStringContainsString('Error obtaining tasks', $response->getMessage());
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getCodeStatus());
        $this->assertEmpty($response->getTasks());
    }
}
