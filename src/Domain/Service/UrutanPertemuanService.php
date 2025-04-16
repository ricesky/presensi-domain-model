<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\Entity\Pertemuan;
use App\Domain\ValueObject\UrutanPertemuan;

class UrutanPertemuanService
{
    public function isUrutanTerpakai(
        UrutanPertemuan $urutan, 
        array $listPertemuan, 
        ?Pertemuan $pertemuanDiabaikan = null): bool
    {
        foreach($listPertemuan as $pertemuan) {
            if ($pertemuanDiabaikan && $pertemuanDiabaikan->getId()->equals($pertemuan->getId())) {
                continue;
            }
            
            if ($pertemuan->getPertemuanKe()->equals($urutan)) {
                return true;
            }
        }

        return false;
    }
}