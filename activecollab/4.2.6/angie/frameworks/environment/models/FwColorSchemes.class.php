<?php

/**
 * Framework level color schemes implementation
 *
 * @package angie.frameworks.environment
 * @subpackage models
 */
abstract class FwColorSchemes {

  /**
   * Config option name for custom schemes
   *
   * @var string
   */
  private static $custom_schemes_option_name = 'custom_schemes';

  /**
   * Config option name for current scheme
   *
   * @var string
   */
  private static $current_scheme_option_name = 'current_scheme';

  /**
   * Get System Schemes
   *
   * @return array
   */
  static function getBuiltIn() {
    return array(
      'default' => array(
        'name' => lang('Default Scheme'),
        'background_color' => '#202329',
        'outer_color' => '#dedeb6',
        'inner_color' => '#e9eadf',
        'link_color' => '#950000',
        'read_only' => true
      )
    );
  } // getSystemSchemes

  /**
   * Custom cache
   *
   * @var array
   */
  private static $custom_cache = false;

  /**
   * Get Custom schemes
   *
   * @return array
   */
  final static function getCustom() {
    if (self::$custom_cache === false) {
      self::$custom_cache = ConfigOptions::getValue(self::$custom_schemes_option_name);
    } // if
    return self::$custom_cache;
  } // getCustom

  /**
   * Invalidate custom cache
   *
   * @return null
   */
  protected function invalidateCustomCache() {
    self::$custom_cache = false;
  } // invalidateCustomCache

  /**
   * Get All Schemes
   *
   * @return array
   */
  static function getAll() {
    return (array) self::getBuiltIn() + (array) self::getCustom();
  } // get

  /**
   * Get scheme by id
   *
   * @param string $scheme_id
   * @return array
   */
  static function get($scheme_id) {
    $all_schemes = self::getAll();
    if (!array_key_exists($scheme_id, $all_schemes)) {
      return false;
    } // if
    return $all_schemes[$scheme_id];
  } // get

  /**
   * Generate id based on scheme name
   *
   * @param string $scheme_name
   * @return strings
   */
  function generateId($scheme_name) {
    return strtolower(Inflector::slug($scheme_name));
  } // generateId

  /**
   *  Validate scheme
   *
   * @param array $scheme
   */
  static function validate($scheme_id, $scheme, $check_if_exists = false) {
    if (!array_key_exists('name', $scheme) || !$scheme['name']) {
      throw new Error(lang('Name is required for color scheme'));
    } // if

    if (!array_key_exists('background_color', $scheme) || !$scheme['background_color']) {
      throw new Error(lang('Background color is required for color scheme'));
    } // if

    if (!array_key_exists('outer_color', $scheme) || !$scheme['outer_color']) {
      throw new Error(lang('Outer color is required for color scheme'));
    } // if

    if (!array_key_exists('inner_color', $scheme) || !$scheme['inner_color']) {
      throw new Error(lang('Inner color is required for color scheme'));
    } // if

    if (!array_key_exists('link_color', $scheme) || !$scheme['link_color']) {
      throw new Error(lang('Link color is required for color scheme'));
    } // if

    if ($check_if_exists) {
      if (self::exists($scheme_id)) {
        throw new Error(lang('Color Scheme with that name already exists'));
      } // if
    } // if

    return true;
  } // validate

  /**
   *
   *
   * @param string $scheme_id
   * @return boolean
   */
  static function exists($scheme_id) {
    return in_array($scheme_id, array_keys(self::getAll()));
  } // exists

  /**
   * Check if $scheme_id is built in
   *
   * @param string $scheme_id
   */
  static function isBuiltIn($scheme_id) {
    return array_key_exists($scheme_id, self::getBuiltIn());
  } // isBuiltIn

  /**
   * Add new scheme
   *
   * @param string $id
   * @param array $scheme
   * @return boolean
   */
  static function add($scheme_id, $scheme) {
    self::validate($scheme_id, $scheme, true);

    $custom_styles = self::getCustom(); // get custom styles
    $custom_styles[$scheme_id] = $scheme; // add new custom style

    ConfigOptions::setValue(self::$custom_schemes_option_name, $custom_styles); // save config option with custom styles

    self::invalidateCustomCache(); // invalidate cache

    return true;
  } // add

