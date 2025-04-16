<?php

declare(strict_types=1);

namespace App\Application\Command\BuatPertemuan;

class BuatPertemuanRequest
{
    public function __construct(
        public string $kelasId,
        public string $pengajarId,
        public string $pertemuanKe,
        public ?string $ruanganId,
        public string $tanggal,
        public string $jamMulai,
        public string $jamSelesai,
        public string $topik,
        public string $topikEn,
        public string $mode
    )
    { }
    
}