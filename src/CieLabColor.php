<?php declare(strict_types = 1);

namespace MattAcosta\ColorSpace;

/**
 * A color in the CIELAB color space.
 *
 * Colors in this space are device-independent and relate to a CIE standard
 * observer instead of a particular device.
 */
class CieLabColor {

  /**
   * The lightness of the color. Black at 0, and white at 100.
   */
  public readonly float $l;

  /**
   * The relation between green-red opponent colors. The color is more green if
   * negative and more red if positive.
   */
  public readonly float $a;

  /**
   * The relation between blue-yellow opponent colors. The color is more blue if
   * negative and more yellow if positive.
   */
  public readonly float $b;

  /**
   * Constructs a `CieLabColor` object.
   */
  public function __construct(float $l, float $a, float $b) {
    if ($l < 0 || $l > 100) {
      throw new \RangeException('Lightness must be between 0 and 100');
    }
    // @todo A* and B* are technically unbounded. In practice however, they
    //   may be bound to a range depending on the reference white used.
    $this->l = $l;
    $this->a = $a;
    $this->b = $b;
  }

  /**
   * Gets the chroma of the color when represented in cylindrical form (CIELCh).
   */
  public function getChroma(): float {
    return sqrt(pow($this->a, 2) + pow($this->b, 2));
  }

  /**
   * Gets the hue of the color when represented in cylindrical form (CIELCh).
   *
   * NOTE: A hue in RGB color space (which includes HSV and HSL colors) is not
   * comparable to a hue in CIELCH color space.
   */
  public function getHue(): float {
    $hue = rad2deg(atan2($this->b, $this->a));
    return $hue < 0 ? $hue + 360 : $hue;
  }

}
