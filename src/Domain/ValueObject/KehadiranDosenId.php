<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

class KehadiranDosenId
{
    private PertemuanId $pertemuanId;
    private DosenId $dosenId;

    public function __construct(PertemuanId $pertemuanId, DosenId $dosenId)
    {
        $this->pertemuanId = $pertemuanId;
        $this->dosenId = $dosenId;
    }

    public function pertemuanId(): PertemuanId
    {
        return $this->pertemuanId;
    }

    public function dosenId(): DosenId
    {
        return $this->dosenId;
    }

    public function equals(KehadiranDosenId $kehadiranDosenId): bool
    {
        return $this->pertemuanId->equals($kehadiranDosenId->pertemuanId()) && 
            $this->dosenId->equals($kehadiranDosenId->dosenId());
    }
}