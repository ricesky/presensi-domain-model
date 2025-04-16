<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\ValueObject\DosenId;

class Dosen
{
    private DosenId $dosenId;
    private string $nama;

    public function __construct(
        DosenId $dosenId,
        string $nama,
    )
    {
        $this->dosenId = $dosenId;
        $this->nama = $nama;
    }

    public function getDosenId(): DosenId
    {
        return $this->dosenId;
    }

    public function getNama(): string
    {
        return $this->nama;
    }
}