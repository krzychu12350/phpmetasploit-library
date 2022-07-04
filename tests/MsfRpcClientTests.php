<?php
declare(strict_types=1);
use PHPUnit\Framework\TestCase;

class MsfRpcClientTests extends TestCase
{
    /** @test */
    public function shouldReturnTrue(): void
    {
        $this->assertSame(true, true);
    }
}
