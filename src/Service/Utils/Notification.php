<?php declare(strict_types=1);

namespace App\Service\Utils;

class Notification
{
    private string $type;
    private string $message;

    public function __construct(string $type, string $message)
    {
        $this->type = $type;
        $this->message = $message;
    }

    public function getType(): string
    {
        return $this->type;
    }
    public function setType($type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getMessage(): string
    {
        return $this->message;
    }
    public function setMessage($message): self
    {
        $this->message = $message;
        return $this;
    }
}