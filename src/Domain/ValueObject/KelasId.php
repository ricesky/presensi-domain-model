<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

use Ramsey\Uuid\Uuid;

class KelasId
{
    private $id;

    public function __construct(string $id)
    {
        if (Uuid::isValid($id)) {
            $this->id = $id;
        } else {
            throw new \InvalidArgumentException("Invalid KelasId format.");
        }
    }

    public function id() : string
    {
        return $this->id;
    }

    public function equals(KelasId $kelasId): bool
    {
        return $this->id === $kelasId->id;
    }

    public function __toString(): string
    {
        return $this->id;
    }
}