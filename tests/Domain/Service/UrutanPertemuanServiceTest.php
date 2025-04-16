<?php

declare(strict_types=1);

namespace Tests\Domain\Service;

use PHPUnit\Framework\TestCase;
use App\Domain\Service\UrutanPertemuanService;
use App\Domain\Entity\Pertemuan;
use App\Domain\ValueObject\UrutanPertemuan;
use App\Domain\ValueObject\PertemuanId;
use Ramsey\Uuid\Uuid;

class UrutanPertemuanServiceTest extends TestCase
{
    private function makePertemuan(int $urutan, ?string $id = null): Pertemuan
    {
        $mock = $this->createMock(Pertemuan::class);
        $mock->method('getPertemuanKe')->willReturn(new UrutanPertemuan($urutan));
        $mock->method('getId')->willReturn(new PertemuanId($id ?? Uuid::uuid4()->toString()));
        
        return $mock;
    }

    public function testUrutanTidakTerpakai(): void
    {
        $service = new UrutanPertemuanService();
        $urutan = new UrutanPertemuan(1);
        $list = [
            $this->makePertemuan(2),
            $this->makePertemuan(3)
        ];

        $this->assertFalse($service->isUrutanTerpakai($urutan, $list));
    }

    public function testUrutanTerpakai(): void
    {
        $service = new UrutanPertemuanService();
        $urutan = new UrutanPertemuan(2);
        $list = [
            $this->makePertemuan(1),
            $this->makePertemuan(2)
        ];

        $this->assertTrue($service->isUrutanTerpakai($urutan, $list));
    }

    public function testUrutanTerpakaiDenganPertemuanDiabaikan(): void
    {
        $service = new UrutanPertemuanService();
        $urutan = new UrutanPertemuan(2);
        $idToIgnore = Uuid::uuid4()->toString();

        $ignored = $this->makePertemuan(2, $idToIgnore);
        $list = [
            $this->makePertemuan(1),
            $ignored
        ];

        $this->assertFalse($service->isUrutanTerpakai($urutan, $list, $ignored));
    }
}
