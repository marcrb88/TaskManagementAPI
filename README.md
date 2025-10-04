# Task Management API

## Description

REST API to allow creating, listing, updating and deleting tasks, as well as assigning them to users.

## Technologies

- PHP 8.2
- Symfony 6
- Doctrine ORM for data persistence
- MySQL
- Docker & Docker Compose for development environment
- PHPUnit for testing
- OpenAPI 3 for API documentation

## Installation and running

### Clone the repository

```bash
git clone https://github.com/marcrb88/TaskManagementAPI.git
```

### Change current directory to the project

```bash
cd ~/TaskManagementAPI
```

### Up containers with docker compose

```bash
docker compose up -d --build
```
### Install composer 

```bash
docker compose exec php-app composer install
```

### Run database migrations

```bash
docker compose exec php-app bin/console doctrine:migrations:migrate
```

### Run tests

```bash
docker compose exec php-app bin/phpunit
```

## API Documentation

The detailed API documentation was created using OpenAPI/Swagger and can be accessed via: https://editor.swagger.io/. You have to upload the file you will find in the root folder of the project called openapi.yaml.

| Method | Endpoint              | Description                        |
| ------ | --------------------- | ---------------------------------- |
| GET    | api/users             | List users                         |
| POST   | api/users             | Create a new user                  |
| GET    | api/tasks             | List tasks (with optional filters) |
| POST   | api/tasks             | Create a new task                  |
| GET    | api/tasks/{id}        | Get task details                   |
| PUT    | api/tasks/{id}        | Update a task                      |
| DELETE | api/tasks/{id}        | Delete a task                      |
| PATCH  | api/tasks/{id}/assign | Assign a task to a user            |

## Postman Collection

You can download the Postman collection to test the API following this link. Once downloaded, you can import it in Postman.
[Download Postman Collection](./TaskManagementAPIPostman.postman_collection.json)

## Technical decisions

- Factory implementation in order to, given a constant (CREATE,UPDATE,FILTER), obtain the properly builder for each case.

- Validation data that requires database query was implemented in application layer (UseCases) to avoid having domain dependencies in infraestructura layer.

- Interfaces and parent classes created to reuse code and do it more mantainable and flexible.

- DTOs implemented in order to comunicate data between layers.

- The endpoint GET /api/tasks supports optional parameters to allow the client filter the list of tasks. You can include  any combination of parameters.
> **Note 1:** The `assignedTo` filter is currently **not available**.

> **Note 2:** In task list filters, the `createdAt`, `dueDate`, and `updatedAt` filters must be passed in the `YYYY-MM-DD` format.  
> Example: `2025-10-10`

- Error responses management in every use case to return to client the specific code and message response of the operation.

- Serialize Abstract class with an implementated toArray() method in order to have array serialization in all DTOS.


## Future improvements

- Implement authentication and authorization (JWT/OAuth2).
- Pagination and sortig for task listings.
- Improve the filtering task endpoint: allow to filter by assignedTo user. It would be implementated adding the property in the CreateFilterRequest, setting it in the CreateFilterTaskRequestBuilder and adding the condition in MySqlTaskRepository.
- Events to notificate users (via mail for example) when tasks are assigned.
- Integrate the API with frontend.
- Cache with Redis for improved performance when listing tasks to improve performance in large list of tasks.
- In definitive, the optional bonus points that suggests the technical test.






