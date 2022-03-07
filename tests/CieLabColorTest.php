<?php declare(strict_types = 1);

namespace MattAcosta\ColorSpace;

use PHPUnit\Framework\TestCase;

/**
 * @covers \MattAcosta\ColorSpace\CieLabColor
 */
final class CieLabColorTest extends TestCase {

  public function constructorValidationProvider(): array {
    return [
      [-1, 0, 0, \RangeException::class],
      [101, 0, 0, \RangeException::class],
    ];
  }

  public function getChromaProvider(): array {
    return [
      [  0, 0, 0, 0],  // Black
      [100, 0, 0, 0],  // White
      [32.2970,  79.1875, -107.8602, 133.8076],  // Blue
      [87.7347, -86.1827,   83.1793, 119.7759],  // Green
      [97.1393, -21.5537,   94.4780,  96.9054],  // Yellow
      [53.2408,  80.0925,   67.2032, 104.5518],  // Red
    ];
  }

  public function getHueProvider(): array {
    return [
      [  0, 0, 0, 0],  // Black
      [100, 0, 0, 0],  // White
      [32.2970,  79.1875, -107.8602, 306.2849],  // Blue
      [87.7347, -86.1827,   83.1793, 136.0160],  // Green
      [97.1393, -21.5537,   94.4780, 102.8512],  // Yellow
      [53.2408,  80.0925,   67.2032,  39.9990],  // Red
    ];
  }

  /**
   * @dataProvider constructorValidationProvider
   */
  public function testConstructorValidation(int $l, int $a, int $b, string $e): void {
    $this->expectException($e);
    new CieLabColor($l, $a, $b);
  }

  /**
   * @dataProvider getChromaProvider
   */
  public function testGetChroma(float $l, float $a, float $b, float $chroma): void {
    $color = new CieLabColor($l, $a, $b);
    $this->assertEqualsWithDelta($chroma, $color->getChroma(), 0.0001);
  }

  /**
   * @dataProvider getHueProvider
   */
  public function testGetHue(float $l, float $a, float $b, float $hue): void {
    $color = new CieLabColor($l, $a, $b);
    $this->assertEqualsWithDelta($hue, $color->getHue(), 0.0001);
  }

}
