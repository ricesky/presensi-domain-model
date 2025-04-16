<?php

declare(strict_types=1);

namespace App\Application\Command\MulaiPertemuan;

use App\Application\Exception\ApplicationException;
use App\Domain\Entity\KehadiranDosen;
use App\Domain\Exception\KehadiranDosenException;
use App\Domain\Exception\PertemuanException;
use App\Domain\Repository\DosenRepositoryInterface;
use App\Domain\Repository\KehadiranDosenRepositoryInterface;
use App\Domain\Repository\PertemuanRepositoryInterface;
use App\Domain\ValueObject\BentukKehadiran;
use App\Domain\ValueObject\DosenId;
use App\Domain\ValueObject\ModePertemuan;
use App\Domain\ValueObject\PertemuanId;
use DateTime;
use InvalidArgumentException;

class MulaiPertemuanCommand
{
    public function __construct(
        private PertemuanRepositoryInterface $pertemuanRepository,
        private DosenRepositoryInterface $dosenRepository,
        private KehadiranDosenRepositoryInterface $kehadiranDosenRepository
    ) { }

    public function execute(MulaiPertemuanRequest $request): void
    {
        $pertemuanId = new PertemuanId($request->pertemuanId);
        $dosenId = new DosenId($request->dosenId);

        $pertemuan = $this->pertemuanRepository->byId($pertemuanId);

        if (!$pertemuan) {
            throw new InvalidArgumentException('pertemuan_tidak_ditemukan');
        }

        $dosen = $this->dosenRepository->byId($dosenId);

        if (!$dosen) {
            throw new InvalidArgumentException('dosen_tidak_ditemukan');
        }

        $kehadiranDosen = $this->kehadiranDosenRepository->byPertemuanId($pertemuanId);

        if ($kehadiranDosen && !$kehadiranDosen->getId()->dosenId()->equals($dosenId)) {
            throw new ApplicationException('pertemuan_sudah_dihadiri_dosen_lain');
        }

        $waktuSekarang = new DateTime('now');

        try {
            
            $pertemuan->mulai(
                modePertemuan: new ModePertemuan($request->modePertemuan),
                bentukKehadiran: new BentukKehadiran($request->bentukKehadiran),
                waktuMulai: $waktuSekarang,
                menitBerlaku: $request->menitBerlaku 
            );

            $this->pertemuanRepository->update($pertemuan);

            $kehadiranDosen = KehadiranDosen::hadir(
                pertemuan: $pertemuan,
                dosenId: $dosenId,
                jamMulai: new DateTime('now'),
                bentukHadir: new BentukKehadiran($request->bentukKehadiran)
            );

            $this->kehadiranDosenRepository->save($kehadiranDosen);

        } catch(PertemuanException $e) {
            throw new ApplicationException($e->getMessage());
        } catch(KehadiranDosenException $e) {
            throw new ApplicationException($e->getMessage());
        }
    }
}