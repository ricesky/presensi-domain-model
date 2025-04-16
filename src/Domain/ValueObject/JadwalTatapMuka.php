<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

use DateTime;
use InvalidArgumentException;

class JadwalTatapMuka
{
    private DateTime $tanggal;
    private DateTime $jamMulai;
    private DateTime $jamSelesai;

    public function __construct(DateTime $tanggal, DateTime $jamMulai, DateTime $jamSelesai)
    {
        $tanggalTatapMuka = $tanggal->format('Y-m-d');
        $tanggalTatapMukaMulai = $jamMulai->format('Y-m-d');
        $tanggalTatapMukaSelesai = $jamSelesai->format('Y-m-d');
        $tanggalTatapMukaDefaultDB = '1900-01-01';
        $isTanggalTatapMukaDefault = $tanggalTatapMukaDefaultDB == $tanggalTatapMukaMulai && $tanggalTatapMukaDefaultDB == $tanggalTatapMukaSelesai;

        if ($tanggalTatapMuka != $tanggalTatapMukaMulai && !$isTanggalTatapMukaDefault) {
            throw new InvalidArgumentException('tanggal_tatap_muka_tidak_sesuai_dengan_jam_mulai');
        }

        if ($tanggalTatapMuka != $tanggalTatapMukaSelesai && !$isTanggalTatapMukaDefault) {
            throw new InvalidArgumentException('tanggal_tatap_muka_tidak_sesuai_dengan_jam_selesai');
        }

        if ($jamMulai >= $jamSelesai) {
            throw new InvalidArgumentException('jam_mulai_tidak_boleh_melebihi_jam_selesai');
        }

        $this->tanggal = $tanggal;
        $this->jamMulai = $jamMulai;
        $this->jamSelesai = $jamSelesai;
    }

    public function getTanggal() : string
    {
        return $this->tanggal->format('Y-m-d');
    }

    public function getJamMulai() : string
    {
        return $this->jamMulai->format('Y-m-d H:i:s');
    }

    public function getJamSelesai() : string
    {
        return $this->jamSelesai->format('Y-m-d H:i:s');
    }
}
