<?php

declare(strict_types=1);

namespace Tests\Domain\ValueObject;

use PHPUnit\Framework\TestCase;
use App\Domain\ValueObject\TopikPerkuliahan;
use InvalidArgumentException;

final class TopikPerkuliahanTest extends TestCase
{
    public function testCanCreateTopikPerkuliahanWithValidInputs(): void
    {
        $deskripsi = str_repeat('a', 20);
        $deskripsiEn = str_repeat('b', 30);

        $topik = new TopikPerkuliahan($deskripsi, $deskripsiEn);

        $this->assertEquals($deskripsi, $topik->getDeskripsi('id'));
        $this->assertEquals($deskripsiEn, $topik->getDeskripsi('en'));
    }

    public function testCanCreateTopikPerkuliahanWithDefaultEnglishDescription(): void
    {
        $deskripsi = str_repeat('x', 20);
        $topik = new TopikPerkuliahan($deskripsi, null);

        $this->assertEquals('-', $topik->getDeskripsi('en'));
    }

    public function testThrowsExceptionWhenDeskripsiIsTooShort(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('panjang_topik_tidak_sesuai');

        new TopikPerkuliahan('short', 'valid description here');
    }

    public function testThrowsExceptionWhenDeskripsiIsTooLong(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('panjang_topik_tidak_sesuai');

        new TopikPerkuliahan(str_repeat('a', 501), 'valid description here');
    }

    public function testThrowsExceptionWhenDeskripsiEnIsTooShortAndNotDefault(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('panjang_topik_inggris_tidak_sesuai');

        new TopikPerkuliahan(str_repeat('a', 20), 'short');
    }

    public function testThrowsExceptionWhenDeskripsiEnIsTooLong(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('panjang_topik_inggris_tidak_sesuai');

        new TopikPerkuliahan(str_repeat('a', 20), str_repeat('b', 501));
    }

    public function testGetDeskripsiReturnsIndonesianByDefault(): void
    {
        $deskripsi = str_repeat('c', 15);
        $topik = new TopikPerkuliahan($deskripsi, null);

        $this->assertEquals($deskripsi, $topik->getDeskripsi());
    }
}
