<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\KehadiranMahasiswa;
use App\Domain\ValueObject\KehadiranMahasiswaId;
use App\Domain\ValueObject\PertemuanId;

interface KehadiranMahasiswaRepositoryInterface
{
    public function byId(KehadiranMahasiswaId $kehadiranMahasiswaId): ?KehadiranMahasiswa;
    public function byPertemuanId(PertemuanId $pertemuanId): array;
    public function save(KehadiranMahasiswa $kehadiranMahasiswa): void;
    public function update(KehadiranMahasiswa $kehadiranMahasiswa): void;
}