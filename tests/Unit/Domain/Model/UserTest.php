<?php

namespace App\Tests\Unit\Domain\Model;

use App\Domain\Model\User;
use DateTime;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testUserIsCreatedWithConstructorValues(): void
    {
        $name = 'Marc Roige';
        $email = 'marcroige88@gmail.com';
        $user = new User($name, $email);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($name, $user->getName());
        $this->assertEquals($email, $user->getEmail());
        $this->assertInstanceOf(DateTime::class, $user->getCreatedAt(), 'createdAt must be initialized in the construct method.');
    }

    public function testSettersAndGettersWorkCorrectly(): void
    {
        $user = new User('Technical Test', 'technicaltest@gmail.com');

        $newName = 'Farmapremium';
        $newEmail = 'farmapremium@gmail.com';
        $newCreatedAt = new DateTime('-1 day');

        $user->setName($newName)
             ->setEmail($newEmail)
             ->setCreatedAt($newCreatedAt);

        $this->assertEquals($newName, $user->getName());
        $this->assertEquals($newEmail, $user->getEmail());
        $this->assertEquals($newCreatedAt, $user->getCreatedAt());
    }
}
