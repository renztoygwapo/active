<?php

/**
 * Color class
 *
 * @package angie.library.color
 */
final class Color {

  /**
   * H color component
   *
   * @var int
   */
  private $h_component;

  /**
   * S color component
   *
   * @var int
   */
  private $s_component;

  /**
   * L color component
   *
   * @var int
   */
  private $l_component;

  /**
   * Constructor
   *
   * @param int $r
   * @param int $g
   * @param int $b
   */
  function __construct($r, $g = null, $b = null) {
    // convert hex to rgb
    if (substr($r, 0, 1) == '#') {
      list($this->h_component, $this->s_component, $this->l_component) = ColorUtil::HEXtoHSL($r);
    } else if ($r) {
      list($this->h_component, $this->s_component, $this->l_component) = ColorUtil::RGBtoHSL($r, $g, $b);
    } else {
      $this->h_component = 0;
      $this->s_component = 0;
      $this->l_component = 0;
    } // if
  } // __construct

  /**
   * Get hue
   *
   * @return int
   */
  public function getHue() {
    return $this->h_component;
  } // getHue

  /**
   * Set Hue
   *
   * @param int $amount
   */
  public function setHue($amount) {
    return $this->h_component = $amount > 360 ? 360 : $amount < 0 ? 0 : $amount;
  } // setHue

  /**
   * Get saturation
   *
   * @return int
   */
  public function getSaturation() {
    return $this->s_component;
  } // getSaturation

  /**
   * Set Saturation
   *
   * @param int $amount
   */
  public function setSaturation($amount) {
    return $this->s_component = $amount > 100 ? 100 : $amount < 0 ? 0 : $amount;
  } // setSaturation

  /**
   * Get lightness
   *
   * @return int
   */
  public function getLightness() {
    return $this->l_component;
  } // getLightness

  /**
   * Set lightness
   *
   * @param int $amount
   */
  public function setLightness($amount) {
    return $this->l_component = $amount > 100 ? 100 : $amount < 0 ? 0 : $amount;
  } // setLightness

  /**
   * Adjust Hue
   *
   * @param int $amount
   * @return int
   */
  public function adjustHue($amount) {
    $this->h_component += $amount;
    $this->h_component = $this->h_component > 360 ? 360 : $this->h_component < 0 ? 0 : $this->h_component;
    return $this->h_component;
  } // adjustHue

  /**
   * Adjust Saturation
   *
   * @param int $amount
   * @return int
   */
  public function adjustSaturation($amount) {
    $this->s_component += $amount;
    $this->s_component = $this->s_component > 100 ? 100 : $this->s_component < 0 ? 0 : $this->s_component;
    return $this->s_component;
  } // adjustSaturation

  /**
   * Adjust Lightness
   *
   * @param int $amount
   * @return int
   */
  public function adjustLightness($amount) {
    $this->l_component += $amount;
    $this->l_component = $this->l_component > 100 ? 100 : $this->l_component < 0 ? 0 : $this->l_component;
    return $this->l_component;
  } // adjustLightness

  /**
   * Return this color as HEX
   *
   * @param string
   */
  public function toHEX() {
    return ColorUtil::HSLtoHEX($this->h_component, $this->s_component, $this->l_component);
  } // toHEX

  /**
   * Return this color as RGB
   *
   * @return array
   */
  public function toRGB() {
    return ColorUtil::HSLtoRGB($this->h_component, $this->s_component, $this->l_component);
  } // toRGB

  /**
   * Return this color as HSL
   *
   * @returns array
   */
  public function toHSL() {
    return array($this->h_component, $this->s_component, $this->l_component);
  } // toHSL
} // Color