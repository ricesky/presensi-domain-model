<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\Kelas;
use App\Domain\ValueObject\KelasId;

interface KelasRepository
{
    public function byId(KelasId $id): ?Kelas;
}