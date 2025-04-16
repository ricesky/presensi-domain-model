<?php

declare(strict_types=1);

namespace Tests\Domain\Entity;

use PHPUnit\Framework\TestCase;
use App\Domain\Entity\Dosen;
use App\Domain\ValueObject\DosenId;
use Ramsey\Uuid\Uuid;

class DosenTest extends TestCase
{
    public function testCanCreateDosen(): void
    {
        $uuid = Uuid::uuid4()->toString();
        $dosenId = new DosenId($uuid);
        $nama = 'Rizky Januar Akbar';

        $dosen = new Dosen($dosenId, $nama);

        $this->assertEquals($dosenId, $dosen->getDosenId());
        $this->assertEquals($nama, $dosen->getNama());
    }

    public function testInvalidUuidThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new DosenId('not-a-valid-uuid');
    }

    public function testDosenIdEquals(): void
    {
        $uuid = Uuid::uuid4()->toString();
        $id1 = new DosenId($uuid);
        $id2 = new DosenId($uuid);

        $this->assertTrue($id1->equals($id2));
    }

    public function testDosenIdToString(): void
    {
        $uuid = Uuid::uuid4()->toString();
        $id = new DosenId($uuid);

        $this->assertEquals($uuid, (string) $id);
    }
}
