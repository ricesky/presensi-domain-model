<?php

declare(strict_types=1);

namespace App\Application\Command\UbahPertemuan;

class UbahPertemuanRequest
{
    public function __construct(
        public string $kelasId,
        public string $pertemuanId,
        public int $pertemuanKe,
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
