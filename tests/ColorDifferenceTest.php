<?php declare(strict_types = 1);

namespace MattAcosta\ColorSpace;

use PHPUnit\Framework\TestCase;

/**
 * @covers \MattAcosta\ColorSpace\ColorDifference
 */
final class ColorDifferenceTest extends TestCase {

  public function deltaCMCProvider(): array {
    return [
      [new CieLabColor(36.3124, 55.0280, -100.7268), new CieLabColor(97.6071, -15.7529, 93.3885), 99.5793],  // #3333FF -> #FFFF00
      [new CieLabColor(100, 0, 0), new CieLabColor(0, 0, 0), 33.7401],   // white -> black
      [new CieLabColor(100, 0, 0), new CieLabColor(100, 0, 0), 0.0000],  // white -> white
    ];
  }

  public function deltaEProvider(): array {
    return [
      [new CieLabColor(36.3124, 55.0280, -100.7268), new CieLabColor(97.6071, -15.7529, 93.3885), 215.5173],  // #3333FF -> #FFFF00
      [new CieLabColor(100, 0, 0), new CieLabColor(0, 0, 0), 100.0000],  // white -> black
      [new CieLabColor(100, 0, 0), new CieLabColor(100, 0, 0), 0.0000],  // white -> white
    ];
  }

  public function deltaE94Provider(): array {
    return [
      [new CieLabColor(36.3124, 55.0280, -100.7268), new CieLabColor(97.6071, -15.7529, 93.3885), 97.3470],  // #3333FF -> #FFFF00
      [new CieLabColor(100, 0, 0), new CieLabColor(0, 0, 0), 100.0000],  // white -> black
      [new CieLabColor(100, 0, 0), new CieLabColor(100, 0, 0), 0.0000],  // white -> white
    ];
  }

