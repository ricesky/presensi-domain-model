<?php

declare(strict_types=1);

namespace Tests\Domain\Entity;

use PHPUnit\Framework\TestCase;
use App\Domain\Entity\KehadiranDosen;
use App\Domain\Entity\Pertemuan;
use App\Domain\Entity\Kelas;
use App\Domain\ValueObject\{KehadiranDosenId, DosenId, PertemuanId, UrutanPertemuan, RuanganId,
    JadwalPertemuan, TopikPerkuliahan, ModePertemuan, StatusPertemuan, KelasId, BentukKehadiran};
use App\Domain\Exception\KehadiranDosenException;
use DateTime;
use DateInterval;
use Ramsey\Uuid\Uuid;

class KehadiranDosenTest extends TestCase
{
    private function makeKelas(bool $permanen = false): Kelas
    {
        return new Kelas(new KelasId(Uuid::uuid4()->toString()), 16, $permanen);
    }

    private function makePertemuan(StatusPertemuan $status, bool $permanen = false): Pertemuan
    {
        $tanggal = new DateTime();
        $mulai = (new DateTime())->sub(new DateInterval('PT1H'));
        $selesai = (clone $mulai)->add(new DateInterval('PT2H'));
        $jadwal = new JadwalPertemuan($tanggal, $mulai, $selesai);

        return new Pertemuan(
            new PertemuanId(null),
            $this->makeKelas($permanen),
            new UrutanPertemuan(1),
            null,
            $jadwal,
            new TopikPerkuliahan(str_repeat('a', 20), null),
            new ModePertemuan('D'),
            $status
        );
    }

    public function testHadirSuccess(): void
    {
        $pertemuan = $this->makePertemuan(StatusPertemuan::sedangBerlangsung());
        $dosenId = new DosenId(Uuid::uuid4()->toString());
        $jamMulai = new DateTime();
        $bentuk = new BentukKehadiran('D');

        $kehadiran = KehadiranDosen::hadir($pertemuan, $dosenId, $jamMulai, $bentuk);

        $this->assertNotNull($kehadiran);
        $this->assertEquals($jamMulai->format('H:i'), $kehadiran->getJamMulai()->format('H:i'));
        $this->assertNull($kehadiran->getJamSelesai());
        $this->assertFalse($kehadiran->isLupaPresensi());
    }

    public function testHadirFailsIfKelasPermanen(): void
    {
        $this->expectException(KehadiranDosenException::class);
        $pertemuan = $this->makePertemuan(StatusPertemuan::sedangBerlangsung(), true);
        KehadiranDosen::hadir($pertemuan, new DosenId(Uuid::uuid4()->toString()), new DateTime(), new BentukKehadiran('D'));
    }

    public function testHadirFailsIfStatusInvalid(): void
    {
        $this->expectException(KehadiranDosenException::class);
        $pertemuan = $this->makePertemuan(StatusPertemuan::belumDimulai());
        KehadiranDosen::hadir($pertemuan, new DosenId(Uuid::uuid4()->toString()), new DateTime(), new BentukKehadiran('D'));
    }

    public function testSelesaiSuccess(): void
    {
        $pertemuan = $this->makePertemuan(StatusPertemuan::sedangBerlangsung());
        $dosenId = new DosenId(Uuid::uuid4()->toString());
        $kehadiran = KehadiranDosen::hadir($pertemuan, $dosenId, new DateTime(), new BentukKehadiran('D'));
        $kehadiran->selesai();

        $this->assertNotNull($kehadiran->getJamSelesai());
    }

    public function testSelesaiFailsIfJamMulaiIsNull(): void
    {
        $this->expectException(KehadiranDosenException::class);
        $id = new KehadiranDosenId(new PertemuanId(null), new DosenId(Uuid::uuid4()->toString()));
        $kehadiran = new KehadiranDosen($id, null, null, false, new BentukKehadiran('D'));
        $kehadiran->selesai();
    }

    public function testLupaSuccess(): void
    {
        $pertemuan = $this->makePertemuan(StatusPertemuan::belumDimulai());
        $dosenId = new DosenId(Uuid::uuid4()->toString());
        $mulai = new DateTime('08:00');
        $selesai = new DateTime('10:00');
        $bentuk = new BentukKehadiran('L');

        $kehadiran = KehadiranDosen::lupa($pertemuan, $dosenId, $mulai, $selesai, $bentuk);

        $this->assertTrue($kehadiran->isLupaPresensi());
        $this->assertNotNull($kehadiran->getJamMulai());
        $this->assertNotNull($kehadiran->getJamSelesai());
    }

    public function testLupaFailsIfJamMulaiAfterJamSelesai(): void
    {
        $this->expectException(KehadiranDosenException::class);
        $pertemuan = $this->makePertemuan(StatusPertemuan::belumDimulai());
        $dosenId = new DosenId(Uuid::uuid4()->toString());
        $mulai = new DateTime('11:00');
        $selesai = new DateTime('10:00');

        KehadiranDosen::lupa($pertemuan, $dosenId, $mulai, $selesai, new BentukKehadiran('L'));
    }

    public function testLupaFailsIfStatusInvalid(): void
    {
        $this->expectException(KehadiranDosenException::class);
        $pertemuan = $this->makePertemuan(StatusPertemuan::selesai());
        $dosenId = new DosenId(Uuid::uuid4()->toString());
        KehadiranDosen::lupa($pertemuan, $dosenId, new DateTime('08:00'), new DateTime('10:00'), new BentukKehadiran('D'));
    }

    public function testLupaFailsIfKelasPermanen(): void
    {
        $this->expectException(KehadiranDosenException::class);
        $pertemuan = $this->makePertemuan(StatusPertemuan::belumDimulai(), true);
        $dosenId = new DosenId(Uuid::uuid4()->toString());
        KehadiranDosen::lupa($pertemuan, $dosenId, new DateTime('08:00'), new DateTime('10:00'), new BentukKehadiran('D'));
    }
}
