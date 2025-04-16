<?php

declare(strict_types=1);

namespace Tests\Core;

use PHPUnit\Framework\TestCase;
use App\Domain\Exception\KodePresensiException;
use App\Domain\ValueObject\KodePresensi;
use DateTime;
use DateInterval;

final class KodePresensiTest extends TestCase
{
    public function testCanCreateValidKodePresensi(): void
    {
        $berlakuSampai = (new DateTime())->add(new DateInterval('PT1H')); // +1 hour
        $kode = '123456';

        $kodePresensi = new KodePresensi($kode, $berlakuSampai);

        $this->assertSame($kode, $kodePresensi->getKode());
        $this->assertEquals($berlakuSampai->format('Y-m-d H:i'), $kodePresensi->getBerlakuSampai()->format('Y-m-d H:i'));
    }

    public function testThrowsExceptionWhenCodeLengthIsNotSix(): void
    {
        $this->expectException(KodePresensiException::class);
        $this->expectExceptionMessage('panjang_kode_presensi_tidak_sesuai');

        new KodePresensi('123', new DateTime('+1 hour'));
    }

    public function testIsValidReturnsTrueWhenCorrectCodeAndNotExpired(): void
    {
        $berlakuSampai = (new DateTime())->add(new DateInterval('PT1H')); // +1 hour
        $kode = '654321';

        $kodePresensi = new KodePresensi($kode, $berlakuSampai);

        $this->assertTrue($kodePresensi->isValid('654321'));
    }

    public function testIsValidReturnsFalseWhenWrongCode(): void
    {
        $berlakuSampai = (new DateTime())->add(new DateInterval('PT1H'));
        $kodePresensi = new KodePresensi('000000', $berlakuSampai);

        $this->assertFalse($kodePresensi->isValid('999999'));
    }

    public function testIsValidReturnsFalseWhenCodeExpired(): void
    {
        $berlakuSampai = (new DateTime())->sub(new DateInterval('PT1H')); // expired
        $kodePresensi = new KodePresensi('123456', $berlakuSampai);

        $this->assertFalse($kodePresensi->isValid('123456'));
    }

    public function testGenerateCreatesValidKode(): void
    {
        $berlakuSampai = new DateTime('+2 hours');
        $kodePresensi = KodePresensi::generate($berlakuSampai);

        $this->assertInstanceOf(KodePresensi::class, $kodePresensi);
        $this->assertEquals(6, strlen($kodePresensi->getKode()));
        $this->assertTrue($kodePresensi->isValid($kodePresensi->getKode()));
    }

    public function testGantiKodeChangesCode(): void
    {
        $berlakuSampai = new DateTime('+1 hour');
        $original = new KodePresensi('111111', $berlakuSampai);
        $new = $original->gantiKode();

        $this->assertInstanceOf(KodePresensi::class, $new);
        $this->assertNotEquals($original->getKode(), $new->getKode());
        $this->assertEquals($original->getBerlakuSampai()->format('Y-m-d H:i'), $new->getBerlakuSampai()->format('Y-m-d H:i'));
    }
}