  /**
   * Update existing scheme
   *
   * @param string $scheme_id
   * @param array $scheme
   * @return boolean
   */
  static function update($scheme_id, $scheme) {
    self::validate($scheme_id, $scheme, false);

    if (!self::exists($scheme_id)) {
      throw new Error(lang('Color scheme with that id does not exists'));
    } // if

    if (self::isBuiltIn($scheme_id)) {
      throw new Error(lang('Color scheme is built-in and cannot be modified'));
    } // if

    $custom_styles = self::getCustom(); // get custom styles
    $custom_styles[$scheme_id] = $scheme; // update custom style

    ConfigOptions::setValue(self::$custom_schemes_option_name, $custom_styles); // save config option with custom styles

    self::invalidateCustomCache(); // invalidate cache

    return true;
  } // update

  /**
   * Rename scheme
   *
   * @param string $scheme_id
   * @param string $new_id
   * @param string $new_name
   * @return boolean
   */
  static function rename($scheme_id, $new_id, $new_name) {
    $custom_styles = self::getCustom(); // get custom styles
    $scheme = $custom_styles[$scheme_id];
    $scheme['name'] = $new_name;

    self::validate($new_id, $scheme, true);

    // remove old value
    if (array_key_exists($scheme_id, $custom_styles)) {
      unset($custom_styles[$scheme_id]);
    } // if

    // add new key
    $custom_styles[$new_id] = $scheme;

    // save custom styles
    ConfigOptions::setValue(self::$custom_schemes_option_name, $custom_styles); // save config option with custom styles

    self::invalidateCustomCache(); // invalidate cache

    return true;
  } // rename

  /**
   * Delete scheme
   *
   * @param string $scheme_id
   */
  static function delete($scheme_id) {
    if (!self::exists($scheme_id)) {
      throw new Error(lang('Color scheme with that id does not exists'));
    } // if

    if (self::isBuiltIn($scheme_id)) {
      throw new Error(lang('Color scheme is built-in and cannot be deleted'));
    } // if

    $custom_styles = self::getCustom(); // get custom styles
    unset($custom_styles[$scheme_id]); // delete custom style

    ConfigOptions::setValue(self::$custom_schemes_option_name, $custom_styles); // save config option with custom styles

    self::invalidateCustomCache(); // invalidate cache

    return true;
  } // delete

  /**
   * Current scheme cache
   *
   * @var array
   */
  static protected $current_scheme_cache = false;

  /**
   * Get current scheme
   *
   * @return array
   */
  static function getCurrentScheme() {
    if (self::$current_scheme_cache === false) {
      self::$current_scheme_cache = self::get(self::getCurrentSchemeId());
    } // if

    return self::$current_scheme_cache;
  } // getCurrentScheme

  /**
   * Get current scheme id
   *
   * @return string
   */
  static function getCurrentSchemeId() {
    $current_scheme = ConfigOptions::getValue(self::$current_scheme_option_name);

    if (!self::exists($current_scheme)) {
      return 'default';
    } // if

    return $current_scheme;
  } // getCurrentSchemeId

  /**
   * Set current scheme
   *
   * @param String $scheme_id
   */
  static function setCurrentSchemeId($scheme_id) {
    if (!self::exists($scheme_id)) {
      throw new Error(lang('Scheme does not exists'));
    } // if

    self::$current_scheme_cache = false;

    return ConfigOptions::setValue(self::$current_scheme_option_name, $scheme_id);
  } // setCurrentSchemeId

  /**
   * Is background color light
   *
   * @return boolean
   */
  static function isBackgroundColorLight() {
    if (self::$current_scheme_cache === false) {
      self::getCurrentScheme();
    } // if

    $color = new Color(self::$current_scheme_cache['background_color']);

    return $color->getLightness() > 60;
  } // isBackgroundLight

