# php-colorspace

[![CI](https://github.com/mattacosta/php-colorspace/workflows/CI/badge.svg)](https://github.com/mattacosta/php-colorspace/actions)

Convert, compare, and animate colors.

## Features

Color spaces

- [sRGB][Wiki_sRGB]
  - [HSL and HSV][Wiki_HslAndHsv]
- [CIELAB][Wiki_CIELAB]
- CIEXYZ

Color difference metrics

- [CIELAB &Delta;E][Wiki_ColorDifference]
  - CIE 1976
  - CIE 1994
  - CIE 2000
  - CMC l:c

Illuminants (part of the `CieXyzColor` class)

- D50
- D65

Animation

- Linear interpolation between colors

## Requirements

- PHP 8.1 or later

## Installation

```
composer require mattacosta/php-colorspace
```

## Usage

**Example**: Converting between color spaces (or alternate representations) using
the `ColorConverter` class:

```php
$hsl_color = ColorConverter::rgbToHsl($rgb_color);
```

**Example**: Computing the difference between two colors using the `ColorDifference`
class:

```php
$deltaE = ColorDifference::deltaE2000($reference, $sample);
```

**Example**: Transitioning from one color to another:

```php
// Tip: Use a cylindrical representation for best results.
$yellow = HsvColor::lerp($red, $green, 0.5);
```

<!-- Reference links -->

[Wiki_CIELAB]: https://en.wikipedia.org/wiki/CIELAB_color_space
[Wiki_ColorDifference]: https://en.wikipedia.org/wiki/Color_difference#CIELAB_ΔE*
[Wiki_HslAndHsv]: https://en.wikipedia.org/wiki/HSL_and_HSV
[Wiki_sRGB]: https://en.wikipedia.org/wiki/SRGB
