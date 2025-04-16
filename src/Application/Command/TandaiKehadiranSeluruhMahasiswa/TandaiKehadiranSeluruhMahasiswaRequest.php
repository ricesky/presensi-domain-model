<?php

declare(strict_types=1);

namespace App\Application\Command\TandaiKehadiranSeluruhMahasiswa;

class TandaiKehadiranSeluruhMahasiswaRequest
{
    public function __construct(
        public string $pertemuanId,
        public string $jenisKehadiran,
        public string $pencatat
    )
    { }
    
}