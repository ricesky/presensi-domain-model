<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\TatapMuka;
use App\Domain\ValueObject\KelasId;
use App\Domain\ValueObject\TatapMukaId;

interface TatapMukaRepository
{
    public function byId(TatapMukaId $id): ?TatapMuka;
    public function listPertemuanKeByKelasId(KelasId $id): ?array;
    public function save(TatapMuka $tatapMuka): void;
    public function update(TatapMuka $tatapMuka): void;
    public function delete(TatapMuka $tatapMuka): void;
}
