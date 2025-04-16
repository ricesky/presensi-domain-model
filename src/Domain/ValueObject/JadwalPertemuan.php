<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

use DateTime;
use InvalidArgumentException;

class JadwalPertemuan
{
    private DateTime $tanggal;
    private DateTime $jamMulai;
    private DateTime $jamSelesai;

    public function __construct(DateTime $tanggal, DateTime $jamMulai, DateTime $jamSelesai)
    {
        $jamMulai->setDate(
            intval($tanggal->format('Y')), 
            intval($tanggal->format('m')), 
            intval($tanggal->format('d'))
        );

        $jamSelesai->setDate(
            intval($tanggal->format('Y')), 
            intval($tanggal->format('m')), 
            intval($tanggal->format('d'))
        );
        
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

    public function getTanggal(): DateTime
    {
        return $this->tanggal;
    }

    public function getJamMulai(): DateTime
    {
        return $this->jamMulai;
    }

    public function getJamSelesai(): DateTime
    {
        return $this->jamSelesai;
    }
}
