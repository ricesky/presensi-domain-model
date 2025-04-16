<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Exception\PertemuanException;
use App\Domain\ValueObject\BentukKehadiran;
use App\Domain\ValueObject\JadwalPertemuan;
use App\Domain\ValueObject\KodePresensi;
use App\Domain\ValueObject\ModePertemuan;
use App\Domain\ValueObject\PertemuanId;
use App\Domain\ValueObject\RuanganId;
use App\Domain\ValueObject\StatusPertemuan;
use App\Domain\ValueObject\TopikPerkuliahan;
use App\Domain\ValueObject\UrutanPertemuan;
use DateTime;

class Pertemuan
{
    private const JEDA_PRESENSI = '30';
    private const MINIMAL_MASA_BERLAKU_KODE_PRESENSI = '15';

    private PertemuanId $id;
    private Kelas $kelas;
    private UrutanPertemuan $pertemuanKe;
    private ?RuanganId $ruanganId;
    private JadwalPertemuan $jadwal;
    private ?TopikPerkuliahan $topik;
    private ModePertemuan $mode;
    private StatusPertemuan $status;
    private ?KodePresensi $kodePresensi;

    public function __construct(
        PertemuanId $id,
        Kelas $kelas,
        UrutanPertemuan $pertemuanKe,
        ?RuanganId $ruanganId = null,
        JadwalPertemuan $jadwal,
        ?TopikPerkuliahan $topik = null,
        ModePertemuan $mode,
        ?StatusPertemuan $status = null,
        ?KodePresensi $kodePresensi = null
    )
    {
        $this->id = $id;
        $this->kelas = $kelas;
        $this->pertemuanKe = $pertemuanKe;
        $this->ruanganId = $ruanganId;
        $this->jadwal = $jadwal;
        $this->topik = $topik;
        $this->mode = $mode;
        $this->kodePresensi = $kodePresensi;

        if (!$status) {
            $now = new DateTime('now');

            if ($now < $this->jadwal->getJamSelesai() && is_null($this->kodePresensi)) {
                $this->status = StatusPertemuan::belumDimulai();
            } elseif ($now > $this->jadwal->getJamSelesai() && is_null($this->kodePresensi)) {
                $this->status = StatusPertemuan::terlewat();
            } elseif ($now > $this->jadwal->getJamSelesai() && !is_null($this->kodePresensi)) {
                $this->status = StatusPertemuan::selesai();
            } else {
                $this->status = StatusPertemuan::sedangBerlangsung();
            }
        } else {
            $this->status = $status;
        }
    }

    public function getId(): PertemuanId
    {
        return $this->id;
    }

    public function getKelas(): Kelas
    {
        return $this->kelas;
    }

    public function getPertemuanKe(): UrutanPertemuan
    {
        return $this->pertemuanKe;
    }

    public function getRuanganId(): ?RuanganId
    {
        return $this->ruanganId;
    }

    public function getJadwal(): JadwalPertemuan
    {
        return $this->jadwal;
    }

    public function getTopik(): ?TopikPerkuliahan
    {
        return $this->topik;
    }

    public function getMode(): ModePertemuan
    {
        return $this->mode;
    }

    public function getStatus(): StatusPertemuan
    {
        return $this->status;
    }

    public function getKodePresensi(): ?KodePresensi
    {
        return $this->kodePresensi;
    }

    public function ubah(
        UrutanPertemuan $urutan,
        ?RuanganId $ruanganId,
        JadwalPertemuan $jadwal,
        TopikPerkuliahan $topik,
        ModePertemuan $mode
    ): void
    {
        if ($this->kelas->isPermanen()) {
            throw new PertemuanException('tidak_dapat_mengubah_pertemuan_karena_nilai_sudah_permanen');
        }

        if ($mode->isOffline() && $ruanganId == null) {
            throw new PertemuanException('mode_tatap_muka_offline_harus_memiliki_ruangan');
        }

        if ($mode->isHybrid() && $ruanganId == null) {
            throw new PertemuanException('mode_tatap_muka_hybrid_harus_memiliki_ruangan');
        }

        $this->pertemuanKe = $urutan;
        $this->ruanganId = $ruanganId;
        $this->jadwal = $jadwal;
        $this->topik = $topik;
        $this->mode = $mode;
    }

