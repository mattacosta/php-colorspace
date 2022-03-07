<?php declare(strict_types = 1);

namespace MattAcosta\ColorSpace;

final class ColorConverter {

  private function __construct() {
    // Not constructable.
  }

  /**
   * @todo Move to RgbColor::getHue()?
   */
  protected static function rgbToHue(float $r, float $g, float $b, float $max, float $delta): float {
    // Color is black or a shade of grey (R = G = B).
    if ($delta === 0.0) {
      return 0.0;
    }
    $hue = 0.0;
    if ($max === $r) {
      $hue = ($g - $b) / $delta;
    }
    else if ($max === $g) {
      $hue = ($b - $r) / $delta + 2;
    }
    else {
      $hue = ($r - $g) / $delta + 4;
    }
    $hue *= 60;
    if ($hue < 0) {
      $hue += 360;
    }
    return $hue;
  }

  /**
   * Converts a CIELAB color to a CIEXYZ color.
   *
   * @param CieLabColor $color
   *   The color to convert.
   * @param CieXyzColor|null $reference_white
   *   The reference white of the CIEXYZ color. Defaults to D65 if `null`.
   */
  public static function cieLabToCieXyz(CieLabColor $color, CieXyzColor $reference_white = null): CieXyzColor {
    $d = 6 / 29;
    $d3 = 3 * pow($d, 2);
    $l = ($color->l + 16) / 116;

    $x = $l + $color->a / 500;
    $y = $l;
    $z = $l - $color->b / 200;

    $x = $x > $d ? pow($x, 3) : $d3 * ($x - 4 / 29);
    $y = $y > $d ? pow($y, 3) : $d3 * ($y - 4 / 29);
    $z = $z > $d ? pow($z, 3) : $d3 * ($z - 4 / 29);

    if ($reference_white === null) {
      $reference_white = CieXyzColor::D65();
    }

    return new CieXyzColor(
      $reference_white->x * $x,
      $reference_white->y * $y,
      $reference_white->z * $z,
    );
  }

  /**
   * Converts a CIEXYZ color to a CIELAB color.
   *
   * @param CieXyzColor $color
   *   The color to convert.
   * @param CieXyzColor|null $reference_white
   *   The reference white of the CIEXYZ color. Defaults to D65 if `null`.
   */
  public static function cieXyzToCieLab(CieXyzColor $color, CieXyzColor $reference_white = null): CieLabColor {
    if ($reference_white === null) {
      $reference_white = CieXyzColor::D65();
    }

    $x = $color->x / $reference_white->x;
    $y = $color->y / $reference_white->y;
    $z = $color->z / $reference_white->z;

    // 0.008856 ~= (6 / 29) ** 3
    // 7.787    ~= 841 / 108 = (1 / 3) * (29 / 6) ** 2
    $x = $x > 0.008856 ? pow($x, 1 / 3) : (7.787 * $x) + (16 / 116);
    $y = $y > 0.008856 ? pow($y, 1 / 3) : (7.787 * $y) + (16 / 116);
    $z = $z > 0.008856 ? pow($z, 1 / 3) : (7.787 * $z) + (16 / 116);

    return new CieLabColor(
      116 * $y - 16,
      500 * ($x - $y),
      200 * ($y - $z)
    );
  }

  /**
   * Converts a CIEXYZ color to a RGB color.
   *
   * @experimental
   */
  public static function cieXyzToRgb(CieXyzColor $color): RgbColor {
    $r =  3.2409699419045226 * $color->x - 1.5373831775700940 * $color->y - 0.4986107602930034 * $color->z;
    $g = -0.9692436362808796 * $color->x + 1.8759675015077202 * $color->y + 0.0415550574071756 * $color->z;
    $b =  0.0556300796969937 * $color->x - 0.2039769588889765 * $color->y + 1.0569715142428786 * $color->z;

    $r = $r > 0.0031308 ? 1.055 * pow($r, 1 / 2.4) - 0.055 : 12.92 * $r;
    $g = $g > 0.0031308 ? 1.055 * pow($g, 1 / 2.4) - 0.055 : 12.92 * $g;
    $b = $b > 0.0031308 ? 1.055 * pow($b, 1 / 2.4) - 0.055 : 12.92 * $b;

    // @todo Clamping may result in wildly different output relative to the
    //   original color. Implement a gammut mapping technique in the future.
    return new RgbColor(
      max(min((int)round($r * 255), 255), 0),
      max(min((int)round($g * 255), 255), 0),
      max(min((int)round($b * 255), 255), 0),
    );
  }

