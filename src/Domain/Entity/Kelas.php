<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Exception\KelasException;
use App\Domain\ValueObject\JadwalTatapMuka;
use App\Domain\ValueObject\KelasId;
use App\Domain\ValueObject\ModeTatapMuka;
use App\Domain\ValueObject\RuanganId;
use App\Domain\ValueObject\TatapMukaId;
use App\Domain\ValueObject\TopikPerkuliahan;

class Kelas
{
    private KelasId $id;
    private bool $nilaiFinal;

    public function __construct(KelasId $id, bool $nilaiFinal)
    {
        $this->id = $id;
        $this->nilaiFinal = $nilaiFinal;
    }

    public function getId() : KelasId
    {
        return $this->id;
    }

    public function isNilaiFinal(): bool
    {
        return $this->nilaiFinal;
    }

    public function buatTatapMuka(
        int $urutan,
        ?RuanganId $ruanganId,
        JadwalTatapMuka $jadwal,
        TopikPerkuliahan $topik,
        ModeTatapMuka $mode) : TatapMuka
    {
        if ($this->isNilaiFinal()) {
            throw new KelasException('tidak_dapat_membuat_tatap_muka_baru_karena_nilai_sudah_final');
        }

        return new TatapMuka(
            new TatapMukaId(),
            $this->id,
            $urutan,
            $ruanganId,
            $jadwal,
            $topik,
            $mode,
            null
        );
    }

    public function ubahTatapMuka(
        TatapMukaId $tatapMukaId,
        int $urutan,
        ?RuanganId $ruanganId,
        JadwalTatapMuka $jadwal,
        TopikPerkuliahan $topik,
        ModeTatapMuka $mode) : TatapMuka
    {

        if ($this->isNilaiFinal()) {
            throw new KelasException('tidak_dapat_mengubah_tatap_muka_baru_karena_nilai_sudah_final');
        }

        return new TatapMuka(
            $tatapMukaId,
            $this->id,
            $urutan,
            $ruanganId,
            $jadwal,
            $topik,
            $mode,
            null
        );
    }
}
