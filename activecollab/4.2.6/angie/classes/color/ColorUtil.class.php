<?php

/**
 * ColorUtil class
 *
 * @package angie.library.color
 */
final class ColorUtil {

  /**
   * Convert HEX to RGB
   *
   * @param string $hex
   * @return array
   */
  static function HEXtoRGB($hex) {
    $hex = str_replace("#", "", $hex);
    if (strlen($hex) == 3) {
      $r = hexdec(substr($hex,0,1).substr($hex,0,1));
      $g = hexdec(substr($hex,1,1).substr($hex,1,1));
      $b = hexdec(substr($hex,2,1).substr($hex,2,1));
    } else {
      $r = hexdec(substr($hex,0,2));
      $g = hexdec(substr($hex,2,2));
      $b = hexdec(substr($hex,4,2));
    } // if

    return array($r, $g, $b);
  } // HEXtoRGB

  /**
   * Convert RGB to HEX
   *
   * @param int $r
   * @param int $g
   * @param int $b
   * @return string
   */
  static function RGBtoHEX($r, $g, $b) {
    $hex = "#";
    $hex .= str_pad(dechex($r), 2, "0", STR_PAD_LEFT);
    $hex .= str_pad(dechex($g), 2, "0", STR_PAD_LEFT);
    $hex .= str_pad(dechex($b), 2, "0", STR_PAD_LEFT);
    return $hex;
  } // RGBtoHEX

  /**
   * Converts RGB to HSL
   *
   * @param number $r
   * @param number $g
   * @param number $b
   * @return array
   */
  static function RGBtoHSL($r, $g, $b) {
    $r = (float) $r / 255.0;
    $g = (float) $g / 255.0;
    $b = (float) $b / 255.0;

    $min = min($r, $g, $b);
    $max = max($r, $g, $b);

    $L = ($min + $max) / 2.0;
    if ($min == $max) {
      $S = $H = 0;
    } else {
      if ($L < 0.5)
        $S = ($max - $min)/($max + $min);
      else
        $S = ($max - $min)/(2.0 - $max - $min);

      if ($r == $max) $H = ($g - $b)/($max - $min);
      elseif ($g == $max) $H = 2.0 + ($b - $r)/($max - $min);
      elseif ($b == $max) $H = 4.0 + ($r - $g)/($max - $min);

    }

    return array(($H < 0 ? $H + 6 : $H) * 60, $S * 100, $L * 100);
  } // RGBtoHSL

  /**
   * Helper function needed for HSLtoRGB process
   *
   * @param $comp
   * @param $temp1
   * @param $temp2
   * @return mixed
   */
  static private function HSLtoRGBhelper($comp, $temp1, $temp2) {
    if ($comp < 0) $comp += 1.0;
    elseif ($comp > 1) $comp -= 1.0;

    if (6 * $comp < 1) return $temp1 + ($temp2 - $temp1) * 6 * $comp;
    if (2 * $comp < 1) return $temp2;
    if (3 * $comp < 2) return $temp1 + ($temp2 - $temp1)*((2/3) - $comp) * 6;

    return $temp1;
  } // if

  /**
   * HSL to RGB
   *
   * @param int $h
   * @param int $s
   * @param int $l
   */
  static function HSLtoRGB($h, $s, $l) {
    $H = $h / 360;
    $S = $s / 100;
    $L = $l / 100;

    if ($S == 0) {
      $r = $g = $b = $L;
    } else {
      $temp2 = $L < 0.5 ? $L*(1.0 + $S) : $L + $S - $L * $S;
      $temp1 = 2.0 * $L - $temp2;
      $r = ColorUtil::HSLtoRGBhelper($H + 1/3, $temp1, $temp2);
      $g = ColorUtil::HSLtoRGBhelper($H, $temp1, $temp2);
      $b = ColorUtil::HSLtoRGBhelper($H - 1/3, $temp1, $temp2);
    } // if

    return array(round($r*255), round($g*255), round($b*255));
  } // HSLtoRGB

  /**
   * Convert HEX to HSL
   *
   * @param string $hex
   * @return array
   */
  static function HEXtoHSL($hex) {
    list($r, $g, $b) = ColorUtil::HEXtoRGB($hex);
    return ColorUtil::RGBtoHSL($r, $g, $b);
  } // HEXtoHSL

  /**
   * Convert HSL to HEX
   *
   * @param int $h
   * @param int $s
   * @param int $l
   * @return String
   */
  static function HSLtoHEX($h, $s, $l) {
    list($r, $g, $b) = ColorUtil::HSLtoRGB($h, $s, $l);
    return ColorUtil::RGBtoHEX($r, $g, $b);
  } // HSLtoHEX
} // ColorUtil