  /**
   * Converts a HSL color to a RGB color.
   *
   * https://en.wikipedia.org/wiki/HSL_and_HSV#HSL_to_RGB
   */
  public static function hslToRgb(HslColor $color): RgbColor {
    $chroma = (1 - abs(2 * $color->l - 1)) * $color->s;
    $secondary = $chroma * (1 - abs((($color->h / 60) % 2) - 1));
    $match = $color->l - $chroma / 2;

    $red = 0;
    $green = 0;
    $blue = 0;

    if ($color->h < 60) {
      $red = $chroma;
      $green = $secondary;
    }
    else if ($color->h < 120) {
      $red = $secondary;
      $green = $chroma;
    }
    else if ($color->h < 180) {
      $green = $chroma;
      $blue = $secondary;
    }
    else if ($color->h < 240) {
      $green = $secondary;
      $blue = $chroma;
    }
    else if ($color->h < 300) {
      $red = $secondary;
      $blue = $chroma;
    }
    else {
      $red = $chroma;
      $blue = $secondary;
    }

    return new RgbColor(
      (int)(($red + $match) * 255),
      (int)(($green + $match) * 255),
      (int)(($blue + $match) * 255),
    );
  }

  /**
   * Converts a HSV color to a RGB color.
   *
   * https://en.wikipedia.org/wiki/HSL_and_HSV#HSV_to_RGB
   */
  public static function hsvToRgb(HsvColor $color): RgbColor {
    $chroma = $color->v * $color->s;
    $secondary = $chroma * (1 - abs((($color->h / 60) % 2) - 1));
    $match = $color->v - $chroma;

    $red = 0;
    $green = 0;
    $blue = 0;

    if ($color->h < 60) {
      $red = $chroma;
      $green = $secondary;
    }
    else if ($color->h < 120) {
      $red = $secondary;
      $green = $chroma;
    }
    else if ($color->h < 180) {
      $green = $chroma;
      $blue = $secondary;
    }
    else if ($color->h < 240) {
      $green = $secondary;
      $blue = $chroma;
    }
    else if ($color->h < 300) {
      $red = $secondary;
      $blue = $chroma;
    }
    else {
      $red = $chroma;
      $blue = $secondary;
    }

    return new RgbColor(
      (int)(($red + $match) * 255),
      (int)(($green + $match) * 255),
      (int)(($blue + $match) * 255),
    );
  }

  /**
   * Converts a RGB color to a HSL color.
   *
   * https://en.wikipedia.org/wiki/HSL_and_HSV#From_RGB
   */
  public static function rgbToHsl(RgbColor $color): HslColor {
    $r = $color->r / 255;
    $g = $color->g / 255;
    $b = $color->b / 255;

    $max = max($r, $g, $b);
    $min = min($r, $g, $b);
    $delta = $max - $min;

    $h = ColorConverter::rgbToHue($r, $g, $b, $max, $delta);
    $s = 0;
    $l = ($max + $min) / 2;
    if ($l > 0 && $l < 1) {
      $s = ($max - $l) / min($l, 1 - $l);
    }
    return new HslColor($h, $s, $l);
  }

  /**
   * Converts a RGB color to a HSV color.
   *
   * https://en.wikipedia.org/wiki/HSL_and_HSV#From_RGB
   */
  public static function rgbToHsv(RgbColor $color): HsvColor {
    // PHP division results in a float, unless both operators are integers that
    // are evenly divisible. If the color is black, $max will be the integer 0.
    $r = $color->r / 255;
    $g = $color->g / 255;
    $b = $color->b / 255;

    $max = max($r, $g, $b);
    $min = min($r, $g, $b);
    $delta = $max - $min;

    $hue = ColorConverter::rgbToHue($r, $g, $b, $max, $delta);
    $saturation = $max === 0 ? 0 : $delta / $max;
    return new HsvColor($hue, $saturation, $max);
  }

  /**
   * Converts a RGB color to a CIEXYZ color.
   *
   * https://en.wikipedia.org/wiki/SRGB#From_sRGB_to_CIE_XYZ
   */
  public static function rgbToCieXyz(RgbColor $color): CieXyzColor {
    $r = $color->r / 255;
    $g = $color->g / 255;
    $b = $color->b / 255;

    // RGB values are gamma-compressed. Convert to expanded (linear) values.
    $r = ($r > 0.04045) ? pow(($r + 0.055) / 1.055, 2.4) : $r / 12.92;
    $g = ($g > 0.04045) ? pow(($g + 0.055) / 1.055, 2.4) : $g / 12.92;
    $b = ($b > 0.04045) ? pow(($b + 0.055) / 1.055, 2.4) : $b / 12.92;

    return new CieXyzColor(
      $r * 0.41239079926595934 + $g * 0.357584339383878 + $b * 0.1804807884018343,
      $r * 0.21263900587151027 + $g * 0.715168678767756 + $b * 0.0721923153607337,
      $r * 0.01933081871559182 + $g * 0.119194779794626 + $b * 0.9505321522496607,
    );
  }

}
