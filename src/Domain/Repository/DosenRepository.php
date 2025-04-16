<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\Dosen;
use App\Domain\ValueObject\DosenId;

interface DosenRepository
{
    public function byId(DosenId $id): ?Dosen;
}