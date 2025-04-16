<?php

declare(strict_types=1);

namespace App\Application\Command\UbahPertemuan;

use App\Application\Command\UbahPertemuan\UbahPertemuanRequest;
use App\Application\Exception\ApplicationException;
use App\Domain\Exception\PertemuanException;
use App\Domain\Repository\PertemuanRepositoryInterface;
use App\Domain\Service\UrutanPertemuanService;
use App\Domain\ValueObject\JadwalPertemuan;
use App\Domain\ValueObject\ModePertemuan;
use App\Domain\ValueObject\PertemuanId;
use App\Domain\ValueObject\RuanganId;
use App\Domain\ValueObject\TopikPerkuliahan;
use App\Domain\ValueObject\UrutanPertemuan;
use DateTime;

class UbahPertemuanCommand
{
    public function __construct(
        private PertemuanRepositoryInterface $pertemuanRepository
    ) { }

    public function execute(UbahPertemuanRequest $request): void
    {
        $pertemuan = $this->pertemuanRepository->byId(new PertemuanId($request->pertemuanId));

        if (!$pertemuan) {
            throw new ApplicationException('pertemuan_tidak_ditemukan');
        }

        $urutan = new UrutanPertemuan(intval($request->pertemuanKe));
        $listPertemuan = $this->pertemuanRepository->byKelasId($pertemuan->getKelas()->getId());

        $urutanPertemuanService = new UrutanPertemuanService();
        if ($urutanPertemuanService->isUrutanTerpakai($urutan, $listPertemuan, $pertemuan)) {
            throw new ApplicationException('urutan_pertemuan_sudah_terpakai');
        }

        try {
            $pertemuan->ubah(
                urutan: new UrutanPertemuan($request->pertemuanKe),
                ruanganId: $request->ruanganId ? new RuanganId($request->ruanganId) : null,
                jadwal: new JadwalPertemuan(
                    new DateTime($request->tanggal),
                    new DateTime($request->jamMulai),
                    new DateTime($request->jamSelesai)
                ),
                topik: new TopikPerkuliahan($request->topik, $request->topikEn),
                mode: new ModePertemuan($request->mode)
            );
    
            $this->pertemuanRepository->update($pertemuan);
        } catch(PertemuanException $e) {
            throw new ApplicationException($e->getMessage());
        }
    }
}