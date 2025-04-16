<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

use InvalidArgumentException;

class ModeTatapMuka
{
    const MODE_TATAP_MUKA_ONLINE = 'D';
    const MODE_TATAP_MUKA_OFFLINE = 'L';
    const MODE_TATAP_MUKA_HYBRID = 'H';
    
    private string $mode;

    public function __construct(string $mode)
    {
        if ($mode != self::MODE_TATAP_MUKA_ONLINE & 
            $mode != self::MODE_TATAP_MUKA_OFFLINE &
            $mode != self::MODE_TATAP_MUKA_HYBRID) {
            throw new InvalidArgumentException('mode_tatap_muka_tidak_sesuai');
        }

        $this->mode = $mode;
    }

    public function getMode(): string
    {
        return $this->mode;
    }

    public function equals(ModeTatapMuka $modeTatapMuka): bool
    {
        return $this->mode === $modeTatapMuka->getMode();
    }

}