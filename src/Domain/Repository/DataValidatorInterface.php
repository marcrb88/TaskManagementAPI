<?php
namespace App\Domain\Repository;

use App\Application\Service\Response\TaskDataValidatorResponse;

interface DataValidatorInterface
{
    public function validate(array $data): TaskDataValidatorResponse;
}