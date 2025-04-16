<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

use InvalidArgumentException;

class TopikPerkuliahan
{
    const MIN_PANJANG_DESKRIPSI = 10;
    const MAX_PANJANG_DESKRIPSI = 500;

    private string $deskripsi;
    private string $deskripsiEn;

    public function __construct(string $deskripsi, ?string $deskripsiEn)
    {
        $defaultDeskripsiEn = "-";

        if(!$deskripsiEn) {
            $deskripsiEn = $defaultDeskripsiEn;
        }

        $isDefaultDeskripsiEn = $deskripsiEn == $defaultDeskripsiEn;

        $panjangDeskripsi = strlen($deskripsi);
        if (!$this->isDeskripsiValid($panjangDeskripsi)) {
            throw new InvalidArgumentException('panjang_topik_tidak_sesuai');
        }

        $panjangDeskripsiEn = strlen($deskripsiEn);
        if (!$this->isDeskripsiValid($panjangDeskripsiEn) && !$isDefaultDeskripsiEn) {
            throw new InvalidArgumentException('panjang_topik_inggris_tidak_sesuai');
        }

        $this->deskripsi = $deskripsi;
        $this->deskripsiEn = $deskripsiEn;
    }

    public function isDeskripsiValid(int $panjangDeskripsi): bool {
        return $panjangDeskripsi >= self::MIN_PANJANG_DESKRIPSI && $panjangDeskripsi <= self::MAX_PANJANG_DESKRIPSI;
    }

    public function getDeskripsi(string $lang = 'id'): string
    {
        return $lang == 'en' ? $this->deskripsiEn : $this->deskripsi;
    }
}
