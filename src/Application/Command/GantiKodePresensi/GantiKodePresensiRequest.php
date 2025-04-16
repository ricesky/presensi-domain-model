<?php

declare(strict_types=1);

namespace App\Application\Command\GantiKodePresensi;

class GantiKodePresensiRequest
{
    public function __construct(
       public string $pertemuanId
    )
    { }
    
}