  /**
   * http://www2.ece.rochester.edu/~gsharma/ciede2000/dataNprograms/ciede2000testdata.txt
   */
  public function deltaE2000Provider(): array {
    return [
      [new CieLabColor(50.0000,   2.6772, -79.7751), new CieLabColor(50.0000,   0.0000, -82.7485),  2.0425],
      [new CieLabColor(50.0000,   3.1571, -77.2803), new CieLabColor(50.0000,   0.0000, -82.7485),  2.8615],
      [new CieLabColor(50.0000,   2.8361, -74.0200), new CieLabColor(50.0000,   0.0000, -82.7485),  3.4412],
      [new CieLabColor(50.0000,  -1.3802, -84.2814), new CieLabColor(50.0000,   0.0000, -82.7485),  1.0000],
      [new CieLabColor(50.0000,  -1.1848, -84.8006), new CieLabColor(50.0000,   0.0000, -82.7485),  1.0000],
      [new CieLabColor(50.0000,  -0.9009, -85.5211), new CieLabColor(50.0000,   0.0000, -82.7485),  1.0000],
      [new CieLabColor(50.0000,   0.0000,   0.0000), new CieLabColor(50.0000,  -1.0000,   2.0000),  2.3669],
      [new CieLabColor(50.0000,  -1.0000,   2.0000), new CieLabColor(50.0000,   0.0000,   0.0000),  2.3669],
      [new CieLabColor(50.0000,   2.4900,  -0.0010), new CieLabColor(50.0000,  -2.4900,   0.0009),  7.1792],
      [new CieLabColor(50.0000,   2.4900,  -0.0010), new CieLabColor(50.0000,  -2.4900,   0.0010),  7.1792],
      [new CieLabColor(50.0000,   2.4900,  -0.0010), new CieLabColor(50.0000,  -2.4900,   0.0011),  7.2195],
      [new CieLabColor(50.0000,   2.4900,  -0.0010), new CieLabColor(50.0000,  -2.4900,   0.0012),  7.2195],
      [new CieLabColor(50.0000,  -0.0010,   2.4900), new CieLabColor(50.0000,   0.0009,  -2.4900),  4.8045],
      [new CieLabColor(50.0000,  -0.0010,   2.4900), new CieLabColor(50.0000,   0.0010,  -2.4900),  4.8045],
      [new CieLabColor(50.0000,  -0.0010,   2.4900), new CieLabColor(50.0000,   0.0011,  -2.4900),  4.7461],
      [new CieLabColor(50.0000,   2.5000,   0.0000), new CieLabColor(50.0000,   0.0000,  -2.5000),  4.3065],
      [new CieLabColor(50.0000,   2.5000,   0.0000), new CieLabColor(73.0000,  25.0000, -18.0000), 27.1492],
      [new CieLabColor(50.0000,   2.5000,   0.0000), new CieLabColor(61.0000,  -5.0000,  29.0000), 22.8977],
      [new CieLabColor(50.0000,   2.5000,   0.0000), new CieLabColor(56.0000, -27.0000,  -3.0000), 31.9030],
      [new CieLabColor(50.0000,   2.5000,   0.0000), new CieLabColor(58.0000,  24.0000,  15.0000), 19.4535],
      [new CieLabColor(50.0000,   2.5000,   0.0000), new CieLabColor(50.0000,   3.1736,   0.5854),  1.0000],
      [new CieLabColor(50.0000,   2.5000,   0.0000), new CieLabColor(50.0000,   3.2972,   0.0000),  1.0000],
      [new CieLabColor(50.0000,   2.5000,   0.0000), new CieLabColor(50.0000,   1.8634,   0.5757),  1.0000],
      [new CieLabColor(50.0000,   2.5000,   0.0000), new CieLabColor(50.0000,   3.2592,   0.3350),  1.0000],
      [new CieLabColor(60.2574, -34.0099,  36.2677), new CieLabColor(60.4626, -34.1751,  39.4387),  1.2644],
      [new CieLabColor(63.0109, -31.0961,  -5.8663), new CieLabColor(62.8187, -29.7946,  -4.0864),  1.2630],
      [new CieLabColor(61.2901,   3.7196,  -5.3901), new CieLabColor(61.4292,   2.2480,  -4.9620),  1.8731],
      [new CieLabColor(35.0831, -44.1164,   3.7933), new CieLabColor(35.0232, -40.0716,   1.5901),  1.8645],
      [new CieLabColor(22.7233,  20.0904, -46.6940), new CieLabColor(23.0331,  14.9730, -42.5619),  2.0373],
      [new CieLabColor(36.4612,  47.8580,  18.3852), new CieLabColor(36.2715,  50.5065,  21.2231),  1.4146],
      [new CieLabColor(90.8027,  -2.0831,   1.4410), new CieLabColor(91.1528,  -1.6435,   0.0447),  1.4441],
      [new CieLabColor(90.9257,  -0.5406,  -0.9208), new CieLabColor(88.6381,  -0.8985,  -0.7239),  1.5381],
      [new CieLabColor( 6.7747,  -0.2908,  -2.4247), new CieLabColor( 5.8714,  -0.0985,  -2.2286),  0.6377],
      [new CieLabColor( 2.0776,   0.0795,  -1.1350), new CieLabColor( 0.9033,  -0.0636,  -0.5514),  0.9082],

      [new CieLabColor(100, 0, 0), new CieLabColor(0, 0, 0),  100.0000],  // white -> black
      [new CieLabColor(100, 0, 0), new CieLabColor(100, 0, 0),  0.0000],  // white -> white
    ];
  }

  /**
   * @dataProvider deltaCMCProvider
   */
  public function testDeltaCMC(CieLabColor $reference, CieLabColor $sample, float $expected): void {
    $this->assertEqualsWithDelta($expected, ColorDifference::deltaCMC($reference, $sample), 0.0001);
  }

  /**
   * @dataProvider deltaEProvider
   */
  public function testDeltaE(CieLabColor $reference, CieLabColor $sample, float $expected): void {
    $this->assertEqualsWithDelta($expected, ColorDifference::deltaE($reference, $sample), 0.0001);
  }

  /**
   * @dataProvider deltaE94Provider
   */
  public function testDeltaE94(CieLabColor $reference, CieLabColor $sample, float $expected): void {
    $this->assertEqualsWithDelta($expected, ColorDifference::deltaE94($reference, $sample), 0.0001);
  }

  /**
   * @dataProvider deltaE2000Provider
   */
  public function testDeltaE2000(CieLabColor $reference, CieLabColor $sample, float $expected): void {
    $this->assertEqualsWithDelta($expected, ColorDifference::deltaE2000($reference, $sample), 0.0001);
  }

}
