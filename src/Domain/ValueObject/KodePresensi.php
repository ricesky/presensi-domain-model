<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

use App\Domain\Exception\KodePresensiException;
use DateTime;

class KodePresensi
{
    private const PANJANG_KODE_PRESENSI = 6;

    private string $kode;
    private DateTime $berlakuSampai;

    public function __construct(string $kode, DateTime $berlakuSampai)
    {
        if (strlen($kode) != self::PANJANG_KODE_PRESENSI) {
            throw new KodePresensiException('panjang_kode_presensi_tidak_sesuai');
        }

        $this->kode = $kode;
        $this->berlakuSampai = $berlakuSampai;
    }

    public function isValid(string $kode): bool
    {
        $now = new DateTime('now');

        if ($this->kode == $kode && $this->berlakuSampai > $now) {
            return true;
        }

        return false;
    }

    public static function generate(DateTime $berlakuSampai): KodePresensi
    {
        $newCode = '';
        for ($digit = 1; $digit <= 6; $digit++) {
            $rand = chr(mt_rand(0, 9) + ord('0'));
            $newCode .= $rand;
        }

        return new KodePresensi($newCode, $berlakuSampai);
    }

    public function gantiKode(): KodePresensi
    {
        return KodePresensi::generate($this->berlakuSampai);
    }

    public function getKode(): string
    {
        return $this->kode;
    }

    public function getBerlakuSampai(): DateTime
    {
        return $this->berlakuSampai;
    }
}
