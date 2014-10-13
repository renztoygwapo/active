<?php

  /**
   * Properly display money value
   * 
   * @package angie.frameworks.globalization
   * @subpackage helpers
   */

  /**
   * Returns formatted money value based on float input
   *
   * @param float $content
   * @param Currency $currency
   * @param Language $language
   * @param Boolean $with_currency_code
   * @param bool $round
   * @return string
   */
  function smarty_modifier_money($content, Currency $currency = null, Language $language = null, $with_currency_code = false, $round = false) {
    return Globalization::formatMoney($content, $currency, $language, $with_currency_code, $round);
  } // smarty_modifier_money