<?php

declare(strict_types=1);

namespace Tests\Domain\Entity;

use App\Domain\Entity\Kelas;
use App\Domain\Entity\Pertemuan;
use App\Domain\Exception\KelasException;
use App\Domain\ValueObject\JadwalPertemuan;
use App\Domain\ValueObject\KelasId;
use App\Domain\ValueObject\ModePertemuan;
use App\Domain\ValueObject\RuanganId;
use App\Domain\ValueObject\TopikPerkuliahan;
use App\Domain\ValueObject\UrutanPertemuan;
use DateTime;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

final class KelasTest extends TestCase
{
    private function makeKelas(bool $permanen = false, int $rencana = 16): Kelas
    {
        return new Kelas(new KelasId(Uuid::uuid4()->toString()), $rencana, $permanen);
    }

    private function makeJadwal(): JadwalPertemuan
    {
        $tanggal = new DateTime('2024-10-01');
        $jamMulai = new DateTime('1900-01-01 08:00:00');
        $jamSelesai = new DateTime('1900-01-01 10:00:00');
        return new JadwalPertemuan($tanggal, $jamMulai, $jamSelesai);
    }

    private function makeTopik(): TopikPerkuliahan
    {
        return new TopikPerkuliahan(
            str_repeat('a', 20),
            str_repeat('b', 20)
        );
    }

    private function makeUrutan(int $ke = 1): UrutanPertemuan
    {
        return new UrutanPertemuan($ke);
    }

    private function makeMode(string $mode): ModePertemuan
    {
        return match (strtolower($mode)) {
            'online'  => ModePertemuan::online(),
            'offline' => ModePertemuan::offline(),
            'hybrid'  => ModePertemuan::hybrid(),
            default   => throw new \InvalidArgumentException("Invalid mode: $mode")
        };
    }

    public function testCanCreatePertemuanSuccessfully(): void
    {
        $kelas = $this->makeKelas(false, 16);
        $ruanganId = new RuanganId(Uuid::uuid4()->toString());

        $pertemuan = $kelas->buatPertemuan(
            $this->makeUrutan(),
            $ruanganId,
            $this->makeJadwal(),
            $this->makeTopik(),
            $this->makeMode('offline')
        );

        $this->assertInstanceOf(Pertemuan::class, $pertemuan);
    }

    public function testThrowsWhenNoRencanaPertemuan(): void
    {
        $kelas = $this->makeKelas(false, 0);

        $this->expectException(KelasException::class);
        $this->expectExceptionMessage('tidak_dapat_buat_pertemuan_baru_karena_belum_ada_rencana_pertemuan');

        $kelas->buatPertemuan(
            $this->makeUrutan(),
            new RuanganId(Uuid::uuid4()->toString()),
            $this->makeJadwal(),
            $this->makeTopik(),
            $this->makeMode('offline')
        );
    }

    public function testThrowsWhenPermanen(): void
    {
        $kelas = $this->makeKelas(true, 16);

        $this->expectException(KelasException::class);
        $this->expectExceptionMessage('tidak_dapat_membuat_pertemuan_baru_karena_nilai_sudah_permanen');

        $kelas->buatPertemuan(
            $this->makeUrutan(),
            new RuanganId(Uuid::uuid4()->toString()),
            $this->makeJadwal(),
            $this->makeTopik(),
            $this->makeMode('offline')
        );
    }

    public function testThrowsWhenOfflineWithoutRuangan(): void
    {
        $kelas = $this->makeKelas();

        $this->expectException(KelasException::class);
        $this->expectExceptionMessage('mode_tatap_muka_offline_harus_memiliki_ruangan');

        $kelas->buatPertemuan(
            $this->makeUrutan(),
            null,
            $this->makeJadwal(),
            $this->makeTopik(),
            $this->makeMode('offline')
        );
    }

    public function testThrowsWhenHybridWithoutRuangan(): void
    {
        $kelas = $this->makeKelas();

        $this->expectException(KelasException::class);
        $this->expectExceptionMessage('mode_tatap_muka_hybrid_harus_memiliki_ruangan');

        $kelas->buatPertemuan(
            $this->makeUrutan(),
            null,
            $this->makeJadwal(),
            $this->makeTopik(),
            $this->makeMode('hybrid')
        );
    }

    public function testDoesNotRequireRuanganForOnline(): void
    {
        $kelas = $this->makeKelas();

        $pertemuan = $kelas->buatPertemuan(
            $this->makeUrutan(),
            null,
            $this->makeJadwal(),
            $this->makeTopik(),
            $this->makeMode('online')
        );

        $this->assertInstanceOf(Pertemuan::class, $pertemuan);
    }
}
