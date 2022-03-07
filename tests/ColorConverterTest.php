<?php declare(strict_types = 1);

namespace MattAcosta\ColorSpace;

use PHPUnit\Framework\TestCase;

/**
 * @covers \MattAcosta\ColorSpace\ColorConverter
 */
final class ColorConverterTest extends TestCase {

  public function cieXyzToRgbProvider(): array {
    // NOTE: Keep synchronized with values in rgbToCieXyzProvider().
    return [
      [new CieXyzColor(0, 0, 0), 0, 0, 0],
      [new CieXyzColor(0.9505, 1.0000, 1.0890), 255, 255, 255],
      [new CieXyzColor(0.4124, 0.2126, 0.0193), 255, 0, 0],
      [new CieXyzColor(0.3576, 0.7152, 0.1192), 0, 255, 0],
      [new CieXyzColor(0.1804, 0.0722, 0.9505), 0, 0, 255],
    ];
  }

  public function hslToRgbProvider(): array {
    return [
      [new HslColor(  0, 0, 0.0), 0, 0, 0],
      [new HslColor(  0, 0, 1.0), 255, 255, 255],
      [new HslColor(  0, 1, 0.5), 255, 0, 0],
      [new HslColor(120, 1, 0.5), 0, 255, 0],
      [new HslColor(240, 1, 0.5), 0, 0, 255],
      [new HslColor(  0, 0, 0.5), 127, 127, 127],
    ];
  }

  public function hsvToRgbProvider(): array {
    return [
      [new HsvColor(0, 0, 0), 0, 0, 0],
      [new HsvColor(0.0, 0.0, 1.0), 255, 255, 255],
      [new HsvColor(  0, 1.0, 1.0), 255,   0,   0],
      [new HsvColor(120, 1.0, 1.0),   0, 255,   0],
      [new HsvColor(240, 1.0, 1.0),   0,   0, 255],
      [new HsvColor(  0, 0.0, 0.5), 127, 127, 127],
    ];
  }

  public function rgbToCieXyzProvider(): array {
    // NOTE: Keep synchronized with values in cieXyzToRgbProvider().
    return [
      [new RgbColor(0, 0, 0), 0, 0, 0],
      [new RgbColor(255, 255, 255), 0.9505, 1.0000, 1.0890],
      [new RgbColor(255,   0,   0), 0.4124, 0.2127, 0.0193],  // 0.4124 -> 0.4125
      [new RgbColor(  0, 255,   0), 0.3576, 0.7152, 0.1192],
      [new RgbColor(  0,   0, 255), 0.1804, 0.0722, 0.9505],  // 0.9505 -> 0.9503
      [new RgbColor(127, 127, 127), 0.2017, 0.2122, 0.2311],
    ];
  }

  public function rgbToHslProvider(): array {
    return [
      [new RgbColor(0, 0, 0), 0, 0, 0],
      [new RgbColor(255, 255, 255),   0.0, 0.0, 1.0],
      [new RgbColor(255,   0,   0),   0.0, 1.0, 0.5],
      [new RgbColor(  0, 255,   0), 120.0, 1.0, 0.5],
      [new RgbColor(  0,   0, 255), 240.0, 1.0, 0.5],
      [new RgbColor(127, 127, 127),   0.0, 0.0, 0.4980],
    ];
  }

  public function rgbToHsvProvider(): array {
    return [
      [new RgbColor(0, 0, 0), 0, 0, 0],
      [new RgbColor(255, 255, 255),   0.0, 0.0, 1.0],
      [new RgbColor(255,   0,   0),   0.0, 1.0, 1.0],
      [new RgbColor(  0, 255,   0), 120.0, 1.0, 1.0],
      [new RgbColor(  0,   0, 255), 240.0, 1.0, 1.0],
      [new RgbColor(127, 127, 127),   0.0, 0.0, 0.4980],
    ];
  }

  /**
   * @dataProvider cieXyzToRgbProvider
   */
  public function testCieXyzToRgb(CieXyzColor $xyz, int $r, int $g, int $b): void {
    $rgb = ColorConverter::cieXyzToRgb($xyz);
    $this->assertSame($r, $rgb->r);
    $this->assertSame($g, $rgb->g);
    $this->assertSame($b, $rgb->b);
  }

  /**
   * @dataProvider hslToRgbProvider
   */
  public function testHslToRgb(HslColor $color, int $r, int $g, int $b): void {
    $rgb = ColorConverter::hslToRgb($color);
    $this->assertSame($r, $rgb->r);
    $this->assertSame($g, $rgb->g);
    $this->assertSame($b, $rgb->b);
  }

  /**
   * @dataProvider hsvToRgbProvider
   */
  public function testHsvToRgb(HsvColor $color, int $r, int $g, int $b): void {
    $rgb = ColorConverter::hsvToRgb($color);
    $this->assertSame($r, $rgb->r);
    $this->assertSame($g, $rgb->g);
    $this->assertSame($b, $rgb->b);
  }

  /**
   * @dataProvider rgbToCieXyzProvider
   */
  public function testRgbToCieXyz(RgbColor $color, float $x, float $y, float $z): void {
    $xyz = ColorConverter::rgbToCieXyz($color);
    $this->assertEqualsWithDelta($x, $xyz->x, 0.0001);
    $this->assertEqualsWithDelta($y, $xyz->y, 0.0001);
    $this->assertEqualsWithDelta($z, $xyz->z, 0.0001);
  }

  /**
   * @dataProvider rgbToHslProvider
   */
  public function testRgbToHsl(RgbColor $color, float $h, float $s, float $l): void {
    $hsl = ColorConverter::rgbToHsl($color);
    $this->assertEqualsWithDelta($h, $hsl->h, 0.0001);
    $this->assertEqualsWithDelta($s, $hsl->s, 0.0001);
    $this->assertEqualsWithDelta($l, $hsl->l, 0.0001);
  }

  /**
   * @dataProvider rgbToHsvProvider
   */
  public function testRgbToHsv(RgbColor $color, float $h, float $s, float $v): void {
    $hsv = ColorConverter::rgbToHsv($color);
    $this->assertEqualsWithDelta($h, $hsv->h, 0.0001);
    $this->assertEqualsWithDelta($s, $hsv->s, 0.0001);
    $this->assertEqualsWithDelta($v, $hsv->v, 0.0001);
  }

}
