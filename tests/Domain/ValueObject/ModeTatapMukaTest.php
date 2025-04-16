<?php

declare(strict_types=1);

namespace Tests\Domain\ValueObject;

use App\Domain\ValueObject\ModeTatapMuka;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ModeTatapMukaTest extends TestCase
{
    public function test_mode_hybrid_bisa_diinstansiasi()
    {
        $mode = new ModeTatapMuka(ModeTatapMuka::MODE_TATAP_MUKA_HYBRID);

        $this->assertEquals(ModeTatapMuka::MODE_TATAP_MUKA_HYBRID, $mode->getMode());
    }

    public function test_mode_online_bisa_diinstansiasi()
    {
        $mode = new ModeTatapMuka(ModeTatapMuka::MODE_TATAP_MUKA_ONLINE);

        $this->assertEquals(ModeTatapMuka::MODE_TATAP_MUKA_ONLINE, $mode->getMode());
    }

    public function test_mode_offline_bisa_diinstansiasi()
    {
        $mode = new ModeTatapMuka(ModeTatapMuka::MODE_TATAP_MUKA_OFFLINE);

        $this->assertEquals(ModeTatapMuka::MODE_TATAP_MUKA_OFFLINE, $mode->getMode());
    }

    public function test_mode_tidak_sesuai()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('mode_tatap_muka_tidak_sesuai');

        return new ModeTatapMuka('Z');
    }
}
