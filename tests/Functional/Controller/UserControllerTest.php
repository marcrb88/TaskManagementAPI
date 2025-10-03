<?php

namespace App\Tests\Integration\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class UserControllerTest extends WebTestCase
{
    private $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    /**
     * Test creating a user scenario.
     */
    public function testCreateUser(): void
    {
        $payload = [
            "name" => "Marc Roige Benaiges",
            "email" => uniqid() . "@gmail.com"
        ];

        $this->client->request(
            'POST',
            '/api/users',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($payload)
        );

        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('id', $data, 'Response should contain user id');
        $this->assertArrayHasKey('message', $data, 'Response should contain message');
        $this->assertArrayHasKey('statusCode', $data, 'Response should contain statusCode');
    }

    /**
     * Test creating an user with an existing email.
     */
    public function testCreateAnExistingUser(): void
    {
        $payload = [
            "name" => "Marc Roige Benaiges",
            "email" => "marcroige88@gmail.com"
        ];

        $this->client->request(
            'POST',
            '/api/users',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($payload)
        );

        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);

        $this->assertStringContainsString('already exists', $data['message']);

    }

    /**
     * Test listing users scenario.
     */
    public function testListUsers(): void
    {
        $this->client->request('GET', '/api/users');

        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('users', $data, 'Response should contain users array');
        $this->assertArrayHasKey('message', $data);
        $this->assertArrayHasKey('statusCode', $data);
    }
}
