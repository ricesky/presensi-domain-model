<?php

declare(strict_types=1);

namespace App\Application\Command\LupaPresensi;

use DateTime;

class LupaPresensiRequest
{
    public function __construct(
       public string $pertemuanId,
       public string $dosenId,
       public string $modePertemuan,
       public string $bentukKehadiran,
       public DateTime $jamMulai,
       public DateTime $jamSelesai
    )
    { }
    
}