<?php declare(strict_types = 1);

namespace MattAcosta\ColorSpace;

/**
 * A color in the sRGB color space.
 *
 * https://en.wikipedia.org/wiki/SRGB
 */
class RgbColor {

  /**
   * The blue channel of the color, as a value between 0 and 255.
   */
  public readonly int $b;

  /**
   * The green channel of the color, as a value between 0 and 255.
   */
  public readonly int $g;

  /**
   * The red channel of the color, as a value between 0 and 255.
   */
  public readonly int $r;

  /**
   * Constructs a `RgbColor` object.
   */
  public function __construct(int $red, int $green, int $blue) {
    if ($red < 0 || $red > 255) {
      throw new \RangeException('Red must be between 0 and 255');
    }
    if ($green < 0 || $green > 255) {
      throw new \RangeException('Green must be between 0 and 255');
    }
    if ($blue < 0 || $blue > 255) {
      throw new \RangeException('Blue must be between 0 and 255');
    }
    $this->r = $red;
    $this->g = $green;
    $this->b = $blue;
  }

  /**
   * Constrains a given value between a minimum and maximum (inclusive).
   */
  protected static function clamp(int $value, int $min, int $max): int {
    return max(min($value, $max), $min);
  }

  /**
   * Computes the linear interpolation between two values.
   *
   * https://en.wikipedia.org/wiki/Linear_interpolation#Programming_language_support
   */
  protected static function lerpInt(int $a, int $b, float $t): int {
    return (int)((1 - $t) * $a + $t * $b);
  }

  /**
   * Determine the point between two colors using linear interpolation.
   *
   * This may not produce the expected result as it operates on the red, green,
   * and blue channels seperately. Use a `HslColor` or `HsvColor` instead.
   *
   * @param float $t
   *   A value representing a position on a timeline, with 0 being the start
   *   and 1 being the end of the time period. This value may be extrapolated
   *   beyond 0 and 1 however.
   */
  public static function lerp(RgbColor $a, RgbColor $b, float $t): RgbColor {
    return new RgbColor(
      RgbColor::clamp(RgbColor::lerpInt($a->r, $b->r, $t), 0, 255),
      RgbColor::clamp(RgbColor::lerpInt($a->g, $b->g, $t), 0, 255),
      RgbColor::clamp(RgbColor::lerpInt($a->b, $b->b, $t), 0, 255),
    );
  }

}