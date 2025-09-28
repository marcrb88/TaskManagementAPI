<?php
namespace App\Domain\Repository;

use App\Application\Service\Response\FilterTaskDataValidatorResponse;

interface DataValidatorInterface
{
    public function validate(array $data): FilterTaskDataValidatorResponse;
}