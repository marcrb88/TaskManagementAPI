<?php

namespace App\Application\Service\Response;

use App\Application\Response\BaseResponse;

class TaskDataValidatorResponse extends BaseResponse
{
    private bool $isValid;

    public function __construct(bool $isValid, string $message = '')
    {
        parent::__construct($message);
        $this->isValid = $isValid;
    }

    public function isValid(): bool
    {
        return $this->isValid;
    }

    public function setIsValid(bool $isValid): self
    {
        $this->isValid = $isValid;

        return $this;
    }

}
