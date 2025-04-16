<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

use InvalidArgumentException;

class BentukKehadiran
{
    const HADIR_ONLINE = 'D';
    const HADIR_OFFLINE = 'L';
    
    private string $kehadiran;

    public function __construct(string $kehadiran)
    {
        if ($kehadiran != self::HADIR_ONLINE & 
            $kehadiran != self::HADIR_OFFLINE) {
            throw new InvalidArgumentException('bentuk_kehadiran_tidak_sesuai');
        }

        $this->kehadiran = $kehadiran;
    }

    public function getKehadiran(): string
    {
        return $this->kehadiran;
    }

    public function isOnline(): bool
    {
        if ($this->kehadiran === self::HADIR_ONLINE) {
            return true;
        }

        return false;
    }

    public function isOffline(): bool
    {
        if ($this->kehadiran === self::HADIR_OFFLINE) {
            return true;
        }

        return false;
    }

}