<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\KehadiranDosen;
use App\Domain\ValueObject\KehadiranDosenId;
use App\Domain\ValueObject\PertemuanId;

interface KehadiranDosenRepositoryInterface
{
    public function byId(KehadiranDosenId $kehadiranDosenId): ?KehadiranDosen;
    public function byPertemuanId(PertemuanId $pertemuanId): ?KehadiranDosen;
    public function save(KehadiranDosen $kehadiranDosen): void;
    public function update(KehadiranDosen $kehadiranDosen): void;
}