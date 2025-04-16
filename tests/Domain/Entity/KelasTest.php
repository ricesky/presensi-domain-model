<?php

declare(strict_types=1);

namespace Tests\Domain\Entity;

use App\Domain\Exception\KelasException;
use App\Domain\Entity\Kelas;
use App\Domain\ValueObject\JadwalTatapMuka;
use App\Domain\ValueObject\KelasId;
use App\Domain\ValueObject\ModeTatapMuka;
use App\Domain\ValueObject\RuanganId;
use App\Domain\ValueObject\TatapMukaId;
use App\Domain\ValueObject\TopikPerkuliahan;
use DateTime;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class KelasTest extends TestCase
{
    private $jadwal;
    private $topik;
    private $modeHybrid;

    protected function setUp(): void
    {
        $this->jadwal = new JadwalTatapMuka(
            new DateTime('2021-08-20'),
            new DateTime('2021-08-20 08:00:00'),
            new DateTime('2021-08-20 09:00:00')
        );

        $this->topik = new TopikPerkuliahan("topik perkuliahan 1", "course topic 1");

        $this->modeHybrid = new ModeTatapMuka(ModeTatapMuka::MODE_TATAP_MUKA_HYBRID);
    }

    public function test_bisa_diinstansiasi()
    {
        $fakeUuid = Uuid::uuid4()->toString();
        $isNilaiFinal = false;

        $kelas = new Kelas(new KelasId($fakeUuid), $isNilaiFinal);

        $this->assertEquals($fakeUuid, $kelas->getId()->id());
        $this->assertEquals($isNilaiFinal, $kelas->isNilaiFinal());
    }

    public function test_buat_tatap_muka()
    {
        $fakeUuid = Uuid::uuid4()->toString();
        $isNilaiFinal = false;
        $urutanPertemuan = 1;

        $kelas = new Kelas(new KelasId($fakeUuid), $isNilaiFinal);
        $tatapMuka = $kelas->buatTatapMuka(
            $urutanPertemuan,
            new RuanganId($fakeUuid),
            $this->jadwal,
            $this->topik,
            $this->modeHybrid
        );

        $this->assertEquals($fakeUuid, $tatapMuka->getKelasId()->id());
        $this->assertEquals($fakeUuid, $tatapMuka->getRuanganId()->id());
        $this->assertEquals($urutanPertemuan, $tatapMuka->getPertemuanKe());
        $this->assertEquals($this->jadwal->getTanggal(), $tatapMuka->getJadwal()->getTanggal());
        $this->assertEquals($this->jadwal->getJamMulai(), $tatapMuka->getJadwal()->getJamMulai());
        $this->assertEquals($this->jadwal->getJamSelesai(), $tatapMuka->getJadwal()->getJamSelesai());
        $this->assertEquals($this->topik->getDeskripsi('id'), $tatapMuka->getTopik()->getDeskripsi('id'));
        $this->assertEquals($this->topik->getDeskripsi('en'), $tatapMuka->getTopik()->getDeskripsi('en'));
        $this->assertEquals($this->modeHybrid->getMode(), $tatapMuka->getMode()->getMode());
    }

    public function test_tidak_bisa_buat_tatap_muka_jika_nilai_final()
    {
        $fakeUuid = Uuid::uuid4()->toString();
        $isNilaiFinal = true;
        $urutanPertemuan = 1;

        $kelas = new Kelas(new KelasId($fakeUuid), $isNilaiFinal);

        $this->expectException(KelasException::class);
        $this->expectExceptionMessage('tidak_dapat_membuat_tatap_muka_baru_karena_nilai_sudah_final');

        return $kelas->buatTatapMuka(
            $urutanPertemuan,
            new RuanganId($fakeUuid),
            $this->jadwal,
            $this->topik,
            $this->modeHybrid
        );
    }

    public function test_ubah_tatap_muka()
    {
        $fakeUuid = Uuid::uuid4()->toString();
        $isNilaiFinal = false;
        $urutanPertemuan = 1;

        $kelas = new Kelas(new KelasId($fakeUuid), $isNilaiFinal);
        $tatapMuka = $kelas->ubahTatapMuka(
            new TatapMukaId($fakeUuid),
            $urutanPertemuan,
            new RuanganId($fakeUuid),
            $this->jadwal,
            $this->topik,
            $this->modeHybrid
        );

        $this->assertEquals($fakeUuid, $tatapMuka->getTatapMukaId()->id());
        $this->assertEquals($fakeUuid, $tatapMuka->getKelasId()->id());
        $this->assertEquals($fakeUuid, $tatapMuka->getRuanganId()->id());
        $this->assertEquals($urutanPertemuan, $tatapMuka->getPertemuanKe());
        $this->assertEquals($this->jadwal->getTanggal(), $tatapMuka->getJadwal()->getTanggal());
        $this->assertEquals($this->jadwal->getJamMulai(), $tatapMuka->getJadwal()->getJamMulai());
        $this->assertEquals($this->jadwal->getJamSelesai(), $tatapMuka->getJadwal()->getJamSelesai());
        $this->assertEquals($this->topik->getDeskripsi('id'), $tatapMuka->getTopik()->getDeskripsi('id'));
        $this->assertEquals($this->topik->getDeskripsi('en'), $tatapMuka->getTopik()->getDeskripsi('en'));
        $this->assertEquals($this->modeHybrid->getMode(), $tatapMuka->getMode()->getMode());
    }

    public function test_tidak_bisa_ubah_tatap_muka_jika_nilai_final()
    {
        $fakeUuid = Uuid::uuid4()->toString();
        $isNilaiFinal = true;
        $urutanPertemuan = 1;

        $kelas = new Kelas(new KelasId($fakeUuid), $isNilaiFinal);

        $this->expectException(KelasException::class);
        $this->expectExceptionMessage('tidak_dapat_mengubah_tatap_muka_baru_karena_nilai_sudah_final');

        return $kelas->ubahTatapMuka(
            new TatapMukaId($fakeUuid),
            $urutanPertemuan,
            new RuanganId($fakeUuid),
            $this->jadwal,
            $this->topik,
            $this->modeHybrid
        );
    }
    
}
