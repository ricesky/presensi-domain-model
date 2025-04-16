<?php

declare(strict_types=1);

namespace Tests\Domain\Entity;

use PHPUnit\Framework\TestCase;
use App\Domain\Entity\KehadiranMahasiswa;
use App\Domain\Entity\Pertemuan;
use App\Domain\Entity\Kelas;
use App\Domain\ValueObject\{KehadiranMahasiswaId, MahasiswaId, JenisKehadiran, PertemuanId, KelasId, UrutanPertemuan,
    JadwalPertemuan, TopikPerkuliahan, ModePertemuan, StatusPertemuan};
use App\Domain\Exception\KehadiranMahasiswaException;
use Ramsey\Uuid\Uuid;
use DateTime;
use DateInterval;

class KehadiranMahasiswaTest extends TestCase
{
    private function makeKelas(bool $permanen = false): Kelas
    {
        return new Kelas(new KelasId(Uuid::uuid4()->toString()), 16, $permanen);
    }

    private function makePertemuan(bool $permanen = false): Pertemuan
    {
        $tanggal = new DateTime();
        $jamMulai = (new DateTime())->sub(new DateInterval('PT1H'));
        $jamSelesai = (clone $jamMulai)->add(new DateInterval('PT2H'));
        $jadwal = new JadwalPertemuan($tanggal, $jamMulai, $jamSelesai);

        return new Pertemuan(
            new PertemuanId(),
            $this->makeKelas($permanen),
            new UrutanPertemuan(1),
            null,
            $jadwal,
            new TopikPerkuliahan(str_repeat('a', 20), null),
            new ModePertemuan('D'),
            StatusPertemuan::sedangBerlangsung()
        );
    }

    private function makeKehadiranMahasiswaId(): KehadiranMahasiswaId
    {
        return new KehadiranMahasiswaId(
            new PertemuanId(),
            new MahasiswaId(Uuid::uuid4()->toString())
        );
    }

    public function testCanCreateKehadiranMahasiswa(): void
    {
        $id = $this->makeKehadiranMahasiswaId();
        $jenis = JenisKehadiran::hadir();
        $waktu = new DateTime();
        $pencatat = 'admin';

        $kehadiran = new KehadiranMahasiswa($id, $jenis, $waktu, $pencatat);

        $this->assertSame($id, $kehadiran->getId());
        $this->assertSame($jenis, $kehadiran->getJenisKehadiran());
        $this->assertSame($waktu->format('Y-m-d H:i'), $kehadiran->getWaktuCatat()->format('Y-m-d H:i'));
        $this->assertSame($pencatat, $kehadiran->getPencatat());
    }

    public function testUbahKehadiranBerhasil(): void
    {
        $id = $this->makeKehadiranMahasiswaId();
        $kehadiran = new KehadiranMahasiswa($id, JenisKehadiran::izin(), null, null);

        $pertemuan = $this->makePertemuan(false);
        $jenisBaru = JenisKehadiran::hadir();
        $pencatat = 'admin';

        $kehadiran->ubah($pertemuan, $jenisBaru, $pencatat);

        $this->assertEquals('H', $kehadiran->getJenisKehadiran()->getKehadiran());
        $this->assertEquals($pencatat, $kehadiran->getPencatat());
        $this->assertNotNull($kehadiran->getWaktuCatat());
    }

    public function testUbahTidakMengubahJikaJenisSama(): void
    {
        $id = $this->makeKehadiranMahasiswaId();
        $jenis = JenisKehadiran::izin();
        $kehadiran = new KehadiranMahasiswa($id, $jenis, null, null);

        $pertemuan = $this->makePertemuan();
        $kehadiran->ubah($pertemuan, JenisKehadiran::izin(), 'admin');

        $this->assertNull($kehadiran->getWaktuCatat());
        $this->assertNull($kehadiran->getPencatat());
    }

    public function testUbahFailsIfKelasPermanen(): void
    {
        $this->expectException(KehadiranMahasiswaException::class);

        $id = $this->makeKehadiranMahasiswaId();
        $kehadiran = new KehadiranMahasiswa($id, JenisKehadiran::izin(), null, null);

        $pertemuan = $this->makePertemuan(true);
        $kehadiran->ubah($pertemuan, JenisKehadiran::hadir(), 'admin');
    }
}