  /**
   * Is outer color light
   *
   * @return boolean
   */
  static function isOuterColorLight() {
    if (self::$current_scheme_cache === false) {
      self::getCurrentScheme();
    } // if

    $color = new Color(self::$current_scheme_cache['outer_color']);

    return $color->getLightness() > 60;
  } // isOuterColorLight

  /**
   * Colors needed for calculation of other colors
   *
   * @var string
   */
  static protected $calculation_background_color;
  static protected $calculation_outer_color;
  static protected $calculation_inner_color;
  static protected $calculation_link_color;

  /**
   * Color replacements
   *
   * @var array
   */
  static protected $color_replacements = false;

  /**
   * Initialize for calculation
   *
   * @param string $background_color
   * @param string $outer_color
   * @param string $inner_color
   * @param string $link_color
   */
  static function initializeForCompile($background_color = null, $outer_color = null, $inner_color = null, $link_color = null) {
    if (self::$color_replacements !== false) {
      return true;
    } // if

    if ($background_color === null && $outer_color === null && $inner_color === null && $link_color === null) {
      $current_scheme = self::getCurrentScheme();
      self::$calculation_background_color = $current_scheme['background_color'];
      self::$calculation_outer_color = $current_scheme['outer_color'];
      self::$calculation_inner_color = $current_scheme['inner_color'];
      self::$calculation_link_color = $current_scheme['link_color'];
    } else {
      self::$calculation_background_color = $background_color;
      self::$calculation_outer_color = $outer_color;
      self::$calculation_inner_color = $inner_color;
      self::$calculation_link_color = $link_color;
    } // if

    // get color replacements
    self::$color_replacements = array(
      // main
      '#background-color-main' => $background_color,
      '#background-color-1' => ColorSchemes::getBackgroundColor1(),
      '#background-color-2' => ColorSchemes::getBackgroundColor2(),
      '#background-border-color-1' => ColorSchemes::getBackgroundBorderColor1(),
      '#background-border-color-2' => ColorSchemes::getBackgroundBorderColor2(),
      '#background-border-color-3' => ColorSchemes::getBackgroundBorderColor3(),

      // page
      '#background-outline-color' => ColorSchemes::getBackgroundOutlineColor(),
      '#statusbar-text-color' => ColorSchemes::getStatusbarTextColor(),
      '#link-color' => $link_color,

      // breadcrumbs
      '#breadcrumbs-text-color' => ColorSchemes::getBreadcrumbsTextColor(),
      '#breadcrumbs-border-color' => ColorSchemes::getBreadcrumbsBorderColor(),

      // search
      '#global-search-background-color' => ColorSchemes::getGlobalSearchBackgroundColor(),

      // inner colors
      '#inner-color-main' => $inner_color,
      '#inner-background-1' => ColorSchemes::getInnerBackgroundColor1(), // #object-list-background-color
      '#inner-background-2' => ColorSchemes::getInnerBackgroundColor2(), // #object-list-group-background-color
      '#inner-background-3' => ColorSchemes::getInnerBackgroundColor3(), // #object-list-filter-background-color
      '#inner-selection' => ColorSchemes::getInnerSelectionColor(), // #object-list-selection-color
      '#inner-border-1' => ColorSchemes::getInnerBorderColor1(), // #object-list-border-color-1
      '#inner-border-2' => ColorSchemes::getInnerBorderColor2(), // #object-list-border-color-2
      '#inner-border-3' => ColorSchemes::getInnerBorderColor3(), // #object-list-group-border-color
      '#inner-border-4' => ColorSchemes::getInnerBorderColor4(), // #object-list-filter-border-color-1
      '#inner-border-5' => ColorSchemes::getInnerBorderColor5(), // #object-list-filter-border-color-2

      // outer colors
      '#outer-color-main' => $outer_color,
      '#outer-background-1' => ColorSchemes::getOuterBackgroundColor1(), // #object-list-background-color
      '#outer-background-2' => ColorSchemes::getOuterBackgroundColor2(), // #object-list-group-background-color, #breadcrumbs-gradient-color
      '#outer-background-3' => ColorSchemes::getOuterBackgroundColor3(),
      '#outer-background-4' => ColorSchemes::getOuterBackgroundColor4(),
      '#outer-text-color' => ColorSchemes::getOuterTextColor(),

      // context popup
      '#context-popup-background' => ColorSchemes::getContextPopupBackgroundColor(),
      '#context-popup-outer-border' => ColorSchemes::getContextPopupOuterBorderColor(),
      '#context-popup-title-background-1' => ColorSchemes::getContextPopupBackgroundColor(),
      '#context-popup-title-background-2' => ColorSchemes::getContextPopupTitleBackgroundColor2(),
      '#context-popup-title-background-3' => ColorSchemes::getContextPopupTitleBackgroundColor3(),
      '#context-popup-title-text-color' => ColorSchemes::getContextPopupTitleTextColor(),
      '#context-popup-content-background-1' => ColorSchemes::getContextPopupContentBackgroundColor1(),
      '#context-popup-content-background-2' => ColorSchemes::getContextPopupContentBackgroundColor2(),
      '#context-popup-content-border-1' => ColorSchemes::getContextPopupContentBorderColor1()
    );

    return true;
  } // if

