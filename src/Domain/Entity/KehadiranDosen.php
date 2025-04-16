<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Exception\KehadiranDosenException;
use App\Domain\ValueObject\BentukKehadiran;
use App\Domain\ValueObject\DosenId;
use App\Domain\ValueObject\KehadiranDosenId;
use DateTime;

class KehadiranDosen
{   
    private KehadiranDosenId $id;
    private ?DateTime $jamMulai;
    private ?DateTime $jamSelesai;
    private bool $lupaPresensi;
    private ?BentukKehadiran $bentukHadir;

    public function getId(): KehadiranDosenId
    {
        return $this->id;
    }

    public function getJamMulai(): ?DateTime
    {
        return $this->jamMulai;
    }

    public function getJamSelesai(): ?DateTime
    {
        return $this->jamSelesai;
    }

    public function isLupaPresensi(): bool
    {
        return $this->lupaPresensi;
    }

    public function getBentukHadir(): ?BentukKehadiran
    {
        return $this->bentukHadir;
    }

    public function __construct(
        KehadiranDosenId $id,
        ?DateTime $jamMulai,
        ?DateTime $jamSelesai,
        bool $lupaPresensi,
        ?BentukKehadiran $bentukHadir
    )
    {
        $this->id = $id;
        $this->jamMulai = $jamMulai;
        $this->jamSelesai = $jamSelesai;
        $this->lupaPresensi = $lupaPresensi;
        $this->bentukHadir = $bentukHadir;
    }

    public static function hadir(
        Pertemuan $pertemuan,
        DosenId $dosenId, 
        DateTime $jamMulai,
        BentukKehadiran $bentukHadir): KehadiranDosen
    {
        if ($pertemuan->getKelas()->isPermanen()) {
            throw new KehadiranDosenException('tidak_dapat_mencatatkan_kehadiran_dosen_karena_nilai_kelas_sudah_permanen');
        }

        if (!$pertemuan->getStatus()->isSedangBerlangsung()) {
            throw new KehadiranDosenException('pencatatan_kehadiran_dosen_dapat_dilakukan_ketika_kelas_sedang_berlangsung');
        }

        $kehadiranDosen = new KehadiranDosen(
            id: new KehadiranDosenId($pertemuan->getId(), $dosenId),
            jamMulai: $jamMulai,
            jamSelesai: null,
            lupaPresensi: false,
            bentukHadir: $bentukHadir
        );

        return $kehadiranDosen;
    }

    public function selesai(): void
    {
        if (is_null($this->jamMulai)) {
            throw new KehadiranDosenException('waktu_mulai_belum_tercatat');
        }

        $this->jamSelesai = new DateTime('now');
        $this->lupaPresensi = false;
    }

    public static function lupa(
        Pertemuan $pertemuan,
        DosenId $dosenId,
        DateTime $jamMulai,
        DateTime $jamSelesai,
        BentukKehadiran $bentukKehadiran
    ): KehadiranDosen
    {
        if ($pertemuan->getKelas()->isPermanen()) {
            throw new KehadiranDosenException('tidak_dapat_mencatatkan_kehadiran_dosen_karena_nilai_kelas_sudah_permanen');
        }
        
        if ($pertemuan->getStatus()->isSedangBerlangsung() || $pertemuan->getStatus()->isSelesai()) {
            throw new KehadiranDosenException('tidak_dapat_menandai_lupa_presensi_pada_pertemuan_yang_sedang_berlangsung_atau_selesai');
        }

        $tanggalRencanaPertemuan = $pertemuan->getJadwal()->getTanggal();

        $jamMulai->setDate(
            intval($tanggalRencanaPertemuan->format('Y')), 
            intval($tanggalRencanaPertemuan->format('m')), 
            intval($tanggalRencanaPertemuan->format('d'))
        );

        $jamSelesai->setDate(
            intval($tanggalRencanaPertemuan->format('Y')), 
            intval($tanggalRencanaPertemuan->format('m')), 
            intval($tanggalRencanaPertemuan->format('d'))
        );

        if ($jamMulai > $jamSelesai) {
            throw new KehadiranDosenException('realisasi_jam_mulai_tidak_boleh_lebih_besar_dari_jam_selesai');
        }

        $kehadiranDosen = new KehadiranDosen(
            id: new KehadiranDosenId($pertemuan->getId(), $dosenId),
            jamMulai: $jamMulai,
            jamSelesai: $jamSelesai,
            lupaPresensi: true,
            bentukHadir: $bentukKehadiran
        );

        return $kehadiranDosen;
    } 
    
}