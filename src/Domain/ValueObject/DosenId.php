<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

use Ramsey\Uuid\Uuid;

class DosenId
{
    private string $id;

    public function __construct(string $id)
    {
        if (Uuid::isValid($id)) {
            $this->id = $id;
        } else {
            throw new \InvalidArgumentException("Invalid DosenId format.");
        }
    }

    public function id() : string
    {
        return $this->id;
    }

    public function equals(DosenId $dosenId): bool
    {
        return $this->id === $dosenId->id;
    }

    public function __toString(): string
    {
        return $this->id;
    }
}