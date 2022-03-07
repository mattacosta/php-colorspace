<?php declare(strict_types = 1);

namespace MattAcosta\ColorSpace;

/**
 * A color in the CIEXYZ color space.
 *
 * Colors in this space are device-independent and relate to a standard
 * observer instead of a particular device.
 */
class CieXyzColor {

  protected static ?CieXyzColor $D50 = null;

  protected static ?CieXyzColor $D65 = null;

  public readonly float $x;

  public readonly float $y;

  public readonly float $z;

  public function __construct(float $x, float $y, float $z)
  {
    $this->x = $x;
    $this->y = $y;
    $this->z = $z;
  }

  public static function D50(): CieXyzColor {
    if (CieXyzColor::$D50 === null) {
      CieXyzColor::$D50 = new CieXyzColor(96.422, 100.000, 82.521);
    }
    return CieXyzColor::$D50;
  }

  public static function D65(): CieXyzColor {
    if (CieXyzColor::$D65 === null) {
      // This definition is from https://en.wikipedia.org/wiki/Illuminant_D65
      // which is probably the definition from ASTM E308-01. Others include:
      //
      // Wikipedia's CIELAB page (95.0489, 100.000, 108.8840)
      // CSS color 4             (95.0456, 100.000, 108.9058)
      CieXyzColor::$D65 = new CieXyzColor(95.047, 100.000, 108.883);
    }
    return CieXyzColor::$D65;
  }

}