  /**
   * Replace colors in given css
   *
   * @param $css
   * @return mixed
   */
  static function compileCss($css) {
    return str_replace(array_keys(self::$color_replacements), array_values(self::$color_replacements), $css);
  } // compileCss

  /**
   * Get Link color
   *
   * @return string
   */
  static function getLinkColor() {
    return self::$calculation_link_color;
  } // getLinkColor

  /**
   * Get Menu Selector Color
   *
   * @return string
   */
  static function getBackgroundColor1() {
    $color = new Color(self::$calculation_background_color);
    $color->adjustHue(-2);
    $color->adjustSaturation(0);

    if ($color->getLightness() >= 50) {
      $color->adjustLightness(-9);
    } else {
      $color->adjustLightness(+9);
    } // if

    return $color->toHEX();
  } // getBackgroundColor1

  /**
   * Get Background Color 2
   *
   * @return String
   */
  static function getBackgroundColor2() {
    $color = new Color(self::getBackgroundColor1());
    $color->adjustSaturation(-5);
    $color->adjustLightness(14);
    return $color->toHEX();
  } // getBackgroundColor2

  /**
   * Get background border color 1
   *
   * @return string
   */
  static function getBackgroundBorderColor1() {
    $color = new Color(self::getBackgroundColor2());
    $color->adjustLightness(-27);
    return $color->toHEX();
  } // getBackgroundBorderColor1

  /**
   * Get background border color 2
   *
   * @return string
   */
  static function getBackgroundBorderColor2() {
    $color = new Color(self::getBackgroundColor2());
    $color->adjustLightness(-19);
    return $color->toHEX();
  } // getBackgroundBorderColor2

  /**
   * Get background border color 2
   *
   * @return string
   */
  static function getBackgroundBorderColor3() {
    $color = new Color(self::getBackgroundColor2());
    $color->adjustLightness(+11);
    return $color->toHEX();
  } // getBackgroundBorderColor2

  /**
   * Get color for other lines and outlines on background layer
   *
   * @return string
   */
  static function getBackgroundOutlineColor() {
    $color = new Color(self::$calculation_background_color);

    if ($color->getSaturation() > 5) {
      $color->setSaturation(10);
    } // if

    if ($color->getLightness() >= 50) {
      $color->adjustLightness(-10);
    } else {
      $color->adjustLightness(+15);
    } // if

    return $color->toHEX();
  } // getBackgroundOutlineColor

  /**
   * Get Statusbar Text Color
   *
   * @return string
   */
  static function getStatusbarTextColor() {
    $color = new Color(self::$calculation_background_color);
    if ($color->getLightness() >= 75) {
      $color->setLightness(15);
    } else {
      $color->setLightness(90);
    } // if

    return $color->toHEX();
  } // getStatusbarTextColor

