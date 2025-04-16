<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

use DateTime;

class KodePresensi
{
    private ?string $kode;
    private ?DateTime $berlakuSampai;

    public function __construct(?string $kode, ?DateTime $berlakuSampai)
    {
        $this->kode = $kode;
        $this->berlakuSampai = $berlakuSampai;
    }

    public function isValid(string $kode): bool
    {
        $now = new DateTime();

        if ($this->kode == $kode && $this->berlakuSampai > $now) {
            return true;
        }

        return false;
    }

    public function generate(): KodePresensi
    {
        $newCode = '';
        for ($digit = 1; $digit <= 6; $digit++) {
            $rand = chr(mt_rand(0, 9) + ord('0'));
            $newCode .= $rand;
        }

        $this->kode = $newCode;
        return $this;
    }

    public function getKode(): ?string
    {
        return $this->kode;
    }

    public function getBerlakuSampai(): string
    {
        return $this->berlakuSampai->format('Y-m-d H:i:s');
    }
}
