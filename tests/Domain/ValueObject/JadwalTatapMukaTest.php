<?php

declare(strict_types=1);

namespace Tests\Domain\ValueObject;

use App\Domain\ValueObject\JadwalTatapMuka;
use DateTime;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class JadwalTatapMukaTest extends TestCase
{
    public function test_dapat_diinstansiasi()
    {
        $tanggal = new DateTime('2021-08-20');
        $jamMulai = new DateTime('2021-08-20 08:00:00');
        $jamSelesai = new DateTime('2021-08-20 10:00:00');

        $jadwalTatapMuka = new JadwalTatapMuka($tanggal, $jamMulai, $jamSelesai);

        $this->assertEquals('2021-08-20', $jadwalTatapMuka->getTanggal());
        $this->assertEquals('2021-08-20 08:00:00', $jadwalTatapMuka->getJamMulai());
        $this->assertEquals('2021-08-20 10:00:00', $jadwalTatapMuka->getJamSelesai());
    }

    public function test_tanggal_berbeda_dengan_jam_mulai()
    {
        $tanggal = new DateTime('2021-08-19');
        $jamMulai = new DateTime('2021-08-20 08:00:00');
        $jamSelesai = new DateTime('2021-08-19 10:00:00');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('tanggal_tatap_muka_tidak_sesuai_dengan_jam_mulai');

        return new JadwalTatapMuka($tanggal, $jamMulai, $jamSelesai);
    }

    public function test_tanggal_berbeda_dengan_jam_selesai()
    {
        $tanggal = new DateTime('2021-08-19');
        $jamMulai = new DateTime('2021-08-19 08:00:00');
        $jamSelesai = new DateTime('2021-08-20 10:00:00');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('tanggal_tatap_muka_tidak_sesuai_dengan_jam_selesai');

        return new JadwalTatapMuka($tanggal, $jamMulai, $jamSelesai);
    }

    public function test_jam_mulai_sama_dengan_jam_selesai()
    {
        $tanggal = new DateTime('2021-08-20');
        $jamMulai = new DateTime('2021-08-20 08:00:00');
        $jamSelesai = new DateTime('2021-08-20 08:00:00');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('jam_mulai_tidak_boleh_melebihi_jam_selesai');

        return new JadwalTatapMuka($tanggal, $jamMulai, $jamSelesai);
    }

    public function test_jam_mulai_lebih_besar_dari_jam_selesai()
    {
        $tanggal = new DateTime('2021-08-20');
        $jamMulai = new DateTime('2021-08-20 09:00:00');
        $jamSelesai = new DateTime('2021-08-20 08:00:00');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('jam_mulai_tidak_boleh_melebihi_jam_selesai');

        return new JadwalTatapMuka($tanggal, $jamMulai, $jamSelesai);
    }
}