  /**
   * Get breadcrumbs text color
   *
   * @return string
   */
  static function getBreadcrumbsTextColor() {
    $color = new Color(self::$calculation_outer_color);
    $color->adjustHue(-60);
    $color->adjustSaturation(-23);
    $color->adjustLightness(-44);
    return $color->toHEX();
  } // getBreadcrumbsTextColor

  /**
   * Get breadcrumbs border color
   *
   * @return string
   */
  static function getBreadcrumbsBorderColor() {
    $color = new Color(ColorSchemes::getOuterBackgroundColor2());
    $color->adjustLightness(-5);
    $color->adjustSaturation(-10);
    return $color->toHEX();
  } // getBreadcrumbsBorderColor

  /**
   * Get global search normal color
   *
   * @return string
   */
  static function getGlobalSearchBackgroundColor() {
    $color = new Color(self::$calculation_background_color);
    $color->adjustHue(-14);

    if ($color->getSaturation() < 30) {
      $color->adjustSaturation(-25);
    } else {
      $color->setSaturation(20);
    } // if

    if ($color->getLightness() < 30) {
      $color->adjustLightness(+25);
    } else {
      $color->adjustLightness(+5);
    } // if

    return $color->toHEX();
  } // getGlobalSearchBackgroundColor()

  /**
   * Get Object list background color
   *
   * @return string
   */
  static function getInnerBackgroundColor1() {
    return self::$calculation_inner_color;
  } // getInnerBackgroundColor1

  /**
   * Get object list group background color
   *
   * @return string
   */
  static function getInnerBackgroundColor2() {
    $color = new Color(self::$calculation_inner_color);
    $color->adjustHue(-1);
    $color->adjustSaturation(-1);
    $color->adjustLightness(-4);
    return $color->toHEX();
  } // getInnerBackgroundColor2

  /**
   * Get object list filter background
   *
   * @return string
   */
  static function getInnerBackgroundColor3() {
    $color = new Color(self::$calculation_inner_color);
    $color->adjustHue(8);
    $color->adjustSaturation(-11);
    $color->adjustLightness(-24);
    return $color->toHEX();
  } // getInnerBackgroundColor3

  /**
   * Get object list selection color
   *
   * @return string
   */
  static function getInnerSelectionColor() {
    $color = new Color(self::$calculation_link_color);
    $color->adjustHue(5);
    $color->adjustSaturation(-68);
    $color->adjustLightness(20);
    return $color->toHEX();
  } // getInnerSelectionColor

  /**
   * Get object list border color 1
   *
   * @return string
   */
  static function getInnerBorderColor1() {
    $color = new Color(self::$calculation_inner_color);
    $color->adjustHue(0);
    $color->adjustSaturation(-5);
    $color->adjustLightness(-3);
    return $color->toHEX();
  } // getInnerBorderColor1

  /**
   * Get object list border color 1
   *
   * @return string
   */
  static function getInnerBorderColor2() {
    $color = new Color(self::$calculation_inner_color);
    $color->adjustHue(-5);
    $color->adjustSaturation(4);
    $color->adjustLightness(2);
    return $color->toHEX();
  } // getInnerBorderColor2

  /**
   * Get object list border color 1
   *
   * @return string
   */
  static function getInnerBorderColor3() {
    $color = new Color(self::$calculation_inner_color);
    $color->adjustHue(0);
    $color->adjustSaturation(-8);
    $color->adjustLightness(-6);
    return $color->toHEX();
  } // getInnerBorderColor3

  /**
   * Get object list filter border color 1
   *
   * @return string
   */
  static function getInnerBorderColor4() {
    $color = new Color(self::$calculation_inner_color);
    $color->adjustHue(6);
    $color->adjustSaturation(-13);
    $color->adjustLightness(-30);
    return $color->toHEX();
  } // getInnerBorderColor4

  /**
   * Get object list filter border color 2
   *
   * @return string
   */
  static function getInnerBorderColor5() {
    $color = new Color(self::$calculation_inner_color);
    $color->adjustHue(12);
    $color->adjustSaturation(-14);
    $color->adjustLightness(-10);
    return $color->toHEX();
  } // getInnerBorderColor5

