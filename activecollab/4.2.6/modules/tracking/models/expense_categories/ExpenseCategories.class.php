<?php

  /**
   * ExpenseCategories class
   *
   * @package activeCollab.modules.tracking
   * @subpackage models
   */
  class ExpenseCategories extends BaseExpenseCategories {
    
    /**
     * Returns true if $user can define a new expense category
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
  	 * Return types slice based on given criteria
  	 * 
  	 * @param integer $num
  	 * @param array $exclude
  	 * @param integer $timestamp
  	 * @return DBResult
  	 */
  	static function getSlice($num = 10, $exclude = null, $timestamp = null) {
  		if($exclude) {
  			return ExpenseCategories::find(array(
  			  'conditions' => array("id NOT IN (?)", $exclude), 
  			  'order' => 'name', 
  			  'limit' => $num,  
  			));
  		} else {
  			return ExpenseCategories::find(array(
  			  'order' => 'name', 
  			  'limit' => $num,  
  			));
  		} // if
  	} // getSlice
    
    /**
     * Cached ID name map
     *
     * @var array
     */
    static private $id_name_map = false;
    
    /**
     * Return map of defined expense categories indexed by ID
     * 
     * @return array
     */
    static function getIdNameMap() {
      if(self::$id_name_map === false) {
        $rows = DB::execute('SELECT id, name FROM ' . TABLE_PREFIX . 'expense_categories ORDER BY name');
        
        if($rows) {
          self::$id_name_map = array();
          
          foreach($rows as $row) {
            self::$id_name_map[(integer) $row['id']] = trim($row['name']);
          } // foreach
        } else {
          self::$id_name_map = null;
        } // if
      } // if
      
      return self::$id_name_map;
    } // getIdNameMap
    
    /**
     * Return job type name by job type ID
     * 
     * @param integer $job_type_id
     * @return string
     */
    static function getNameById($job_type_id) {
      return array_var(self::getIdNameMap(), $job_type_id);
    } // getNameById
    
    // ---------------------------------------------------
    //  Default expense category
    // ---------------------------------------------------
    
    /**
     * Return default category ID
     * 
     * @return integer
     */
    static function getDefaultId() {
      return (integer) DB::executeFirstCell('SELECT id FROM ' . TABLE_PREFIX . 'expense_categories WHERE is_default = ? ORDER BY is_default DESC LIMIT 0, 1', true);
    } // getDefaultId
    
    /**
     * Return default expense category
     * 
     * @return ExpenseCategory
     */
    static function getDefault() {
      return ExpenseCategories::find(array(
        'order' => 'is_default DESC', 
        'one' => true, 
      ));
    } // getDefault
    
    /**
     * Set default expense category
     *
     * @param ExpenseCategory $category
     * @return ExpenseCategory
     */
    static function setDefault(ExpenseCategory $category) {
      try {
        $expense_categories_table = TABLE_PREFIX . 'expense_categories';
        
        DB::beginWork("Setting up default expense category @ " . __CLASS__);
        
        DB::execute("UPDATE $expense_categories_table SET is_default = ?", false);
        DB::execute("UPDATE $expense_categories_table SET is_default = ? WHERE id = ?", true, $category->getId());
        
        DB::commit("Default expense category has been set @ " . __CLASS__);
      } catch(Exception $e) {
        DB::rollback("Failed to set default expense category @ " . __CLASS__);
        throw $e;
      } // try
    } // setDefault
    
  }