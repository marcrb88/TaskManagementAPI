<?php

namespace App\Tests\Unit\Application\UseCase\Task;

use App\Application\UseCase\Task\ListTask\ListTaskUseCase;
use App\Application\UseCase\Task\ListTask\CreateFilterRequest;
use App\Domain\Model\Task;
use App\Domain\Repository\TaskRepositoryInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;
use PHPUnit\Framework\MockObject\MockObject;

class ListTaskUseCaseTest extends TestCase
{
    /** @var TaskRepositoryInterface&MockObject */
    private $taskRepositoryInterface;

    protected function setUp(): void
    {
        $this->taskRepositoryInterface = $this->createMock(TaskRepositoryInterface::class);
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

        $this->taskRepositoryInterface
            ->expects($this->once())
            ->method('findAll')
            ->willReturn($tasks);

        /** @var CreateFilterRequest&MockObject $filterRequest */
        $filterRequest = $this->createMock(CreateFilterRequest::class);
        $filterRequest->method('toArray')->willReturn([]);

        $useCase = new ListTaskUseCase($this->taskRepositoryInterface);
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

        $this->taskRepositoryInterface
            ->expects($this->once())
            ->method('findByFilters')
            ->with($filters)
            ->willReturn($tasks);

        /** @var CreateFilterRequest&MockObject $filterRequest */
        $filterRequest = $this->createMock(CreateFilterRequest::class);
        $filterRequest->method('toArray')->willReturn($filters);

        $useCase = new ListTaskUseCase($this->taskRepositoryInterface);
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
        $this->taskRepositoryInterface
            ->method('findAll')
            ->willReturn([]);

        /** @var CreateFilterRequest&MockObject $filterRequest */
        $filterRequest = $this->createMock(CreateFilterRequest::class);
        $filterRequest->method('toArray')->willReturn([]);

        $useCase = new ListTaskUseCase($this->taskRepositoryInterface);
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
        $this->taskRepositoryInterface
            ->method('findAll')
            ->will($this->throwException(new \Exception('Database error', Response::HTTP_INTERNAL_SERVER_ERROR)));

        /** @var CreateFilterRequest&MockObject $filterRequest */
        $filterRequest = $this->createMock(CreateFilterRequest::class);
        $filterRequest->method('toArray')->willReturn([]);

        $useCase = new ListTaskUseCase($this->taskRepositoryInterface);
        $response = $useCase->execute($filterRequest);

        $this->assertStringContainsString('Error obtaining tasks', $response->getMessage());
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getCodeStatus());
        $this->assertEmpty($response->getTasks());
    }
}
