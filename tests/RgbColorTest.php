<?php declare(strict_types = 1);

namespace MattAcosta\ColorSpace;

use PHPUnit\Framework\TestCase;

/**
 * @covers \MattAcosta\ColorSpace\RgbColor
 */
final class RgbColorTest extends TestCase {

  public function constructorValidationProvider(): array {
    return [
      [-1, 0, 0, \RangeException::class],
      [256, 0, 0, \RangeException::class],
      [0, -1, 0, \RangeException::class],
      [0, 256, 0, \RangeException::class],
      [0, 0, -1, \RangeException::class],
      [0, 0, 256, \RangeException::class],
    ];
  }

  /**
   * @dataProvider constructorValidationProvider
   */
  public function testConstructorValidation(int $r, int $g, int $b, string $e): void {
    $this->expectException($e);
    new RgbColor($r, $g, $b);
  }

  public function testLerp(): void {
    $white = new RgbColor(255, 255, 255);
    $black = new RgbColor(0, 0, 0);

    $actual = RgbColor::lerp($white, $black, 0);
    $this->assertSame(255, $actual->r, 'Red');
    $this->assertSame(255, $actual->g, 'Green');
    $this->assertSame(255, $actual->b, 'Blue');

    $actual = RgbColor::lerp($white, $black, 0.5);
    $this->assertSame(127, $actual->r, 'Red');
    $this->assertSame(127, $actual->g, 'Green');
    $this->assertSame(127, $actual->b, 'Blue');

    $actual = RgbColor::lerp($white, $black, 1);
    $this->assertSame(0, $actual->r, 'Red');
    $this->assertSame(0, $actual->g, 'Green');
    $this->assertSame(0, $actual->b, 'Blue');

    // Extrapolate beyond 0.
    $actual = RgbColor::lerp($white, $black, -1);
    $this->assertSame(255, $actual->r, 'Red');
    $this->assertSame(255, $actual->g, 'Green');
    $this->assertSame(255, $actual->b, 'Blue');

    // Extrapolate beyond 1.
    $actual = RgbColor::lerp($white, $black, 2);
    $this->assertSame(0, $actual->r, 'Red');
    $this->assertSame(0, $actual->g, 'Green');
    $this->assertSame(0, $actual->b, 'Blue');
  }

}
