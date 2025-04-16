<?php

declare(strict_types=1);

namespace App\Application\Command\GantiKodePresensi;

use App\Application\Exception\ApplicationException;
use App\Domain\Exception\PertemuanException;
use App\Domain\Repository\PertemuanRepositoryInterface;
use App\Domain\ValueObject\PertemuanId;
use InvalidArgumentException;

class GantiKodePresensiCommand
{
    public function __construct(
        private PertemuanRepositoryInterface $pertemuanRepository
    ) { }

    public function execute(GantiKodePresensiRequest $request): void
    {
        $pertemuanId = new PertemuanId($request->pertemuanId);

        $pertemuan = $this->pertemuanRepository->byId($pertemuanId);

        if (!$pertemuan) {
            throw new InvalidArgumentException('pertemuan_tidak_ditemukan');
        }

        try {
            $pertemuan->gantiKodePresensi();

            $this->pertemuanRepository->update($pertemuan);
        } catch (PertemuanException $e) {
            throw new ApplicationException($e->getMessage());
        }
    }
}