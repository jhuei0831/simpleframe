<?php declare(strict_types=1);

// $root = "../";
// include($root.'_config/settings.php');

use PHPUnit\Framework\TestCase;

final class LoginTest extends TestCase
{
    public function testCanLoginYoo(): void
    {
        $stack = [];
        $this->assertSame(0, count($stack));

        array_push($stack, 'foo');
        $this->assertSame('foo', $stack[count($stack)-1]);
        $this->assertSame(1, count($stack));

        $this->assertSame('foo', array_pop($stack));
        $this->assertSame(0, count($stack));
    }
}