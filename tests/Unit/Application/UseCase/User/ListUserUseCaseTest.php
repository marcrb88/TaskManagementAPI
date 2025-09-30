<?php

namespace App\Tests\Unit\Application\UseCase\User;

use App\Application\UseCase\User\ListUser\ListUserUseCase;
use App\Infrastructure\Repository\MySqlUserRepository;
use App\Domain\Model\User;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;

class ListUserUseCaseTest extends TestCase
{
    private $userRepository;

    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(MySqlUserRepository::class);
    }

    /**
     * Test users obtained successfully scenario.
     */
    public function testListUsersSuccess(): void
    {
        $user1 = $this->createMock(User::class);
        $user2 = $this->createMock(User::class);

        $this->userRepository
            ->expects($this->once())
            ->method('findAll')
            ->willReturn([$user1, $user2]);

        $useCase = new ListUserUseCase($this->userRepository);
        $response = $useCase->execute();

        $this->assertEquals('Users obtained successfully', $response->getMessage());
        $this->assertEquals(Response::HTTP_OK, $response->getCodeStatus());
        $this->assertCount(2, $response->getUsers());
    }

    /**
     * Tests users not found scenario.
     */
    public function testNoUsersFound(): void
    {
        $this->userRepository
            ->expects($this->once())
            ->method('findAll')
            ->willReturn([]);

        $useCase = new ListUserUseCase($this->userRepository);
        $response = $useCase->execute();

        $this->assertEquals('No users found', $response->getMessage());
        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getCodeStatus());
        $this->assertEmpty($response->getUsers());
    }

    /**
     * Test exception occurs during fetching users scenario.
     */
    public function testExceptionDuringFetch(): void
    {
        $this->userRepository
            ->method('findAll')
            ->will($this->throwException(new \Exception('Database error', Response::HTTP_INTERNAL_SERVER_ERROR)));

        $useCase = new ListUserUseCase($this->userRepository);
        $response = $useCase->execute();

        $this->assertStringContainsString('Error obtaining users: Database error', $response->getMessage());
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getCodeStatus());
    }
}
