<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

use Ramsey\Uuid\Uuid;

class TatapMukaId
{
    private string $id;

    public function __construct(string $id = null)
    {
        $this->id = $id ? : Uuid::uuid4()->toString();
    }

    public function id() : string
    {
        return $this->id;
    }

    public function equals(TatapMukaId $tatapMukaId): bool
    {
        return $this->id === $tatapMukaId->id;
    }

    public function __toString(): string
    {
        return $this->id;
    }
}