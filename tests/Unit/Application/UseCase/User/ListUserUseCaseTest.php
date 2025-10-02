<?php

namespace App\Tests\Unit\Application\UseCase\User;

use App\Application\UseCase\User\ListUser\ListUserUseCase;
use App\Domain\Model\User;
use App\Domain\Repository\UserRepositoryInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;
use PHPUnit\Framework\MockObject\MockObject;

class ListUserUseCaseTest extends TestCase
{
    /** @var UserRepositoryInterface&MockObject */
    private $userRepositoryInterface;

    protected function setUp(): void
    {
        $this->userRepositoryInterface = $this->createMock(UserRepositoryInterface::class);
    }

    /**
     * Test users obtained successfully scenario.
     */
    public function testListUsersSuccess(): void
    {
        $user1 = $this->createMock(User::class);
        $user2 = $this->createMock(User::class);

        $this->userRepositoryInterface
            ->expects($this->once())
            ->method('findAll')
            ->willReturn([$user1, $user2]);

        $useCase = new ListUserUseCase($this->userRepositoryInterface);
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
        $this->userRepositoryInterface
            ->expects($this->once())
            ->method('findAll')
            ->willReturn([]);

        $useCase = new ListUserUseCase($this->userRepositoryInterface);
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
        $this->userRepositoryInterface
            ->method('findAll')
            ->will($this->throwException(new \Exception('Database error', Response::HTTP_INTERNAL_SERVER_ERROR)));

        $useCase = new ListUserUseCase($this->userRepositoryInterface);
        $response = $useCase->execute();

        $this->assertStringContainsString('Error obtaining users: Database error', $response->getMessage());
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getCodeStatus());
    }
}
