<?php

  /**
   * Simple container for managing configuration options and storing values
   *
   * @package angie.library
   */
  final class ConfigOptions {
    
    /**
     * Return value by name
     * 
     * If $name is an array, system will get array of configuration option 
     * values and return them as associative array
     * 
     * Set $use_cache to false if you want this method to ignore cached values
     *
     * @param mixed $name
     * @param boolean $use_cache
     * @return mixed
     * @throws ConfigOptionDnxError
     * @throws InvalidParamError
     */
    static function getValue($name, $use_cache = true) {
      if(empty($name)) {
        throw new InvalidParamError('name', $name);
      } // if

      $find = (array) $name;
      
      $single = $find !== $name; // if we had conversion to array, we had scalar
      
      $cached_values = AngieApplication::cache()->get('config_options', function() {
        $options = array();
        $config_options = DB::execute("SELECT name, value FROM " . TABLE_PREFIX . "config_options");
        foreach ($config_options as $config_option) {
          $options[$config_option['name']] = !is_null($config_option['value']) ? unserialize($config_option['value']) : null;
        } // foreach

        return $options;
      });

      $values = array();
      
      foreach($find as $option) {
        if ($use_cache) {
          if (array_key_exists($option, $cached_values)) {
            $values[$option] = $cached_values[$option];
            continue;
          } else {
            throw new ConfigOptionDnxError($option);
          } // if
        } else {
          if($row = DB::executeFirstRow('SELECT value FROM ' . TABLE_PREFIX . 'config_options WHERE name = ?', $option)) {
            $values[$option] = !is_null($row['value']) ? unserialize($row['value']) : null;
            if(is_array($cached_values)) {
              $cached_values[$option] = $values[$option];
            } else {
              $cached_values = array($option => $values[$option]);
            } // if
          } else {
            throw new ConfigOptionDnxError($option);
          } // if
        } // if
        

      } // foreach

      return $single ? array_shift($values) : $values;
    } // getValue
    
    /**
     * Set value for a given object
     * 
     * This function can be called in following ways:
     * 
     * ConfigOptions::setValue('Option Name', 'Value');
     * 
     * as well as:
     * 
     * ConfigOptions::setValeu(array(
     *   'Option 1' => 'Value 1',
     *   'Option 2' => 'Value 2',
     * ));
     *
     * @param string $name
     * @param mixed $value
     * @param boolean $clear_for_cache
     * @return mixed
     * @throws Exception
     */
    static function setValue($name, $value = null, $clear_for_cache = false) {
      try {
        DB::beginWork('Setting configuration values @ ' . __CLASS__);

        $to_set = is_array($name) ? $name : array($name => $value);
        
        foreach($to_set as $k => $v) {
          if(self::exists($k, false)) {
            DB::execute('UPDATE ' . TABLE_PREFIX . 'config_options SET value = ? WHERE name = ?', serialize($v), $k);
          } else {
            throw new ConfigOptionDnxError($k);
          } // if
        } // foreach

        AngieApplication::cache()->remove('config_options');
        
        if($clear_for_cache) {
        	AngieApplication::cache()->clearModelCache();
        } // if
        
        DB::commit('Configuration values set @ ' . __CLASS__);
      } catch(Exception $e) {
        DB::rollback('Failed to set configuration values @ ' . __CLASS__);
        
        throw $e;
      } // try
      
      return $name === $to_set ? $to_set : $value; // Return what we have just set
    } // setValue
    
    /**
     * Get value for a given parent object
     *
     * @param string $name
     * @param IConfigContext $for
     * @param boolean $use_cache
     * @return mixed
     */
    static function getValueFor($name, IConfigContext $for, $use_cache = true) {
      $find = (array) $name;
      
      $cached_values = AngieApplication::cache()->getByObject($for, 'config_options');
      $values = array();
      
      foreach($find as $option) {
        if($use_cache && is_array($cached_values) && array_key_exists($option, $cached_values)) {
          $values[$option] = $cached_values[$option];
          continue;
        } // if
        
        if($row = DB::executeFirstRow('SELECT value FROM ' . TABLE_PREFIX . 'config_option_values WHERE name = ? AND parent_type = ? AND parent_id = ?', $option, ConfigOptions::getParentTypeByObject($for), $for->getId())) {
          $values[$option] = $row['value'] ? unserialize($row['value']) : null;
        } else {
          $values[$option] = self::getValue($option, $use_cache);
        } // if
        
        if(is_array($cached_values)) {
          $cached_values[$option] = $values[$option];
        } else {
          $cached_values = array($option => $values[$option]);
        } // if
      } // foreach
      AngieApplication::cache()->setByObject($for, 'config_options', $cached_values);
      
      return $find === $name ? $values : array_shift($values);
    } // getValueFor
    
    /**
     * Returns true if there is a value for given config options
     *
     * @param string $name
     * @param IConfigContext $for
     * @return boolean
     */
    static function hasValueFor($name, IConfigContext $for) {
      if(is_array($name)) {
        $options = array_unique($name);
      } else {
        $options = array($name);
      } // if
      
      return ((integer) DB::executeFirstCell("SELECT COUNT(*) AS 'row_count' FROM " . TABLE_PREFIX . 'config_option_values WHERE name = ? AND parent_type = ? AND parent_id = ?', $options, ConfigOptions::getParentTypeByObject($for), $for->getId())) == count($name);
    } // hasValueFor
    
    /**
     * Set value for a given parent object
     *
     * @param string $name
     * @param IConfigContext $for
     * @param mixed $value
     * @return array|null
     * @throws Exception
     */
    static function setValueFor($name, IConfigContext $for, $value = null) {
      $config_option_values_table = TABLE_PREFIX . 'config_option_values';
      
      try {
        DB::beginWork('Setting configuration option for object @ ' . __CLASS__);
        
        $cached_values = AngieApplication::cache()->getByObject($for, 'config_options');
        
        $to_set = is_array($name) ? $name : array($name => $value);
        foreach($to_set as $k => $v) {
          if(self::exists($k, false)) {
            if((integer) DB::executeFirstCell("SELECT COUNT(*) AS 'row_count' FROM $config_option_values_table WHERE name = ? AND parent_type = ? AND parent_id = ?", $k, ConfigOptions::getParentTypeByObject($for), $for->getId())) {
            	if($v === null) {
            		DB::execute("DELETE FROM $config_option_values_table WHERE name = ? AND parent_type = ? AND parent_id = ?", $v, ConfigOptions::getParentTypeByObject($for), $for->getId());
            	} else {
            		DB::execute("UPDATE $config_option_values_table SET value = ? WHERE name = ? AND parent_type = ? AND parent_id = ?", serialize($v), $k, ConfigOptions::getParentTypeByObject($for), $for->getId());
            	} // if
            } else {
              DB::execute("INSERT INTO $config_option_values_table (name, parent_type, parent_id, value) VALUES (?, ?, ?, ?)", $k, ConfigOptions::getParentTypeByObject($for), $for->getId(), serialize($v));
            } // if
            
            if($v === null) {
            	if(is_array($cached_values) && isset($cached_values[$k])) {
            		unset($cached_values[$k]);
            	} // if
            } else {
            	if(is_array($cached_values)) {
	              $cached_values[$k] = $v;
	            } else {
	              $cached_values = array($k => $v);
	            } // if
            } // if
          } else {
            throw new ConfigOptionDnxError($name);
          } // if
        } // foreach
        
        AngieApplication::cache()->setByObject($for, 'config_options', $cached_values);
        
        DB::commit('Configuration option values set for object @ ' . __CLASS__);
      } catch(Exception $e) {
        DB::rollback('Failed to set configuration option values for object @ ' . __CLASS__);
        
        throw $e;
      } // try
      
      return $name === $to_set ? $to_set : $value; // Return what we have just set
    } // setValueFor

    /**
     * Remove all custom values for given option
     *
     * @param string $name
     */
    static function removeValues($name) {
      DB::execute('DELETE FROM ' . TABLE_PREFIX . 'config_option_values WHERE name IN (?)', $name);
      AngieApplication::cache()->clear();
    } // removeValues
    
    /**
     * Remove all values for a given parent object
     *
     * @param IConfigContext $for
     * @param mixed $specific
     * @return bool
     */
    static function removeValuesFor(IConfigContext $for, $specific = null) {
      if($specific) {
        DB::execute('DELETE FROM ' . TABLE_PREFIX . 'config_option_values WHERE name IN (?) AND parent_type = ? AND parent_id = ?', (array) $specific, ConfigOptions::getParentTypeByObject($for), $for->getId());
      } else {
        DB::execute('DELETE FROM ' . TABLE_PREFIX . 'config_option_values WHERE parent_type = ? AND parent_id = ?', ConfigOptions::getParentTypeByObject($for), $for->getId());
      } // if

      AngieApplication::cache()->removeByObject($for, 'config_options');
    } // removeValuesFor

    /**
     * Clone custom configuration options from source to target object
     *
     * @param IConfigContext $from
     * @param IConfigContext $to
     * @throws Exception
     */
    static function cloneValuesFor(IConfigContext $from, IConfigContext $to) {
      $config_option_values_table = TABLE_PREFIX . 'config_option_values';

      $rows = DB::execute("SELECT name, value FROM $config_option_values_table WHERE parent_type = ? AND parent_id = ?", ConfigOptions::getParentTypeByObject($from), $from->getId());

      if($rows) {
        $escaped_parent_type = DB::escape(ConfigOptions::getParentTypeByObject($to));
        $escaped_parent_id = DB::escape($to->getId());

        try {
          DB::beginWork('Cloning custom config option values @ ' . __CLASS__);

          foreach($rows as $row) {
            DB::execute("REPLACE INTO $config_option_values_table (name, parent_type, parent_id, value) VALUES (?, $escaped_parent_type, $escaped_parent_id, ?)", $row['name'], $row['value']);
          } // foreach

          DB::commit('Clonned custom config option values @ ' . __CLASS__);
        } catch(Exception $e) {
          DB::rollback('Failed to clone custom config option values @ ' . __CLASS__);

          throw $e;
        } // try
      } // if
    } // cloneValuesFor
    
    /**
     * Return number of custom values for given option
     *
     * @param string $name
     * @param mixed $value
     * @param array $exclude
     * @return integer
     */
    static function countByValue($name, $value, $exclude = null) {
      $exclude_filter = '';
      
      if(is_foreachable($exclude)) {
        $exclude_filter = array();
        
        foreach($exclude as $exclude_object) {
          $exclude_filter[] = DB::prepare('(parent_type = ? AND parent_id = ?)', ConfigOptions::getParentTypeByObject($exclude_object), $exclude_object->getId());
        } // foreach
        
        $exclude_filter = ' AND NOT (' . implode(' OR ', $exclude_filter) . ')';
      } // if
      
      return (integer) DB::executeFirstCell("SELECT COUNT(*) AS 'row_count' FROM " . TABLE_PREFIX . 'config_option_values WHERE name = ? AND value = ? ' . $exclude_filter, $name, serialize($value));
    } // countByValue
    
    /**
     * Remove all custom values by $name and $value
     * 
     * This method is useful when we need to clean up custom values when 
     * something system wide is changed (language or filter is removed etc)
     *
     * @param string $name
     * @param mixed $value
     */
    static function removeByValue($name, $value) {
      DB::execute("DELETE FROM " . TABLE_PREFIX . 'config_option_values WHERE name = ? AND value = ?', $name, serialize($value));
      AngieApplication::cache()->clearModelCache();
    } // removeByValue
    
    // ---------------------------------------------------
    //  Management
    // ---------------------------------------------------
    
    /**
     * Cached array of exists value
     *
     * @var array
     */
    static private $exists_cache = array();
    
    /**
     * Check if specific configuration option exists
     *
     * @param string $name
     * @param boolean $use_cache
     * @return boolean
     */
    static function exists($name, $use_cache = true) {
      if(!array_key_exists($name, self::$exists_cache) || !$use_cache) {
        self::$exists_cache[$name] = (boolean) DB::executeFirstCell("SELECT COUNT(*) AS 'row_count' FROM " . TABLE_PREFIX . 'config_options WHERE name = ?', $name);
      } // if
      
      return self::$exists_cache[$name];
    } // exists
    
    /**
     * Define new option
     *
     * @param string $name
     * @param string $module
     * @param mixed $default_value
     * @throws Error
     */
    static function addOption($name, $module = 'system', $default_value = null) {
      if(empty($name)) {
        throw new Error('Configuration option name is required');
      } // if
      if(empty($module)) {
        throw new Error('Configuration option needs to be associated with a module');
      } // if
      
      DB::execute('INSERT INTO ' . TABLE_PREFIX . 'config_options (name, module, value) VALUES (?, ?, ?)', $name, $module, serialize($default_value));
      AngieApplication::cache()->remove('config_options');
    } // addOption
    
    /**
     * Remove option definition
     *
     * @param string $name
     */
    static function removeOption($name) {
      DB::transact(function() use ($name) {
        DB::execute('DELETE FROM ' . TABLE_PREFIX . 'config_options WHERE name = ?', $name);
        DB::execute('DELETE FROM ' . TABLE_PREFIX . 'config_option_values WHERE name = ?', $name);

        AngieApplication::cache()->remove('config_options');
        AngieApplication::cache()->clearModelCache();
      }, 'Removing config option');
    } // removeOption
    
    /**
     * Remove all options by module
     *
     * @param AngieModule $module
     */
    static function deleteByModule($module) {
      DB::transact(function() use ($module) {
        $rows = DB::executeFirstColumn('SELECT name FROM ' . TABLE_PREFIX . 'config_options WHERE module = ?', $module);
        if($rows) {
          DB::execute('DELETE FROM ' . TABLE_PREFIX . 'config_options WHERE name IN (?)', $rows);
          DB::execute('DELETE FROM ' . TABLE_PREFIX . 'config_option_values WHERE name IN (?)', $rows);
        } // if

        AngieApplication::cache()->remove('config_options');
        AngieApplication::cache()->clearModelCache();
      }, 'Removing config option by module');
    } // deleteByModule
    
    // ---------------------------------------------------
    //  Utility
    // ---------------------------------------------------

    /**
     * Return parent type based on object instance
     *
     * @param mixed $object
     * @return mixed|string
     * @throws InvalidParamError
     */
    static private function getParentTypeByObject($object) {
      if($object instanceof DataObject) {
        return $object->getModelName(false, true);
      } elseif(is_object($object)) {
        return get_class($object);
      } else {
        throw new InvalidParamError('object', $object, '$object is not an object');
      } // if
    } // getParentTypeByObject
    
  }