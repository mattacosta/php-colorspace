<?php declare(strict_types = 1);

namespace MattAcosta\ColorSpace;

/**
 * A cyclindrical representation of the RGB color model typically used for
 * modeling how colors appear under a bright white light.
 *
 * https://en.wikipedia.org/wiki/HSL_and_HSV
 */
class HsvColor {

  /**
   * The angular dimension of the color, starting with the red primary at 0
   * degrees, and going through the green and blue primaries at 120 and 240
   * degrees respectively.
   */
  public readonly float $h;

  /**
   * The tint of the color (with white), as a number between 0 and 1.
   */
  public readonly float $s;

  /**
   * The brightness of the color, as a number between 0 and 1.
   */
  public readonly float $v;

  /**
   * Constructs a `HsvColor` object.
   */
  public function __construct(float $hue, float $saturation, float $value) {
    if ($hue < 0 || $hue > 360) {
      throw new \RangeException('Hue must be between 0 and 360');
    }
    if ($saturation < 0 || $saturation > 1) {
      throw new \RangeException('Saturation must be between 0 and 1');
    }
    if ($value < 0 || $value > 1) {
      throw new \RangeException('Value must be between 0 and 1');
    }
    $this->h = $hue;
    $this->s = $saturation;
    $this->v = $value;
  }

  /**
   * Constrains a given value between a minimum and maximum (inclusive).
   */
  protected static function clamp(float $value, float $min, float $max): float {
    return max(min($value, $max), $min);
  }

  /**
   * Computes the linear interpolation between two values.
   *
   * https://en.wikipedia.org/wiki/Linear_interpolation#Programming_language_support
   */
  protected static function lerpFloat(float $a, float $b, float $t): float {
    return (1 - $t) * $a + $t * $b;
  }

  /**
   * Determine the point between two colors using linear interpolation.
   *
   * @param float $t
   *   A value representing a position on a timeline, with 0 being the start
   *   and 1 being the end of the time period. This value may be extrapolated
   *   beyond 0 and 1 however.
   */
  public static function lerp(HsvColor $a, HsvColor $b, float $t): HsvColor {
    return new HsvColor(
      HsvColor::lerpFloat($a->h, $b->h, $t) % 360,
      HsvColor::clamp(HsvColor::lerpFloat($a->s, $b->s, $t), 0, 1),
      HsvColor::clamp(HsvColor::lerpFloat($a->v, $b->v, $t), 0, 1),
    );
  }

}