    public function mulai(
        ModePertemuan $modePertemuan,
        BentukKehadiran $bentukKehadiran,
        DateTime $waktuMulai,
        ?int $menitBerlaku): void
    {
        if ($this->kelas->isPermanen()) {
            throw new PertemuanException('tidak_dapat_memulai_pertemuan_karena_nilai_sudah_permanen');
        }

        if (!$this->isBolehMulaiPertemuan($waktuMulai)) {
            throw new PertemuanException('pertemuan_belum_boleh_dimulai');
        }

        if ($this->isTerlewat($waktuMulai)) {
            throw new PertemuanException('pertemuan_sudah_terlewati');
        }

        if ($modePertemuan->isOnline() && !$bentukKehadiran->isOnline()) {
            throw new PertemuanException('kehadiran_dosen_harus_online_untuk_mode_pertemuan_online');
        }

        if ($modePertemuan->isOffline() && !$bentukKehadiran->isOffline()) {
            throw new PertemuanException('kehadiran_dosen_harus_offline_untuk_mode_pertemuan_offline');
        }

        if ($menitBerlaku && $menitBerlaku < self::MINIMAL_MASA_BERLAKU_KODE_PRESENSI) {
            throw new PertemuanException('menit_berlaku_kode_presensi_tidak_boleh_kurang_dari_' 
                . self::MINIMAL_MASA_BERLAKU_KODE_PRESENSI . '_menit');
        }

        $kodePresensiBerlakuSampai = $this->jadwal->getJamSelesai();
        if ($menitBerlaku) {
            $jeda = new \DateInterval('PT' . $menitBerlaku . 'M');
            $jamMulai = $this->jadwal->getJamMulai();
            $kodePresensiBerlakuSampai = $jamMulai->add($jeda);
        }

        $this->kodePresensi = KodePresensi::generate($kodePresensiBerlakuSampai);
        $this->status = StatusPertemuan::sedangBerlangsung();
    }

    public function akhiri(): void
    {
        if ($this->kelas->isPermanen()) {
            throw new PertemuanException('tidak_dapat_mengakhiri_pertemuan_karena_nilai_sudah_permanen');
        }

        if ($this->status->isBelumDimulai()) {
            throw new PertemuanException('tidak_dapat_mengakhiri_pertemuan_yang_belum_dimulai');
        }

        if ($this->status->isTerlewat()) {
            throw new PertemuanException('tidak_dapat_mengakhiri_pertemuan_yang_terlewat');
        }

        $this->status = StatusPertemuan::selesai();
    }

    public function lupa(ModePertemuan $modePertemuan): void
    {
        if ($this->kelas->isPermanen()) {
            throw new PertemuanException('tidak_dapat_menandai_lupa_pertemuan_karena_nilai_sudah_permanen');
        }

        if ($this->getStatus()->isSedangBerlangsung() || $this->getStatus()->isSelesai()) {
            throw new PertemuanException('tidak_dapat_menandai_lupa_presensi_pada_pertemuan_yang_sedang_berlangsung_atau_selesai');
        }

        $this->status = StatusPertemuan::selesai();
        $this->mode = $modePertemuan;
    }

    public function gantiKodePresensi(): void
    {
        if ($this->kelas->isPermanen()) {
            throw new PertemuanException('tidak_dapat_mengubah_kode_presensi_karena_nilai_sudah_permanen');
        }

        if (!$this->status->isSedangBerlangsung()) {
            throw new PertemuanException('tidak_dapat_mengubah_kode_presensi_jika_pertemuan_belum_berlangsung');
        }

        if (!$this->kodePresensi) {
            throw new PertemuanException('pertemuan_belum_dimulai_sehingga_kode_presensi_tidak_ditemukan');
        }

        $this->kodePresensi = $this->kodePresensi->gantiKode();
    }

    private function isBolehMulaiPertemuan(DateTime $waktuSekarang): bool
    {
        $jeda = new \DateInterval('PT' . self::JEDA_PRESENSI . 'M');
        $jamMulai = $this->jadwal->getJamMulai();
        $jamAwalDiperbolehkan = $jamMulai->sub($jeda);

        if ($waktuSekarang >= $jamAwalDiperbolehkan) {
            return true;
        }

        return false;
    }

    private function isTerlewat(DateTime $waktuSekarang): bool
    {
        $jamSelesai = $this->jadwal->getJamSelesai();

        return $waktuSekarang > $jamSelesai;
    }

}
