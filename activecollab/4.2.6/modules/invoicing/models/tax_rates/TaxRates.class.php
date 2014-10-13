<?php

  /**
   * TaxRates class
   * 
   * @package activeCollab.modules.invoicing
   * @subpackage models
   */
  class TaxRates extends BaseTaxRates {

    /**
  	 * Return slice of invoice item template definitions based on given criteria
  	 * 
  	 * @param integer $num
  	 * @param array $exclude
  	 * @param integer $timestamp
  	 * @return DBResult
  	 */
  	static function getSlice($num = 10, $exclude = null, $timestamp = null) {
  		if($exclude) {
  			return self::find(array(
  			  'conditions' => array('id NOT IN (?)', $exclude), 
  			  'order' => 'name', 
  			  'limit' => $num,  
  			));
  		} else {
  			return self::find(array(
  			  'order' => 'name', 
  			  'limit' => $num,  
  			));
  		} // if
  	} // getSlice

    /**
     * Get Default tax rate
     *
     * @return TaxRate
     */
    static function getDefault() {
      return self::find(array(
        'conditions' => array('is_default = ?', true),
        'one' => true
      ));
    } // getDefault

    /**
     * Return default tax rate ID
     *
     * @return int|null
     */
    static function getDefaultId() {
      $id = DB::executeFirstCell('SELECT id FROM ' . TaxRates::getTableName() . ' WHERE is_default = ? ORDER BY name LIMIT 0, 1', true);

      return $id ? (integer) $id : null;
    } // getDefaultId

    /**
     * Cached ID name map
     *
     * @var array
     */
    static private $id_name_map = false;

    /**
     * Cached ID name map with percentage included
     *
     * @var array
     */
    static private $id_name_map_with_percentage = false;

    /**
     * Return ID name map
     *
     * @param boolean $include_percentage
     * @return array
     */
    static function getIdNameMap($include_percentage = false) {
      if(self::$id_name_map === false && self::$id_name_map_with_percentage === false) {
        $tax_rates = DB::execute('SELECT id, name, percentage FROM ' . TaxRates::getTableName() . ' ORDER BY name');

        if($tax_rates) {
          $tax_rates->setCasting(array(
            'id' => DBResult::CAST_INT,
            'percentage' => DBResult::CAST_FLOAT,
          ));

          foreach($tax_rates as $tax_rate) {
            self::$id_name_map[$tax_rate['id']] = $tax_rate['name'];
            self::$id_name_map_with_percentage[$tax_rate['id']] = $tax_rate['name'] . ' (' . Globalization::formatNumber($tax_rate['percentage'], null, 3) . ')%';
          } // foreach
        } // if
      } // if

      return $include_percentage ? self::$id_name_map_with_percentage : self::$id_name_map;
    } // getIdNameMap

  }