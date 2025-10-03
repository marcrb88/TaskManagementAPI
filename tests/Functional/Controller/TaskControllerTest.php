<?php

namespace App\Tests\Integration\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class TaskControllerTest extends WebTestCase
{
    private $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    /** 
     * Test creating a task scenario.
     */
    public function testCreateTask(): void
    {
        $payload = [
            "title" => "Create Test Task",
            "description" => "Create Test Task",
            "dueDate" => "2025-10-20T00:00:00",
            "status" => "pending",
            "priority"=>"high",
            "createdAt" =>"2025-09-27T22:30:00",
            "assignedTo" => null
        ];

        $this->client->request(
            'POST',
            '/api/tasks',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($payload)
        );

        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        
        $this->assertTrue(
            in_array($response->getStatusCode(), [Response::HTTP_OK, Response::HTTP_CREATED]),
            'Expected HTTP 200 or 201 status code'
        );

        $data = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('id', $data, 'Response should contain task id');
        $this->assertArrayHasKey('message', $data, 'Response should contain message');
        $this->assertArrayHasKey('statusCode', $data, 'Response should contain statusCode');
        
    }

    /**
     * Test listing tasks scenario.
     */
    public function testListTasks(): void
    {
        $this->client->request('GET', '/api/tasks');

        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('tasks', $data, 'Response should contain tasks array');
        $this->assertArrayHasKey('message', $data);
        $this->assertArrayHasKey('statusCode', $data);
    }

    /**
     * Test obtaining task details scenario.
     */
    public function testGetTaskDetail(): void
    {
        $payload = [
            'title' => 'Detail Test Task',
            'description' => 'Task to test details',
            'dueDate' => '2025-10-10T12:00:00+00:00'
        ];

        $this->client->request(
            'POST',
            '/api/tasks',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($payload)
        );

        $createResponse = json_decode($this->client->getResponse()->getContent(), true);
        $taskId = $createResponse['id'];

        $this->client->request('GET', '/api/tasks/' . $taskId);

        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('task', $data, 'Response should contain task data');
        $this->assertEquals($taskId, $data['task']['id']);
    }

    /**
     * Test updating a task scenario.
     */
    public function testUpdateTask(): void
    {
        $payload = [
            'title' => 'Update Test Task',
            'description' => 'Task to test update',
            'dueDate' => '2025-10-10T12:00:00+00:00'
        ];

        $this->client->request(
            'POST',
            '/api/tasks',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($payload)
        );

        $createResponse = json_decode($this->client->getResponse()->getContent(), true);
        $taskId = $createResponse['id'];

        $updatePayload = [
            'title' => 'Updated Title',
            'description' => 'Updated Description'
        ];

        $this->client->request(
            'PUT',
            '/api/tasks/' . $taskId,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($updatePayload)
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('message', $data);
        $this->assertArrayHasKey('statusCode', $data);
    }

    /**
     * Test deleting a task scenario.
     */
    public function testDeleteTask(): void
    {
        $payload = [
            'title' => 'Delete Test Task',
            'description' => 'Task to test deletion',
            'dueDate' => '2025-10-10T12:00:00+00:00'
        ];

        $this->client->request(
            'POST',
            '/api/tasks',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($payload)
        );

        $createResponse = json_decode($this->client->getResponse()->getContent(), true);
        $taskId = $createResponse['id'];

        $this->client->request('DELETE', '/api/tasks/' . $taskId);

        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('message', $data);
        $this->assertArrayHasKey('statusCode', $data);
    }

    /**
     * Test assigning a task to a user scenario.
     */
    public function testAssignTaskToUser(): void
    {
        $payload = [
            'title' => 'Assign Test Task',
            'description' => 'Task to test assignment',
            'dueDate' => '2025-10-10T12:00:00+00:00'
        ];

        $this->client->request(
            'POST',
            '/api/tasks',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($payload)
        );

        $createResponse = json_decode($this->client->getResponse()->getContent(), true);
        $taskId = $createResponse['id'];

        $userPayload = [
            'name' => 'Test User',
            'email' => 'user_'.uniqid().'@example.com'
        ];

        $this->client->request(
            'POST',
            '/api/users',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($userPayload)
        );

        $userResponse = json_decode($this->client->getResponse()->getContent(), true);
        $userId = $userResponse['id'];

        $assignPayload = ['assignedTo' => $userId];

        $this->client->request(
            'PATCH',
            '/api/tasks/' . $taskId . '/assign',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($assignPayload)
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('message', $data);
        $this->assertArrayHasKey('statusCode', $data);
    }
}
