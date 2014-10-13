<?php

  /**
   * Framework level languages manager implementation
   *
   * @package angie.frameworks.globalization
   * @subpackage models
   */
  abstract class FwLanguages extends BaseLanguages {
    
    /**
  	 * Return languages slice based on given criteria
  	 * 
  	 * @param integer $num
  	 * @param array $exclude
  	 * @param integer $timestamp
  	 * @return DBResult
  	 */
  	static function getSlice($num = 10, $exclude = null, $timestamp = null) {
  		if($exclude) {
  			return Languages::find(array(
  			  'conditions' => array("id NOT IN (?)", $exclude), 
  			  'order' => 'name', 
  			  'limit' => $num,  
  			));
  		} else {
  			return Languages::find(array(
  			  'order' => 'name', 
  			  'limit' => $num,  
  			));
  		} // if
  	} // getSlice
  	
  	/**
  	 * Return ID name map of languages
  	 * 
  	 * @return array
  	 */
  	static function getIdNameMap() {
  	  $rows = DB::execute('SELECT id, name FROM ' . TABLE_PREFIX . 'languages ORDER BY name');

      $result = array();
      $result['0'] = lang('English (built in)');
      $result['0'] = lang('English (built in)');

  	  if($rows) {
  	    foreach($rows as $row) {
  	      $result[(integer) $row['id']] = $row['name'];
  	    } // foreach
  	  } // if

      return $result;
  	} // getIdNameMap
    
    /**
     * Check if $locale is already defined in system
     *
     * @param string $locale
     * @return boolean
     */
    static function localeExists($locale) {
      return (boolean) Languages::count(array('locale = ?', $locale));
    } // localeExists
    
    /**
     * Check if $name is already used in system
     *
     * @param string $name
     * @return boolean
     */
    static function nameExists($name) {
      return (boolean) Languages::count(array('name = ?', $name));
    } // nameExists
        
    /**
     * Return default language
     *
     * @return Language
     */
    static function findDefault() {
      $default_language_id = ConfigOptions::getValue('language');
    	if($default_language_id) {
    	  $default_language = Languages::findById($default_language_id);
    	  if($default_language instanceof Language) {
    	    return $default_language;
    	  } // if
    	} // if

    	return null;
    } // findDefault

    /**
     * Get built in language
     *
     * @return Language
     */
    static function getBuiltIn() {
      $language = new Language();
      $language->setId(0);
      $language->setLocale(BUILT_IN_LOCALE);
      $language->setName('English');
      $language->setDecimalSeparator('.');
      $language->setThousandsSeparator(',');
      return $language;
    } // getBuiltIn
    
    /**
     * Find all languages
     * 
     * @return DBResult
     */
    static function findAll() {
    	return FwLanguages::find(array(
    		'order' => 'name'
    	));
    } // findAll
    
    /**
     * Return language by locale
     *
     * @param string $locale
     * @return Language
     */
    static function findByLocale($locale) {
      return Languages::find(array(
        'conditions' => array('locale = ?', $locale),
        'one' => true
      ));
    } // findByLocale
    
    /**
     * Find language by user
     * 
     * @param User $user
     * @return Language
     */
    static function findByUser(User $user) {
    	$language_id = ConfigOptions::getValueFor('language', $user);
    	return Languages::findById($language_id);
    } // findByUser
    
    /**
     * Clean up unused translations
     */
    static function cleanUpUnusedTranslations() {    	
    	$languages_table = TABLE_PREFIX . 'languages';
    	$translations_table = TABLE_PREFIX . 'language_phrase_translations';
    	$phrases_table = TABLE_PREFIX . 'language_phrases';
    	
    	// cleanup translations that belongs to non existing languages
    	DB::execute("DELETE $translations_table.* FROM $translations_table LEFT JOIN $languages_table ON $translations_table.language_id = $languages_table.id WHERE $languages_table.id IS NULL");
    	
    	// cleanup translations to non existing phrases in dictionary
    	DB::execute("DELETE $translations_table.* FROM $translations_table LEFT JOIN $phrases_table ON $translations_table.phrase_hash = $phrases_table.hash WHERE $phrases_table.phrase IS NULL");

      AngieApplication::cache()->removeByModel('languages');
    } // cleanUpUnusedTranslations

    /**
     * Return locale code of locale
     *
     * @param $locale
     * @return string
     */
    static function getLocaleCode($locale) {
      $locale_code = explode('.', $locale);
      $locale_code = array_var($locale_code, 0, 'en-us');
      $locale_code = str_replace('_', '-', $locale_code);
      return strtolower($locale_code);
    } // getLocaleCode

  }