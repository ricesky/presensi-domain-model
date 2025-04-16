<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

use InvalidArgumentException;

class ModePertemuan
{
    const ONLINE = 'D';
    const OFFLINE = 'L';
    const HYBRID = 'H';
    
    private string $mode;

    public function __construct(string $mode)
    {
        if ($mode != self::ONLINE & 
            $mode != self::OFFLINE &
            $mode != self::HYBRID) {
            throw new InvalidArgumentException('mode_pertemuan_tidak_sesuai');
        }

        $this->mode = $mode;
    }

    public function getMode(): string
    {
        return $this->mode;
    }

    public function equals(ModePertemuan $modePertemuan): bool
    {
        return $this->mode === $modePertemuan->getMode();
    }

    public function isOnline(): bool
    {
        return $this->mode === self::ONLINE;
    }

    public function isOffline(): bool
    {
        return $this->mode === self::OFFLINE;
    }

    public function isHybrid(): bool
    {
        return $this->mode === self::HYBRID;
    }

    public static function online(): ModePertemuan
    {
        return new ModePertemuan(self::ONLINE);
    }

    public static function offline(): ModePertemuan
    {
        return new ModePertemuan(self::OFFLINE);
    }

    public static function hybrid(): ModePertemuan
    {
        return new ModePertemuan(self::HYBRID);
    }

}