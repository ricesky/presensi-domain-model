<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\ValueObject\JadwalTatapMuka;
use App\Domain\ValueObject\KelasId;
use App\Domain\ValueObject\KodePresensi;
use App\Domain\ValueObject\ModeTatapMuka;
use App\Domain\ValueObject\RuanganId;
use App\Domain\ValueObject\TatapMukaId;
use App\Domain\ValueObject\TopikPerkuliahan;

use InvalidArgumentException;

class TatapMuka
{
    private TatapMukaId $id;
    private KelasId $kelasId;
    private int $pertemuanKe;
    private ?RuanganId $ruanganId;
    private JadwalTatapMuka $jadwal;
    private ?TopikPerkuliahan $topik;
    private ModeTatapMuka $mode;
    private ?KodePresensi $kodePresensi;

    public function __construct(
        TatapMukaId $id,
        KelasId $kelasId,
        int $pertemuanKe,
        ?RuanganId $ruanganId = NULL,
        JadwalTatapMuka $jadwal,
        ?TopikPerkuliahan $topik = NULL,
        ModeTatapMuka $mode,
        ?KodePresensi $kodePresensi = NULL
    )
    {
        if ($pertemuanKe <= 0) {
            throw new InvalidArgumentException('urutan_harus_lebih_besar_dari_0');
        }

        if ($mode->getMode() == ModeTatapMuka::MODE_TATAP_MUKA_OFFLINE && $ruanganId == null) {
            throw new InvalidArgumentException('mode_tatap_muka_offline_harus_memiliki_ruangan');
        }

        if ($mode->getMode() == ModeTatapMuka::MODE_TATAP_MUKA_HYBRID && $ruanganId == null) {
            throw new InvalidArgumentException('mode_tatap_muka_hybrid_harus_memiliki_ruangan');
        }

        $this->id = $id;
        $this->kelasId = $kelasId;
        $this->pertemuanKe = $pertemuanKe;
        $this->ruanganId = $ruanganId;
        $this->jadwal = $jadwal;
        $this->topik = $topik;
        $this->mode = $mode;
        $this->kodePresensi = $kodePresensi;
    }

    public function getTatapMukaId(): TatapMukaId
    {
        return $this->id;
    }

    public function getKelasId(): KelasId
    {
        return $this->kelasId;
    }

    public function getPertemuanKe(): int
    {
        return $this->pertemuanKe;
    }

    public function getRuanganId(): ?RuanganId
    {
        return $this->ruanganId;
    }

    public function getJadwal(): JadwalTatapMuka
    {
        return $this->jadwal;
    }

    public function getTopik(): ?TopikPerkuliahan
    {
        return $this->topik;
    }

    public function getMode(): ModeTatapMuka
    {
        return $this->mode;
    }

    public function getKodePresensi(): ?KodePresensi
    {
        return $this->kodePresensi;
    }

    public function setKodePresensi(KodePresensi $kodePresensi) : void
    {
        $this->kodePresensi = $kodePresensi;
    }

}
