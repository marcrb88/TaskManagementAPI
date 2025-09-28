<?php

namespace App\Application\Response;

class BaseResponse
{
    protected string $message;

    public function __construct(string $message)
    {
        $this->message = $message;
    }

    public function getMessage(): string
    {
        return $this->message;
    }
    
    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }
}
