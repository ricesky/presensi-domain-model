<?php

declare(strict_types=1);

namespace App\Application\Command\TandaiKehadiranMahasiswa;

use App\Domain\Entity\KehadiranMahasiswa;
use App\Domain\Repository\KehadiranMahasiswaRepositoryInterface;
use App\Domain\Repository\PertemuanRepositoryInterface;
use App\Domain\ValueObject\JenisKehadiran;
use App\Domain\ValueObject\KehadiranMahasiswaId;
use App\Domain\ValueObject\MahasiswaId;
use App\Domain\ValueObject\PertemuanId;
use DateTime;
use InvalidArgumentException;

class TandaiKehadiranMahasiswaCommand
{
    public function __construct(
        private PertemuanRepositoryInterface $pertemuanRepository,
        private KehadiranMahasiswaRepositoryInterface $kehadiranMahasiswaRepository
    ) { }

    public function execute(TandaiKehadiranMahasiswaRequest $request): void
    {
        $pertemuanId = new PertemuanId($request->pertemuanId);
        $mahasiswaId = new MahasiswaId($request->mahasiswaId);
        $kehadiranMahasiswaId = new KehadiranMahasiswaId($pertemuanId, $mahasiswaId);
        $jenisKehadiran = new JenisKehadiran($request->jenisKehadiran);
        $pencatat = $request->pencatat;

        $pertemuan = $this->pertemuanRepository->byId($pertemuanId);

        if (!$pertemuan) {
            throw new InvalidArgumentException('pertemuan_tidak_ditemukan');
        }

        $kehadiranMahasiswa = $this->kehadiranMahasiswaRepository->byId($kehadiranMahasiswaId);

        if ($kehadiranMahasiswa) {
            $kehadiranMahasiswa->ubah(
                pertemuan: $pertemuan,
                jenisKehadiran: $jenisKehadiran,
                pencatat: $pencatat
            );

            $this->kehadiranMahasiswaRepository->update($kehadiranMahasiswa);
        } else {
            $kehadiranMahasiswa = new KehadiranMahasiswa(
                id: $kehadiranMahasiswaId,
                jenisKehadiran: $jenisKehadiran,
                waktuCatat: new DateTime('now'),
                pencatat: $pencatat
            );

            $this->kehadiranMahasiswaRepository->save($kehadiranMahasiswa);
        }
    }
}