<?php declare(strict_types = 1);

namespace MattAcosta\ColorSpace;

final class ColorDifference {

  /**
   * Calculate a delta E using the CMC l:c metric.
   *
   * The lightness and chroma weights should represent a ratio appropriate for
   * an application. For "acceptability" use a ratio of 2:1 and for
   * "imperceptability" use a ratio of 1:1.
   *
   * https://en.wikipedia.org/wiki/Color_difference#CMC_l:c_(1984)
   *
   * @param CieLabColor $color1
   *   The reference color.
   * @param CieLabColor $color2
   *   The sample color.
   * @param float $kL
   *   (optional) The weight given to the lightness of a color. Defaults to 2.
   * @param float $kC
   *   (optional) The weight given to the chroma of a color. Defaults to 1.
   */
  public static function deltaCMC(CieLabColor $color1, CieLabColor $color2, float $kL = 2, float $kC = 1): float {
    $c1 = $color1->getChroma();
    $c2 = $color2->getChroma();
    $h1 = $color1->getHue();

    $t = ($h1 < 164 || $h1 > 345)
      ? 0.36 + abs(0.4 * cos(deg2rad($h1 + 35)))
      : 0.56 + abs(0.2 * cos(deg2rad($h1 + 168)));
    $f = sqrt(pow($c1, 4) / (pow($c1, 4) + 1900));

    $sL = $color1->l < 16 ? 0.511 : (0.040975 * $color1->l) / (1 + 0.01765 * $color1->l);
    $sC = ((0.0638 * $c1) / (1 + 0.0131 * $c1)) + 0.638;
    $sH = $sC * ($f * $t + 1 - $f);

    // Uses the same definition as the CIE94 formula.
    $dH = sqrt(pow($color1->a - $color2->a, 2) + pow($color1->b - $color2->b, 2) - pow($c1 - $c2, 2));

    $sL = ($color2->l - $color1->l) / ($kL * $sL);
    $sC = ($c2 - $c1) / ($kC * $sC);
    $sH = $dH / $sH;

    return sqrt(pow($sL, 2) + pow($sC, 2) + pow($sH, 2));
  }

  /**
   * Calculate a delta E using simple euclidian geometry (CIE76).
   *
   * NOTE: Since the CIELAB color space is not as perceptually uniform as
   * intended (especially in saturated regions) this formula rates those colors
   * too highly and is not recommended.
   *
   * https://en.wikipedia.org/wiki/Color_difference#CIE76
   *
   * @param CieLabColor $color1
   *   The reference color.
   * @param CieLabColor $color2
   *   The sample color.
   */
  public static function deltaE(CieLabColor $color1, CieLabColor $color2): float {
    return sqrt(pow($color2->l - $color1->l, 2) + pow($color2->a - $color1->a, 2) + pow($color2->b - $color1->b, 2));
  }

