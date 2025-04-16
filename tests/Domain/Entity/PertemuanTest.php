<?php

declare(strict_types=1);

namespace Tests\Domain\Entity;

use App\Domain\Entity\Pertemuan;
use App\Domain\Entity\Kelas;
use App\Domain\ValueObject\PertemuanId;
use App\Domain\ValueObject\UrutanPertemuan;
use App\Domain\ValueObject\JadwalPertemuan;
use App\Domain\ValueObject\TopikPerkuliahan;
use App\Domain\ValueObject\ModePertemuan;
use App\Domain\ValueObject\StatusPertemuan;
use App\Domain\ValueObject\BentukKehadiran;
use App\Domain\ValueObject\KelasId;
use App\Domain\Exception\PertemuanException;
use DateTime;
use DateInterval;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

final class PertemuanTest extends TestCase
{
    private function makeKelas(bool $permanen = false): Kelas
    {
        return new Kelas(
            new KelasId(Uuid::uuid4()->toString()),
            16,
            $permanen
        );
    }

    private function makeJadwalForMulaiTest(): JadwalPertemuan
    {
        $tanggal = new DateTime();
        $jamMulai = (new DateTime())->sub(new DateInterval('PT31M')); // Mulai 31 menit yang lalu
        $jamSelesai = (clone $jamMulai)->add(new DateInterval('PT2H'));
        return new JadwalPertemuan($tanggal, $jamMulai, $jamSelesai);
    }

    private function makeUrutan(): UrutanPertemuan
    {
        return new UrutanPertemuan(1);
    }

    private function makeTopik(): TopikPerkuliahan
    {
        return new TopikPerkuliahan(str_repeat('a', 20), str_repeat('b', 20));
    }

    private function makeMode(string $short = 'D'): ModePertemuan
    {
        return new ModePertemuan($short);
    }

    private function makeKehadiran(string $short = 'D'): BentukKehadiran
    {
        return new BentukKehadiran($short);
    }

    public function testMulaiSuccessfullyChangesStatusAndCreatesKodePresensi(): void
    {
        $jadwal = $this->makeJadwalForMulaiTest();
        $waktuMulai = new DateTime(); // current time, valid due to jadwal set above

        $pertemuan = new Pertemuan(
            new PertemuanId(null),
            $this->makeKelas(),
            $this->makeUrutan(),
            null,
            $jadwal,
            $this->makeTopik(),
            $this->makeMode('D') // Online
        );

        $pertemuan->mulai(
            $this->makeMode('D'),       // Online mode
            $this->makeKehadiran('D'),  // Online presence
            $waktuMulai,
            30                          // valid duration
        );

        $this->assertNotNull($pertemuan->getKodePresensi());
        $this->assertEquals(
            StatusPertemuan::sedangBerlangsung()->getStatus(),
            $pertemuan->getStatus()->getStatus()
        );
    }

    public function testThrowsExceptionWhenMulaiTooEarly(): void
    {
        $jadwal = $this->makeJadwalForMulaiTest();
        $waktuTerlaluCepat = (clone $jadwal->getJamMulai())->sub(new DateInterval('PT31M')); // 61 minutes before start

        $pertemuan = new Pertemuan(
            new PertemuanId(null),
            $this->makeKelas(),
            $this->makeUrutan(),
            null,
            $jadwal,
            $this->makeTopik(),
            $this->makeMode('D')
        );

        $this->expectException(PertemuanException::class);
        $this->expectExceptionMessage('pertemuan_belum_boleh_dimulai');

        $pertemuan->mulai(
            $this->makeMode('D'),
            $this->makeKehadiran('D'),
            $waktuTerlaluCepat,
            30
        );
    }

    public function testThrowsExceptionWhenDurasiTerlaluPendek(): void
    {
        $jadwal = $this->makeJadwalForMulaiTest();
        $waktuValid = new DateTime();

        $pertemuan = new Pertemuan(
            new PertemuanId(null),
            $this->makeKelas(),
            $this->makeUrutan(),
            null,
            $jadwal,
            $this->makeTopik(),
            $this->makeMode('D')
        );

        $this->expectException(PertemuanException::class);
        $this->expectExceptionMessage('menit_berlaku_kode_presensi_tidak_boleh_kurang_dari_15_menit');

        $pertemuan->mulai(
            $this->makeMode('D'),
            $this->makeKehadiran('D'),
            $waktuValid,
            5 // too short
        );
    }
}
