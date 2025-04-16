<?php

declare(strict_types=1);

namespace App\Application\Command\AkhiriPertemuan;

use App\Application\Exception\ApplicationException;
use App\Domain\Exception\KehadiranDosenException;
use App\Domain\Exception\PertemuanException;
use App\Domain\Repository\DosenRepositoryInterface;
use App\Domain\Repository\KehadiranDosenRepositoryInterface;
use App\Domain\Repository\PertemuanRepositoryInterface;
use App\Domain\ValueObject\DosenId;
use App\Domain\ValueObject\KehadiranDosenId;
use App\Domain\ValueObject\PertemuanId;

use InvalidArgumentException;

class AkhiriPertemuanCommand
{
    public function __construct(
        private PertemuanRepositoryInterface $pertemuanRepository,
        private DosenRepositoryInterface $dosenRepository,
        private KehadiranDosenRepositoryInterface $kehadiranDosenRepository
    ) { }

    public function execute(AkhiriPertemuanRequest $request): void
    {
        $pertemuanId = new PertemuanId($request->pertemuanId);
        $dosenId = new DosenId($request->dosenId);
        $kehadiranDosenId = new KehadiranDosenId($pertemuanId, $dosenId);

        $pertemuan = $this->pertemuanRepository->byId($pertemuanId);

        if (!$pertemuan) {
            throw new InvalidArgumentException('pertemuan_tidak_ditemukan');
        }

        $dosen = $this->dosenRepository->byId($dosenId);

        if (!$dosen) {
            throw new InvalidArgumentException('dosen_tidak_ditemukan');
        }

        $kehadiranDosen = $this->kehadiranDosenRepository->byId($kehadiranDosenId);

        if (!$kehadiranDosen) {
            throw new ApplicationException('kehadiran_dosen_tidak_ditemukan');
        }

        try {
            
            $pertemuan->akhiri();

            $this->pertemuanRepository->update($pertemuan);

            $kehadiranDosen->selesai();

            $this->kehadiranDosenRepository->update($kehadiranDosen);

        } catch(PertemuanException $e) {
            throw new ApplicationException($e->getMessage());
        } catch(KehadiranDosenException $e) {
            throw new ApplicationException($e->getMessage());
        }
    }
}