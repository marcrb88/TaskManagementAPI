<?php
namespace App\Domain\Repository;

use App\Application\Service\Response\DataValidatorResponse;

interface DataValidatorInterface
{
    public function validate(array $data): DataValidatorResponse;
}