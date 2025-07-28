<?php
declare(strict_types= 1);

use PHPUnit\Framework\TestCase;

final class ExampleTest extends TestCase
{
    public function testTrueExample(): void
    {
        $this->assertTrue(true);
    }

    public function testSameExample(): void
    {
        $this->assertSame(1,1);
    }
}