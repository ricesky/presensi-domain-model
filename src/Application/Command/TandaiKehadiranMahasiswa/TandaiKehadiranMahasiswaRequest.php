<?php

declare(strict_types=1);

namespace App\Application\Command\TandaiKehadiranMahasiswa;

class TandaiKehadiranMahasiswaRequest
{
    public function __construct(
        public string $pertemuanId,
        public string $mahasiswaId,
        public string $jenisKehadiran,
        public string $pencatat
    )
    { }
    
}