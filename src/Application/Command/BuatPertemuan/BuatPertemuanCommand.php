<?php

declare(strict_types=1);

namespace App\Application\Command\BuatPertemuan;

use App\Application\Exception\ApplicationException;
use App\Domain\Exception\KelasException;
use App\Domain\Exception\TopikPerkuliahanException;
use App\Domain\Repository\KelasRepositoryInterface;
use App\Domain\Repository\PertemuanRepositoryInterface;
use App\Domain\Service\UrutanPertemuanService;
use App\Domain\ValueObject\JadwalPertemuan;
use App\Domain\ValueObject\KelasId;
use App\Domain\ValueObject\ModePertemuan;
use App\Domain\ValueObject\RuanganId;
use App\Domain\ValueObject\TopikPerkuliahan;
use App\Domain\ValueObject\UrutanPertemuan;
use DateTime;
use InvalidArgumentException;

class BuatPertemuanCommand
{
    public function __construct(
        private KelasRepositoryInterface $kelasRepository,
        private PertemuanRepositoryInterface $pertemuanRepository
    ) { }

    public function execute(BuatPertemuanRequest $request): void
    {
        $kelasId = new KelasId($request->kelasId);
        $kelas = $this->kelasRepository->byId($kelasId);
        
        if (!$kelas) {
            throw new InvalidArgumentException('kelas_tidak_ditemukan');
        }
        
        $ruanganId = null;
        if ($request->ruanganId) {
            $ruanganId = new RuanganId($request->ruanganId);
        }

        $jadwal = new JadwalPertemuan(
            new DateTime($request->tanggal),
            new DateTime($request->jamMulai),
            new DateTime($request->jamSelesai)
        );

        $urutan = new UrutanPertemuan(intval($request->pertemuanKe));
        $listPertemuan = $this->pertemuanRepository->byKelasId($kelasId);

        $urutanPertemuanService = new UrutanPertemuanService();
        if ($urutanPertemuanService->isUrutanTerpakai($urutan, $listPertemuan)) {
            throw new ApplicationException('urutan_pertemuan_sudah_terpakai');
        }

        $topik = new TopikPerkuliahan($request->topik, $request->topikEn);
        $mode = new ModePertemuan($request->mode);

        try {
            $pertemuan = $kelas->buatPertemuan(
                urutan: $urutan,
                ruanganId: $ruanganId,
                jadwal: $jadwal,
                topik: $topik,
                mode: $mode
            );
    
            $this->pertemuanRepository->save($pertemuan);
        } catch (KelasException $e) {
            throw new ApplicationException($e->getMessage());
        } catch (TopikPerkuliahanException $e) {
            throw new ApplicationException($e->getMessage());
        } 
        
    }
}