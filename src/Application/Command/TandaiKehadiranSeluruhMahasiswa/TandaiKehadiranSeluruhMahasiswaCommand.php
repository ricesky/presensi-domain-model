<?php

declare(strict_types=1);

namespace App\Application\Command\TandaiKehadiranSeluruhMahasiswa;

use App\Application\Exception\ApplicationException;
use App\Domain\Entity\KehadiranMahasiswa;
use App\Domain\Entity\Mahasiswa;
use App\Domain\Repository\KehadiranMahasiswaRepositoryInterface;
use App\Domain\Repository\MahasiswaRepositoryInterface;
use App\Domain\Repository\PertemuanRepositoryInterface;
use App\Domain\ValueObject\JenisKehadiran;
use App\Domain\ValueObject\KehadiranMahasiswaId;
use App\Domain\ValueObject\PertemuanId;
use DateTime;

class TandaiKehadiranSeluruhMahasiswaCommand
{
    public function __construct(
        private PertemuanRepositoryInterface $pertemuanRepository,
        private MahasiswaRepositoryInterface $mahasiswaRepository,
        private KehadiranMahasiswaRepositoryInterface $kehadiranMahasiswaRepository
    ) { }

    public function execute(TandaiKehadiranSeluruhMahasiswaRequest $request): void
    {
        $pertemuanId = new PertemuanId($request->pertemuanId);
        $jenisKehadiran = new JenisKehadiran($request->jenisKehadiran);
        $pencatat = $request->pencatat;
    
        $pertemuan = $this->pertemuanRepository->byId($pertemuanId);

        if (!$pertemuan) {
            throw new ApplicationException('pertemuan_tidak_ditemukan');
        }

        $listMahasiswa = $this->mahasiswaRepository->byKelasId($pertemuan->getKelas()->getId());
        $listKehadiranMahasiswa = $this->kehadiranMahasiswaRepository->byPertemuanId($pertemuanId);

        foreach($listMahasiswa as $mahasiswa) {
            $kehadiran = $this->findKehadiranMahasiswa($listKehadiranMahasiswa, $mahasiswa);

            if ($kehadiran) {
                $kehadiran->ubah(
                    pertemuan: $pertemuan, 
                    jenisKehadiran: $jenisKehadiran,
                    pencatat: $pencatat
                );
    
                $this->kehadiranMahasiswaRepository->update($kehadiran);
            } else {
                $kehadiran = new KehadiranMahasiswa(
                    id: new KehadiranMahasiswaId($pertemuan->getId(), $mahasiswa->getId()),
                    jenisKehadiran: $jenisKehadiran,
                    waktuCatat: new DateTime('now'),
                    pencatat: $pencatat
                );

                $this->kehadiranMahasiswaRepository->save($kehadiran);
            }
        }

    }

    private function findKehadiranMahasiswa(array $listKehadiranMahasiswa, Mahasiswa $mahasiswa): ?KehadiranMahasiswa
    {
        foreach ($listKehadiranMahasiswa as $kehadiran) {
            if ($mahasiswa->getId()->equals($kehadiran->getId()->mahasiswaId())) {
                return $kehadiran;
            }
        }

        return null;
    }
}