<?php

namespace App\Tests\Unit\Application\UseCase\User;

use App\Application\UseCase\User\CreateUser\CreateUserUseCase;
use App\Application\UseCase\User\CreateUser\CreateUserRequest;
use App\Infrastructure\Repository\MySqlUserRepository;
use App\Domain\Model\User;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;

class CreateUserUseCaseTest extends TestCase
{
    private $userRepository;

    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(MySqlUserRepository::class);
    }

    /**
     * Test user created successfully scenario.
     */
    public function testCreateUserSuccess(): void
    {
        $name = 'Marc Roige';
        $email = 'marcroige88@gmail.com';

        $this->userRepository
            ->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(User::class));

        $useCase = new CreateUserUseCase($this->userRepository);
        $request = $this->createMock(CreateUserRequest::class);
        $request->method('getName')->willReturn($name);
        $request->method('getEmail')->willReturn($email);

        $response = $useCase->execute($request);

        $this->assertEquals('User created successfully', $response->getMessage());
        $this->assertEquals(Response::HTTP_CREATED, $response->getCodeStatus());
        $this->assertInstanceOf(User::class, $response->getUser());
        $this->assertEquals($name, $response->getUser()->getName());
        $this->assertEquals($email, $response->getUser()->getEmail());
    }

    /**
     * Test exception thrown during save user scenario.
     */
    public function testExceptionDuringSave(): void
    {
        $name = 'Farmapremium';
        $email = 'farmapremium.com';

        $this->userRepository
            ->method('save')
            ->will($this->throwException(new \Exception('Database error', Response::HTTP_INTERNAL_SERVER_ERROR)));

        $useCase = new CreateUserUseCase($this->userRepository);
        $request = $this->createMock(CreateUserRequest::class);
        $request->method('getName')->willReturn($name);
        $request->method('getEmail')->willReturn($email);

        $response = $useCase->execute($request);

        $this->assertStringContainsString('Error creating user: Database error', $response->getMessage());
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getCodeStatus());
        $this->assertNull($response->getUser());
    }
}
