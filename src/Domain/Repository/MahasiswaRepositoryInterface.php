<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\ValueObject\KelasId;

interface MahasiswaRepositoryInterface
{
    public function byKelasId(KelasId $kelasId): array;
}