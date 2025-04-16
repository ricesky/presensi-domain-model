<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

use Ramsey\Uuid\Uuid;

class RuanganId
{
    private string $id;

    public function __construct(string $id)
    {
        if (Uuid::isValid($id)) {
            $this->id = $id;
        } else {
            throw new \InvalidArgumentException("Invalid RuanganId format.");
        }
    }

    public function id() : string
    {
        return $this->id;
    }

    public function equals(RuanganId $ruanganId): bool
    {
        return $this->id === $ruanganId->id;
    }

    public function __toString(): string
    {
        return $this->id;
    }
}