  /**
   * Calculate a delta E using the CIEDE2000 formula.
   *
   * This formula improves upon the CIE94 formula in the following areas:
   * - Adds a hue rotational term which improves "blues that turn into purple."
   * - Adds compensation for neutral colors.
   * - Adds further compensations for lightness, chroma, and hues.
   *
   * @param CieLabColor $color1
   *   The reference color.
   * @param CieLabColor $color2
   *   The sample color.
   *
   * @link http://www.ece.rochester.edu/~gsharma/ciede2000/ The CIEDE2000 Color-Difference Formula @endlink
   */
  public static function deltaE2000(
    CieLabColor $color1,
    CieLabColor $color2,
    float $kL = 1,
    float $kC = 1,
    float $kH = 1,
  ): float {
    // Step 1: Equations 2-7

    $c1 = $color1->getChroma();
    $c2 = $color2->getChroma();
    $cAvg = ($c1 + $c2) / 2;
    $g = (1 - sqrt(pow($cAvg, 7) / (pow($cAvg, 7) + pow(25, 7)))) / 2;
    $a1 = (1 + $g) * $color1->a;
    $a2 = (1 + $g) * $color2->a;
    $c1 = sqrt(pow($a1, 2) + pow($color1->b, 2));
    $c2 = sqrt(pow($a2, 2) + pow($color2->b, 2));
    $h1 = ($a1 === 0.0 && $color1->b === 0.0) ? 0 : rad2deg(atan2($color1->b, $a1));
    $h2 = ($a2 === 0.0 && $color2->b === 0.0) ? 0 : rad2deg(atan2($color2->b, $a2));
    if ($h1 < 0) {
      $h1 += 360;
    }
    if ($h2 < 0) {
      $h2 += 360;
    }

    // Step 2: Equations 8-11

    $dL = $color2->l - $color1->l;
    $dC = $c2 - $c1;
    $dH = 0;
    if ($c1 * $c2 !== 0.0) {
      $dH = $h2 - $h1;
      if (abs($dH) > 180) {
        if ($dH > 180) {
          $dH -= 360;
        }
        else if ($dH < -180) {
          $dH += 360;
        }
      }
    }
    $dH = 2 * sqrt($c1 * $c2) * sin(deg2rad($dH / 2));

    // Step 3: Equations 12-24

    $lAvg = ($color1->l + $color2->l) / 2;
    $cAvg = ($c1 + $c2) / 2;
    $hAvg = $h1 + $h2;
    if ($c1 * $c2 !== 0.0) {
      if (abs($h1 - $h2) > 180) {
        if ($hAvg < 360) {
          $hAvg = ($hAvg + 360) / 2;
        }
        else {
          $hAvg = ($hAvg - 360) / 2;
        }
      }
      else {
        $hAvg = $hAvg / 2;
      }
    }

    $t = 1;
    $t -= 0.17 * cos(deg2rad($hAvg - 30));
    $t += 0.24 * cos(deg2rad(2 * $hAvg));
    $t += 0.32 * cos(deg2rad(3 * $hAvg + 6));
    $t -= 0.20 * cos(deg2rad(4 * $hAvg - 63));
    $dT = 30 * exp(-1 * pow(($hAvg - 275) / 25, 2));

    $lAvgSqr = pow($lAvg - 50, 2);
    $sL = 1 + (0.015 * $lAvgSqr) / sqrt(20 + $lAvgSqr);
    $sC = 1 + 0.045 * $cAvg;
    $sH = 1 + 0.015 * $cAvg * $t;
    $rC = 2 * sqrt(pow($cAvg, 7) / (pow($cAvg, 7) + pow(25, 7)));
    $rT = -sin(deg2rad(2 * $dT)) * $rC;

    // Compute!
    $dL = $dL / ($kL * $sL);
    $dC = $dC / ($kC * $sC);
    $dH = $dH / ($kH * $sH);
    return sqrt(pow($dL, 2) + pow($dC, 2) + pow($dH, 2) + $rT * $dC * $dH);
  }

  /**
   * Calculate the delta E using application specific weights (CIE94).
   *
   * @param CieLabColor $color1
   *   The reference color.
   * @param CieLabColor $color2
   *   The sample color.
   * @param float $k1
   *   (optional) The application weight given to the chroma of a color.
   *   Graphic arts: 0.045 (default)
   *   Textiles: 0.048
   * @param float $k2
   *   (optional) The application weight given to the hue of a color.
   *   Graphic arts: 0.015 (default)
   *   Textiles: 0.014
   * @param int $kL
   *   (optional) The application weight given to the lightness of a color.
   *   Graphic arts: 1 (default)
   *   Textiles: 2
   */
  public static function deltaE94(
    CieLabColor $color1,
    CieLabColor $color2,
    float $k1 = 0.045,
    float $k2 = 0.015,
    int $kL = 1,
    int $kC = 1,
    int $kH = 1,
  ): float {
    if ($k1 <= 0 || $k2 <= 0) {
      throw new \RangeException('Application weights must be greater than 0');
    }
    if ($kL !== 1 && $kL !== 2) {
      throw new \RangeException('Invalid weight given for lightness');
    }

    $c1 = $color1->getChroma();
    $c2 = $color2->getChroma();

    $dL = $color1->l - $color2->l;
    $dC = $c1 - $c2;
    $dH = sqrt(pow($color1->a - $color2->a, 2) + pow($color1->b - $color2->b, 2) - pow($dC, 2));

    $sL = 1;
    $sC = 1 + $k1 * $c1;
    $sH = 1 + $k2 * $c1;

    $sL = $dL / ($kL * $sL);
    $sC = $dC / ($kC * $sC);
    $sH = $dH / ($kH * $sH);
    return sqrt(pow($sL, 2) + pow($sC, 2) + pow($sH, 2));
  }

}
