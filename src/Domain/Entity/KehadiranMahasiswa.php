<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Exception\KehadiranMahasiswaException;
use App\Domain\ValueObject\KehadiranMahasiswaId;
use App\Domain\ValueObject\JenisKehadiran;


use DateTime;

class KehadiranMahasiswa
{   
    private KehadiranMahasiswaId $id;
    private ?JenisKehadiran $jenisKehadiran;
    private ?DateTime $waktuCatat;
    private ?string $pencatat;
    
    public function __construct(
        KehadiranMahasiswaId $id,
        ?JenisKehadiran $jenisKehadiran,
        ?DateTime $waktuCatat,
        ?string $pencatat
    )
    {
        $this->id = $id;
        $this->jenisKehadiran = $jenisKehadiran;
        $this->waktuCatat = $waktuCatat;
        $this->pencatat = $pencatat;
    }

    public function getId(): KehadiranMahasiswaId
    {
        return $this->id;
    }

    public function getJenisKehadiran(): ?JenisKehadiran
    {
        return $this->jenisKehadiran;
    }

    public function getWaktuCatat(): ?DateTime
    {
        return $this->waktuCatat;
    }

    public function getPencatat(): ?string
    {
        return $this->pencatat;
    }

    public function ubah(
        Pertemuan $pertemuan, 
        JenisKehadiran $jenisKehadiran,
        string $pencatat): void
    {
        if ($pertemuan->getKelas()->isPermanen()) {
            throw new KehadiranMahasiswaException('tidak_dapat_mengubah_kehadiran_mahasiswa_karena_nilai_sudah_permanen');
        }

        if (!$this->jenisKehadiran->equals($jenisKehadiran)) {
            $this->jenisKehadiran = $jenisKehadiran;
            $this->pencatat = $pencatat;
            $this->waktuCatat = new DateTime('now');
        }
    }
    
}