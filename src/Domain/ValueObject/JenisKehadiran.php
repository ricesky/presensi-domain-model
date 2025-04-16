<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

use InvalidArgumentException;

class JenisKehadiran
{
    const HADIR = 'H';
    const IZIN = 'I';
    const SAKIT = 'S';
    const ALPA = 'A';
    
    private string $kehadiran;

    public function __construct(string $kehadiran)
    {
        if ($kehadiran != self::HADIR & 
            $kehadiran != self::IZIN &
            $kehadiran != self::SAKIT &
            $kehadiran != self::ALPA) {
            throw new InvalidArgumentException('jenis_kehadiran_tidak_sesuai');
        }

        $this->kehadiran = $kehadiran;
    }

    public function getKehadiran(): string
    {
        return $this->kehadiran;
    }

    public function equals(JenisKehadiran $jenisKehadiran): bool
    {
        return $this->kehadiran === $jenisKehadiran->getKehadiran();
    }

    public static function hadir(): JenisKehadiran
    {
        return new JenisKehadiran(self::HADIR);
    }

    public static function alpa(): JenisKehadiran
    {
        return new JenisKehadiran(self::ALPA);
    }

    public static function izin(): JenisKehadiran
    {
        return new JenisKehadiran(self::IZIN);
    }

    public static function sakit(): JenisKehadiran
    {
        return new JenisKehadiran(self::SAKIT);
    }


}