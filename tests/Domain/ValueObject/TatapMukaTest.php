<?php

declare(strict_types=1);

namespace Tests\Domain\ValueObject;

use App\Domain\Entity\TatapMuka;
use App\Domain\ValueObject\JadwalTatapMuka;
use App\Domain\ValueObject\KelasId;
use App\Domain\ValueObject\KodePresensi;
use App\Domain\ValueObject\ModeTatapMuka;
use App\Domain\ValueObject\RuanganId;
use App\Domain\ValueObject\TatapMukaId;
use DateTime;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class TatapMukaTest extends TestCase
{
    private $jadwal;
    private $modeHybrid;
    private $modeOffline;
    private $modeOnline;

    protected function setUp(): void
    {
        $this->jadwal = new JadwalTatapMuka(
            new DateTime('2021-08-20'),
            new DateTime('2021-08-20 08:00:00'),
            new DateTime('2021-08-20 09:00:00')
        );

        $this->modeHybrid = new ModeTatapMuka(ModeTatapMuka::MODE_TATAP_MUKA_HYBRID);
        $this->modeOffline = new ModeTatapMuka(ModeTatapMuka::MODE_TATAP_MUKA_OFFLINE);
        $this->modeOnline = new ModeTatapMuka(ModeTatapMuka::MODE_TATAP_MUKA_ONLINE);
    }

    public function test_bisa_diinstansiasi()
    {
        $fakeUuid = Uuid::uuid4();

        $tatapMuka = new TatapMuka(
            new TatapMukaId($fakeUuid->toString()),
            new KelasId($fakeUuid->toString()),
            1,
            new RuanganId($fakeUuid->toString()),
            $this->jadwal,
            null,
            $this->modeHybrid,
            new KodePresensi('123123', new DateTime('2021-01-01 00:00:00'))
        );

        $this->assertEquals($fakeUuid, $tatapMuka->getTatapMukaId()->id());
        $this->assertEquals($fakeUuid, $tatapMuka->getKelasId()->id());
        $this->assertEquals(1, $tatapMuka->getPertemuanKe());
        $this->assertEquals($fakeUuid, $tatapMuka->getRuanganId()->id());
        $this->assertEquals(ModeTatapMuka::MODE_TATAP_MUKA_HYBRID, $tatapMuka->getMode()->getMode());
        $this->assertEquals('123123', $tatapMuka->getKodePresensi()->getKode());
        $this->assertEquals('2021-01-01 00:00:00', $tatapMuka->getKodePresensi()->getBerlakuSampai());
    }

    public function test_urutan_pertemuan_harus_lebih_besar_dari_nol()
    {
        $fakeUuid = Uuid::uuid4();
        $urutanPertemuan = 0;

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('urutan_harus_lebih_besar_dari_0');

        return new TatapMuka(
            new TatapMukaId($fakeUuid->toString()),
            new KelasId($fakeUuid->toString()),
            $urutanPertemuan,
            new RuanganId($fakeUuid->toString()),
            $this->jadwal,
            null,
            $this->modeHybrid,
            null
        );
    }

    public function test_tatap_muka_hybrid_harus_punya_ruangan()
    {
        $fakeUuid = Uuid::uuid4();
        $urutanPertemuan = 1;

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('mode_tatap_muka_hybrid_harus_memiliki_ruangan');

        return new TatapMuka(
            new TatapMukaId($fakeUuid->toString()),
            new KelasId($fakeUuid->toString()),
            $urutanPertemuan,
            null,
            $this->jadwal,
            null,
            $this->modeHybrid,
            null
        );
    }

    public function test_tatap_muka_offline_harus_punya_ruangan()
    {
        $fakeUuid = Uuid::uuid4();
        $urutanPertemuan = 1;

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('mode_tatap_muka_offline_harus_memiliki_ruangan');

        return new TatapMuka(
            new TatapMukaId($fakeUuid->toString()),
            new KelasId($fakeUuid->toString()),
            $urutanPertemuan,
            null,
            $this->jadwal,
            null,
            $this->modeOffline,
            null
        );
    }

    public function test_tatap_muka_online_tanpa_ruangan()
    {
        $fakeUuid = Uuid::uuid4();
        $urutanPertemuan = 1;

        $tatapMuka = new TatapMuka(
            new TatapMukaId($fakeUuid->toString()),
            new KelasId($fakeUuid->toString()),
            $urutanPertemuan,
            null,
            $this->jadwal,
            null,
            $this->modeOnline,
            null
        );

        $this->assertEquals($this->modeOnline->getMode(), $tatapMuka->getMode()->getMode());
    }
}
