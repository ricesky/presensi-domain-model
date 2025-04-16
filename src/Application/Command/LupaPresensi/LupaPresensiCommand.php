<?php

declare(strict_types=1);

namespace App\Application\Command\LupaPresensi;

use App\Domain\Entity\KehadiranDosen;
use App\Domain\Repository\KehadiranDosenRepositoryInterface;
use App\Domain\Repository\PertemuanRepositoryInterface;
use App\Domain\ValueObject\BentukKehadiran;
use App\Domain\ValueObject\DosenId;
use App\Domain\ValueObject\ModePertemuan;
use App\Domain\ValueObject\PertemuanId;
use InvalidArgumentException;

class LupaPresensiCommand
{
    public function __construct(
        private PertemuanRepositoryInterface $pertemuanRepository,
        private KehadiranDosenRepositoryInterface $kehadiranDosenRepository
    ) { }

    public function execute(LupaPresensiRequest $request): void
    {
        $pertemuanId = new PertemuanId($request->pertemuanId);
        $dosenId = new DosenId($request->dosenId);

        $pertemuan = $this->pertemuanRepository->byId($pertemuanId);

        if (!$pertemuan) {
            throw new InvalidArgumentException('pertemuan_tidak_ditemukan');
        }

        $kehadiranDosen = KehadiranDosen::lupa(
            pertemuan: $pertemuan,
            dosenId: $dosenId,
            jamMulai: $request->jamMulai,
            jamSelesai: $request->jamSelesai,
            bentukKehadiran: new BentukKehadiran($request->bentukKehadiran)
        );

        $this->kehadiranDosenRepository->save($kehadiranDosen);

        $pertemuan->lupa(new ModePertemuan($request->modePertemuan));

        $this->pertemuanRepository->update($pertemuan);
    }
}