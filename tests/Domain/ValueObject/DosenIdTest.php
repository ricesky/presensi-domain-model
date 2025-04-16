<?php

declare(strict_types=1);

namespace Tests\Domain\ValueObject;

use PHPUnit\Framework\TestCase;
use App\Domain\ValueObject\DosenId;
use Ramsey\Uuid\Uuid;

class DosenIdTest extends TestCase
{
    public function testCanCreateValidDosenId(): void
    {
        $uuid = Uuid::uuid4()->toString();
        $dosenId = new DosenId($uuid);

        $this->assertEquals($uuid, $dosenId->id());
    }

    public function testInvalidUuidThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new DosenId('invalid-uuid');
    }

    public function testEqualsReturnsTrueForSameUuid(): void
    {
        $uuid = Uuid::uuid4()->toString();

        $id1 = new DosenId($uuid);
        $id2 = new DosenId($uuid);

        $this->assertTrue($id1->equals($id2));
    }

    public function testEqualsReturnsFalseForDifferentUuid(): void
    {
        $id1 = new DosenId(Uuid::uuid4()->toString());
        $id2 = new DosenId(Uuid::uuid4()->toString());

        $this->assertFalse($id1->equals($id2));
    }

    public function testToStringReturnsCorrectValue(): void
    {
        $uuid = Uuid::uuid4()->toString();
        $dosenId = new DosenId($uuid);

        $this->assertEquals($uuid, (string) $dosenId);
    }

}