  /**
   * Get Object list background color
   *
   * @return string
   */
  static function getOuterBackgroundColor1() {
    return self::$calculation_outer_color;
  } // getOuterBackgroundColor1

  /**
   * Get object list group background color
   *
   * @return string
   */
  static function getOuterBackgroundColor2() {
    $color = new Color(self::$calculation_outer_color);
    $color->adjustLightness(-8);
    $color->adjustSaturation(-5);
    return $color->toHEX();
  } // getOuterBackgroundColor2

  /**
   * Get object list group background color
   *
   * @return string
   */
  static function getOuterBackgroundColor3() {
    $color = new Color(self::$calculation_outer_color);

    if ($color->getSaturation() > 5) {
      $color->setSaturation(33);
    } // if

    $color->setLightness(90);
    return $color->toHEX();
  } // getOuterBackgroundColor2

  /**
   * Get object list group background color
   *
   * @return string
   */
  static function getOuterBackgroundColor4() {
    $color = new Color(self::$calculation_outer_color);

    if ($color->getSaturation() > 5) {
      $color->setSaturation(35);
    } // if

    $color->setLightness(88);
    return $color->toHEX();
  } // getOuterBackgroundColor2

  /**
   * Get outer text color
   *
   * @return string
   */
  static function getOuterTextColor() {
    $color = new Color(self::getOuterBackgroundColor3());

    if ($color->getSaturation() > 5) {
      $color->setSaturation(6);
    } // if

    $color->setLightness(39);
    return $color->toHEX();
  } // getOuterTextColor

  /**
   * Get Context Popup Background Color
   *
   * @return String
   */
  static function getContextPopupBackgroundColor() {
    $color = new Color(self::$calculation_outer_color);

    if ($color->getSaturation() > 5) {
      $color->setSaturation(35);
    } // if

    $color->setLightness(92);
    return $color->toHEX();
  } // getContextPopupBackgroundColor

  /**
   * Get Context Popup Outer Border Color
   *
   * @return string
   */
  static function getContextPopupOuterBorderColor() {
    return '#babbbc';
  } // getContextPopupOuterBorderColor

  /***
   * Get Context Popup Content Background Color 1
   *
   * @return string
   */
  static function getContextPopupContentBackgroundColor1() {
    return '#FFFFFF';
  } // getContextPopupContentBackgroundColor1

  /***
   * Get Context Popup Content Background Color 2
   *
   * @return string
   */
  static function getContextPopupContentBackgroundColor2() {
    return '#f9f9f9';
  } // getContextPopupContentBackgroundColor2

  /**
   * Get Context Popup Content Border Color 1
   *
   * @return string
   */
  static function getContextPopupContentBorderColor1() {
    return '#f1f1f1';
  } // getContextPopupContentBorderColor1

  /**
   * Get Context Popup Title Text Color
   *
   * @return string
   */
  static function getContextPopupTitleTextColor() {
    $color = new Color(self::getContextPopupBackgroundColor());

    if ($color->getSaturation() > 5) {
      $color->setSaturation(6);
    } // if

    $color->setLightness(42);
    return $color->toHEX();
  } // getContextPopupTitleTextColor

  /**
   * Get Context Popup Title Background Color 2
   *
   * @return string
   */
  static function getContextPopupTitleBackgroundColor2() {
    $color = new Color(self::getContextPopupBackgroundColor());

    if ($color->getSaturation() > 5) {
      $color->setSaturation(27);
    } // if

    $color->setLightness(85);
    return $color->toHEX();
  } // getContextPopupTitleBackgroundColor2

  /**
   * Get Context Popup Title Background Color 3
   *
   * @return string
   */
  static function getContextPopupTitleBackgroundColor3() {
    $color = new Color(self::getContextPopupBackgroundColor());

    if ($color->getSaturation() > 5) {
      $color->setSaturation(31);
    } // if

    $color->setLightness(90);
    return $color->toHEX();
  } // getContextPopupTitleBackgroundColor3

} // FwColorSchemes