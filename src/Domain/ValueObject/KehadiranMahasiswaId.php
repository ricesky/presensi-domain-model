<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

class KehadiranMahasiswaId
{
    private PertemuanId $pertemuanId;
    private MahasiswaId $mahasiswaId;

    public function __construct(PertemuanId $pertemuanId, MahasiswaId $mahasiswaId)
    {
        $this->pertemuanId = $pertemuanId;
        $this->mahasiswaId = $mahasiswaId;
    }

    public function pertemuanId(): PertemuanId
    {
        return $this->pertemuanId;
    }

    public function mahasiswaId(): MahasiswaId
    {
        return $this->mahasiswaId;
    }

    public function equals(KehadiranMahasiswaId $kehadiranMahasiswaId): bool
    {
        return $this->pertemuanId->equals($kehadiranMahasiswaId->pertemuanId()) && 
            $this->mahasiswaId->equals($kehadiranMahasiswaId->mahasiswaId());
    }
}