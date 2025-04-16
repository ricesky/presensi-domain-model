<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

use Ramsey\Uuid\Uuid;

class MahasiswaId
{
    private $id;

    public function __construct(string $id)
    {
        if (Uuid::isValid($id)) {
            $this->id = $id;
        } else {
            throw new \InvalidArgumentException("Invalid class MahasiswaId format.");
        }
    }

    public function id() : string
    {
        return $this->id;
    }

    public function equals(MahasiswaId $mahasiswaId) : bool
    {
        return $this->id === $mahasiswaId->id;
    }
}