<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

use InvalidArgumentException;

class UrutanPertemuan
{   
    private int $urutan;

    public function __construct(int $urutan)
    {
        if ($urutan <= 0) {
            throw new InvalidArgumentException('urutan_harus_lebih_besar_dari_0');
        }
        $this->urutan = $urutan;
    }

    public function getUrutan(): int
    {
        return $this->urutan;
    }

    public function equals(UrutanPertemuan $urutanPertemuan): bool
    {
        return $this->urutan === $urutanPertemuan->getUrutan();
    }

}