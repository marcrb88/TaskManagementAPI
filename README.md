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
docker compose up -d
```
```bash
docker compose exec php-app bin/console doctrine:migrations:migrate
```
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


## Technical decisions

- Factory implementation in order to, given a constant (CREATE,UPDATE,FILTER), obtain the properly builder for each case.

- Validation data that requires database query was implemented in application layer (UseCases) to avoid having domain dependencies in infraestructura layer.

- Interfaces and parent classes created to reuse code and do it more mantainable and flexible.

- DTOs implemented in order to comunicate data between layers.

- Task filters implemented by query parameters in GET /api/tasks.

- Error responses management in every use case to return to client the specific code and message response of the operation.

- Serialize Abstract class with an implementated toArray() method in order to have array serialization in all DTOS.


## Future improvements

- Implement authentication and authorization (JWT/OAuth2).
- Pagination and sortig for task listings.
- Events to notificate users (via mail for example) when tasks are assigned.
- Integrate the API with frontend.
- Cache with Redis for improved performance when listing tasks to improve performance in large list of tasks.
- In definitive, the optional bonus points that suggests the technical test.






