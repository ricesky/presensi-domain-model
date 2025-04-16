<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

use InvalidArgumentException;

class StatusPertemuan
{
    const BELUM_DIMULAI = '1';
    const SEDANG_BERLANGSUNG = '2';
    const SELESAI = '3';
    const TERLEWAT = '4';
    
    private string $status;

    public function __construct(string $status)
    {
        if ($status != self::BELUM_DIMULAI & 
            $status != self::SEDANG_BERLANGSUNG &
            $status != self::SELESAI &
            $status != self::TERLEWAT) {
            throw new InvalidArgumentException('status_pertemuan_tidak_sesuai');
        }

        $this->status = $status;
    }

    public function getStatus() : string
    {
        return $this->status;
    }

    public function equals(StatusPertemuan $statusPertemuan) : bool
    {
        return $this->status === $statusPertemuan->getStatus();
    }

    public function isBelumDimulai(): bool
    {
        if ($this->status === self::BELUM_DIMULAI) {
            return true;
        }
        return false;
    }

    public function isSedangBerlangsung(): bool
    {
        if ($this->status === self::SEDANG_BERLANGSUNG) {
            return true;
        }
        return false;
    }

    public function isSelesai(): bool
    {
        if ($this->status === self::SELESAI) {
            return true;
        }
        return false;
    }

    public function isTerlewat(): bool
    {
        if ($this->status === self::TERLEWAT) {
            return true;
        }
        return false;
    }

    public static function belumDimulai(): StatusPertemuan
    {
        return new StatusPertemuan(self::BELUM_DIMULAI);
    }

    public static function sedangBerlangsung(): StatusPertemuan
    {
        return new StatusPertemuan(self::SEDANG_BERLANGSUNG);
    }

    public static function selesai(): StatusPertemuan
    {
        return new StatusPertemuan(self::SELESAI);
    }

    public static function terlewat(): StatusPertemuan
    {
        return new StatusPertemuan(self::TERLEWAT);
    }

}