<?php

  /**
   * Framework level currencies management implementation
   * 
   * @package angie.frameworks.globalization
   * @subpackage models
   */
  class FwCurrencies extends BaseCurrencies {
    
    /**
     * Returns true if $user can create new currency
     * 
     * @param IUser $user
     * @return boolean
     */
    static function canAdd(IUser $user) {
      return $user->isAdministrator();
    } // canAdd
    
    // ---------------------------------------------------
    //  Finders
    // ---------------------------------------------------
    
    /**
     * Cached default currency
     *
     * @var Currency
     */
    static private $default_currency = false;
  
    /**
     * Return default currency
     *
     * @return Currency
     */
    static function getDefault() {
      if(self::$default_currency === false) {
        self::$default_currency = AngieApplication::cache()->get('default_currency', function() {
          return Currencies::find(array(
            'order' => 'is_default DESC',
            'one' => true,
          ));
        });
      } //if
      
      return self::$default_currency;
    } // findDefault
    
    /**
     * Set $currency as default
     *
     * @param Currency $currency
     * @return Currency
     */
    static function setDefault(Currency $currency) {
      if($currency->getIsDefault()) {
        return true;
      } // if
      
      try {
        DB::beginWork('Setting default currency @ ' . __CLASS__);
      
        $currency->setIsDefault(true);
        $currency->save();
        
        DB::execute('UPDATE ' . TABLE_PREFIX . 'currencies SET is_default = ? WHERE id != ?', false, $currency->getId());
        AngieApplication::cache()->removeByModel('currencies');
        AngieApplication::cache()->remove('default_currency');
        
        DB::commit('Default currency set @ ' . __CLASS__);
        
        self::$default_currency = $currency;
      } catch(Exception $e) {
        DB::rollback('Failed to set default currency @ ' . __CLASS__);
        throw $e;
      } // try
      
      return self::$default_currency;
    } // setDefault
    
    /**
     * Return ID of default currency
     * 
     * @return integer
     */
    static function getDefaultId() {
      if(self::$default_currency instanceof Currency) {
        return self::$default_currency->getId();
      } else {
        return (integer) DB::executeFirstCell('SELECT id FROM ' . TABLE_PREFIX . 'currencies ORDER BY is_default DESC LIMIT 0, 1');
      } // if
    } // getDefaultId
    
    /**
  	 * Return currencies slice based on given criteria
  	 * 
  	 * @param integer $num
  	 * @param array $exclude
  	 * @param integer $timestamp
  	 * @return DBResult
  	 */
  	static function getSlice($num = 10, $exclude = null, $timestamp = null) {
  		if($exclude) {
  			return Currencies::find(array(
  			  'conditions' => array("id NOT IN (?)", $exclude), 
  			  'order' => 'name', 
  			  'limit' => $num,  
  			));
  		} else {
  			return Currencies::find(array(
  			  'order' => 'name', 
  			  'limit' => $num,  
  			));
  		} // if
  	} // getSlice
  	
  	/**
  	 * Return currency by currency code
  	 * 
  	 * @param string $code
  	 * @return Currency
  	 */
  	static function findByCode($code) {
  	  return Currencies::find(array(
  	    'conditions' => array('code = ?', $code), 
  	    'one' => true, 
  	  ));
  	} // findByCode
  	
  	/**
  	 * Cached ID name map
  	 *
  	 * @var array
  	 */
  	static private $id_name_map = false;
  	
  	/**
  	 * Return ID name map of currencies
  	 * 
  	 * @return array
  	 */
  	static function getIdNameMap() {
  	  if(self::$id_name_map === false) {
        self::$id_name_map = AngieApplication::cache()->get('currencies_id_name_map', function() {
          $rows = DB::execute('SELECT id, name FROM ' . TABLE_PREFIX . 'currencies ORDER BY name');
          if($rows) {
            $result = array();

            foreach($rows as $row) {
              $result[(integer) $row['id']] = $row['name'];
            } // foreach

            return $result;
          } // if

          return null;
        });
  	  } // if
  	  
  	  return self::$id_name_map;
  	} // getIdNameMap


    /**
     * Cached ID code map
     *
     * @var array
     */
    static private $id_code_map = false;

    /**
     * Return ID code map of currencies
     *
     * @return array
     */
    static function getIdCodeMap() {
      if(self::$id_code_map === false) {
        self::$id_code_map = AngieApplication::cache()->get('currencies_id_code_map', function() {
          $rows = DB::execute('SELECT id, code FROM ' . TABLE_PREFIX . 'currencies ORDER BY code');
          if($rows) {
            $result = array();

            foreach($rows as $row) {
              $result[(integer) $row['id']] = $row['code'];
            } // foreach

            return $result;
          } // if

          return null;
        });
      } // if

      return self::$id_code_map;
    } // getIdCodeMap
  	
  	/**
  	 * Cached ID details map
  	 *
  	 * @var array
  	 */
  	static private $id_details_map = false;
  	
  	/**
  	 * Prepare and return ID details map
  	 * 
  	 * @return array
  	 */
  	static function getIdDetailsMap() {
  	  if(self::$id_details_map === false) {
        self::$id_details_map = AngieApplication::cache()->get('currencies_id_details_map', function() {
          $rows = DB::execute('SELECT id, name, code, decimal_spaces, decimal_rounding FROM ' . TABLE_PREFIX . 'currencies ORDER BY name');
          if($rows) {
            $result = array();

            foreach($rows as $row) {
              $result[(integer) $row['id']] = array(
                'name' => $row['name'],
                'code' => $row['code'],
                'decimal_spaces' => $row['decimal_spaces'],
                'decimal_rounding' => $row['decimal_rounding'],
              );
            } // foreach

            return $result;
          } // if

          return null;
        });
  	  } // if
  	  
  	  return self::$id_details_map;
  	} // getIdDetailsMap

    /**
     * Get Number of Decimal spaces
     *
     * @param Currency $currency
     * @return int
     */
    static function getDecimalSpaces(Currency $currency = null) {
      if ($currency instanceof Currency) {
        return $currency->getDecimalSpaces();
      } // if

      $default_currency = Currencies::getDefault();
      if ($default_currency instanceof Currency) {
        return $default_currency->getDecimalSpaces();
      } // if

      return 2;
    } // getDecimalSpaces

    /**
     * Perform Decimal Rounding
     *
     * @param float $value
     * @param Currency $currency
     * @return float
     */
    static function roundDecimal($value, $currency) {
      if (!$currency->getDecimalRounding()) {
        return $value;
      } // if

      $rounding_step = 1 / $currency->getDecimalRounding();
      return round($value * $rounding_step) / $rounding_step;
    } // roundDecimal
  }