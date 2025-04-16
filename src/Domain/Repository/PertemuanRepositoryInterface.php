<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\Pertemuan;
use App\Domain\ValueObject\KelasId;
use App\Domain\ValueObject\PertemuanId;

interface PertemuanRepositoryInterface
{
    public function byId(PertemuanId $pertemuanId) : ?Pertemuan;
    public function byKelasId(KelasId $kelasId) : array;
    public function save(Pertemuan $pertemuan) : void;
    public function update(Pertemuan $pertemuan) : void;
}
