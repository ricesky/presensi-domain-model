<?php

declare(strict_types=1);

namespace Tests\Domain\ValueObject;

use PHPUnit\Framework\TestCase;
use App\Domain\ValueObject\JadwalPertemuan;
use DateTime;
use InvalidArgumentException;

final class JadwalPertemuanTest extends TestCase
{
    public function testCanCreateJadwalPertemuanSuccessfully(): void
    {
        $tanggal = new DateTime('2024-10-01');
        $jamMulai = new DateTime('1900-01-01 08:00:00');
        $jamSelesai = new DateTime('1900-01-01 10:00:00');

        $jadwal = new JadwalPertemuan($tanggal, $jamMulai, $jamSelesai);

        $this->assertEquals('2024-10-01', $jadwal->getTanggal()->format('Y-m-d'));
        $this->assertEquals('08:00:00', $jadwal->getJamMulai()->format('H:i:s'));
        $this->assertEquals('10:00:00', $jadwal->getJamSelesai()->format('H:i:s'));
    }

    public function testThrowsExceptionWhenJamMulaiIsAfterJamSelesai(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('jam_mulai_tidak_boleh_melebihi_jam_selesai');

        $tanggal = new DateTime('2024-10-01');
        $jamMulai = new DateTime('1900-01-01 11:00:00');
        $jamSelesai = new DateTime('1900-01-01 10:00:00'); // jam mulai after jam selesai

        new JadwalPertemuan($tanggal, $jamMulai, $jamSelesai);
    }

    public function testAllowsDefaultDateTime1900Scenario(): void
    {
        // Default DB format workaround
        $tanggal = new DateTime('2024-10-01');
        $jamMulai = new DateTime('1900-01-01 08:00:00');
        $jamSelesai = new DateTime('1900-01-01 10:00:00');

        $jadwal = new JadwalPertemuan($tanggal, $jamMulai, $jamSelesai);

        $this->assertInstanceOf(JadwalPertemuan::class, $jadwal);
    }
}
