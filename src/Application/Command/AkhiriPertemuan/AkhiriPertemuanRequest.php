<?php

declare(strict_types=1);

namespace App\Application\Command\AkhiriPertemuan;

class AkhiriPertemuanRequest
{
    public function __construct(
        public string $pertemuanId,
        public string $dosenId
    )
    { }
    
}