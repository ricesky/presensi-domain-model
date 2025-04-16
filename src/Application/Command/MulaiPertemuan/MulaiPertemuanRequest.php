<?php

declare(strict_types=1);

namespace App\Application\Command\MulaiPertemuan;

class MulaiPertemuanRequest
{
    public function __construct(
       public string $pertemuanId,
       public string $dosenId,
       public string $modePertemuan,
       public string $bentukKehadiran,
       public ?int $menitBerlaku = NULL
    )
    { }
    
}