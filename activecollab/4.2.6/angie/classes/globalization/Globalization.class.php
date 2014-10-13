<?php

  /**
   * Globalization interface
   *
   * @package angie.library.globalization
   */
  final class Globalization {
  	
  	const PAPER_FORMAT_A3 = 'A3';
  	const PAPER_FORMAT_A4 = 'A4';
  	const PAPER_FORMAT_A5 = 'A5';
  	const PAPER_FORMAT_LEGAL = 'Legal';
  	const PAPER_FORMAT_LETTER = 'Letter';
  	
		const PAPER_ORIENTATION_PORTRAIT = 'Portrait';
		const PAPER_ORIENTATION_LANDSCAPE = 'Landscape';
    
    /**
     * Globalization adapter instance
     *
     * @var GlobalizationAdapter
     */
    static private $adapter;
    
    /**
     * Use globalization adapter by name
     *
     * @param string $name
     */
    static function useAdapter($name) {
      self::$adapter = new $name();
    } // useAdapter
    
    /**
     * Get language for given user
     * 
     * If $user is not a valid user instance, system will return language for 
     * currently logged in user or default language if no user is logged in
     * 
     * @param IUser $user
     * @return Language
     */
    static function getLanguage($user = null) {
      return self::$adapter->getLanguage($user);
    } // getLanguage
    
    /**
     * Return $content in selected language and insert $params in it
     *
     * @param string $content
     * @param array $params
     * @param boolean $clean_params
     * @param Language $language
     * @return string
     */
    static function lang($content, $params = null, $clean_params = true, $language = null) {
      $locale = $language instanceof Language ? $language->getLocale() : self::$current_language_locale;

      if($locale == BUILT_IN_LOCALE) {
        $result = $content;
      } else {
        if ($language instanceof Language && !isset(self::$current_langauge_translations[$locale])) {
          self::$current_langauge_translations[$locale] = $language->getTranslation(Language::DICTIONARY_SERVERSIDE); // load translations if not loaded already
        } // if

        $result = isset(self::$current_langauge_translations[$locale]) && isset(self::$current_langauge_translations[$locale][$content]) && self::$current_langauge_translations[$locale][$content] ? self::$current_langauge_translations[$locale][$content] : $content;
      } // if

      if($params && strpos($result, ':') !== false) {
        foreach($params as $k => $v) {
          $result = str_replace(":$k", ($clean_params ? clean($v) : $v), $result);
        } // foreach
      } // if

      return $result;
    } // lang

    /**
     * Locale of currenly loaded language
     *
     * @var string
     */
    static private $current_language_locale = BUILT_IN_LOCALE;

    /**
     * Translations of currently loaded languages
     *
     * @var array
     */
    static private $current_langauge_translations = array();
    
    /**
     * Set current locale by given user
     * 
     * @param User $user
     * @return Language
     */
    static function setCurrentLocaleByUser($user) {
      $language_id = $user instanceof User ? ConfigOptions::getValueFor('language', $user) : ConfigOptions::getValue('language');
          
      // Now load language
      if ($language_id) {
        $language = Languages::findById($language_id);
        if ($language instanceof Language) {
          self::$current_language_locale = $language->getLocale();
              
          if (self::$current_language_locale != BUILT_IN_LOCALE) {
            setlocale(LC_ALL, self::$current_language_locale); // Set locale
          	self::$current_langauge_translations[self::$current_language_locale] = $language->getTranslation(Language::DICTIONARY_SERVERSIDE);
        	} // if
        	
        	return $language;
      	} // if
  		} // if
  		
  		return new Language(); // Language not loaded?
    } // setCurrentLocaleByUser
    
    /**
     * Return all available timezones
     *
     * @return array
     */
    static function getTimezones() {
      return self::$adapter->getTimezones();
    } // getTimezones
    
    /**
     * Return formatted timezone name
     *
     * @param integer $offset
     * @param string $name
     * @return string
     */
    static function getFormattedTimezone($offset, $name = null) {
      $timezones = self::getTimezones();

      if(empty($name)) {
        $name = isset($timezones[$offset]) ? implode(', ', $timezones[$offset]) : '';
      } // if

      if($offset === 0) {
        return "(GMT) $name";
      } else {
        $sign = $offset > 0 ? '+' : '-';
        $hours = abs($offset) / 3600;
        if($hours < 10) {
          $hours = '0' . floor($hours);
        } // if
        $minutes = (abs($offset) % 3600) / 60;
        if($minutes < 10) {
          $minutes = '0' . $minutes;
        } // if
        
        return "(GMT $sign$hours:$minutes) $name";
      } // if
    } // getFormattedTimezone
    
    /**
     * Return array of month names
     *
     * @param Language $language
     * @return array
     */
    static function getMonthNames($language = null) {
      return self::$adapter->getMonthNames($language, false);
    } // getMonthNames
    
    /**
     * Return array of month names, in short format
     *
     * @param Language $language
     * @return array
     */
    static function getShortMonthNames($language = null) {
      return self::$adapter->getMonthNames($language, true);
    } // getShortMonthNames
    
    /**
     * Return day names
     *
     * @param Language $language
     * @return array
     */
    static function getDayNames($language = null) {
      return self::$adapter->getDayNames($language, false);
    } // getDayNames
    
    /**
     * Return short day names
     *
     * @param Language $language
     * @return array
     */
    static function getShortDayNames($language = null) {
      return self::$adapter->getDayNames($language, true);
    } // getShortDayNames
    
    /**
     * Returns true if $date is work day
     *
     * @param DateValue $date
     * @return boolean
     */
    static function isWorkday(DateValue $date) {
      return self::$adapter->isWorkday($date);
    } // isWorkday

    /**
     * Returns true if $date is not work day
     *
     * @param DateValue $date
     * @return boolean
     */
    static function isWeekend(DateValue $date) {
      return !self::$adapter->isWorkday($date);
    } // isWorkday
    
    /**
     * Returns true if $date is day off
     *
     * @param DateValue $date
     * @return boolean
     */
    static function isDayOff(DateValue $date) {
      return self::$adapter->isDayOff($date);
    } // isDayOff
    
    /**
     * Get array of workdays
     * 
     * @return array
     */
    static function getWorkdays() {
    	return self::$adapter->getWorkdays();
    } // getWorkdays
    
    /**
     * Get array of days off
     * 
     * @return array
     */
    static function getDaysOff() {
    	return self::$adapter->getDaysOff();
    } // getDaysOff
    
    /**
     * Get array of days off mapped for js
     * 
     * @return array
     */
    static function getDaysOffMappedForJs() {
    	return self::$adapter->getDaysOffMappedForJs();
    } // getDaysOffMappedForJs
    
    /**
     * Get array of letters in english alphabet
     * 
     * @return array
     */
    static function getAlphabet() {
    	return self::$adapter->getAlphabet();
    } // getAlphabet

    /**
     * Format number
     *
     * @param float $number
     * @param Language $language
     * @param int $decimal_spaces
     * @param bool $trim_zeros
     * @return string
     */
    static function formatNumber($number, Language $language = null, $decimal_spaces = 2, $trim_zeros = false) {
      return self::$adapter->formatNumber($number, $language, $decimal_spaces, $trim_zeros);
    } // formatNumber

    /**
     * Format money
     *
     * @param float $number
     * @param Currency $currency
     * @param Language $language
     * @param Boolean $include_code
     * @param Boolean $round
     * @return string
     */
    static function formatMoney($number, Currency $currency = null, Language $language = null, $include_code = false, $round = false) {
      return self::$adapter->formatMoney($number, $currency, $language, $include_code, $round);
    } // formatMoney
    
  }