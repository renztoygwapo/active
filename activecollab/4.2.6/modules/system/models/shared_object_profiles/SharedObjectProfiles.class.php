<?php

  /**
   * SharedObjectProfiles class
   *
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class SharedObjectProfiles extends BaseSharedObjectProfiles {
    
    /**
     * Return sharing profile by parent
     * 
     * @param ISharing $parent
     * @return SharedObjectProfile
     */
    static function findByParent(ISharing $parent) {
     
     return SharedObjectProfiles::find(array(
        'conditions' => array('parent_type = ? AND parent_id = ?', get_class($parent), $parent->getId()), 
        'one' => true, 
      ));
    } // findByParent
    
    /**
     * Return parent instance by sharing context and code
     * 
     * @param string $context
     * @param string $code
     * @return ISharing
     */
    static function findParentByContextAndCode($context, $code) {
      $row = DB::executeFirstRow('SELECT parent_type, parent_id FROM ' . TABLE_PREFIX . 'shared_object_profiles WHERE sharing_context = ? AND sharing_code = ? LIMIT 0, 1', $context, $code);
      
      if($row) {
        $parent_type = $row['parent_type'];
        
        if(class_exists($parent_type, true)) {
          $parent = new $parent_type($row['parent_id']);
          
          if($parent instanceof ISharing && $parent->isLoaded()) {
            return $parent;
          } // if
        } // if
      } // if
      
      return null;
    } // findParentByContextAndCode
  
    /**
     * Return unique sharing code for given context
     * 
     * @param string $context
     */
    static function getUniqueCodeForContext($context) {
      do {
        $code = make_string(13);
      } while(SharedObjectProfiles::existsByContextAndCode($context, $code));
      
      return $code;
    } // getUniqueCodeForContext
    
    /**
     * Return unique code from context and name
     * @param unknown_type $context
     * @param unknown_type $name
     */
    static function getUniqueCodeForContextAndName($context, $name) {
      $start_with = Inflector::slug($name);
      $counter = 0;
      
      do {
        $code = $start_with;
        
        if($counter) {
          $code = "$code-$counter";
        } // if
        
        $counter++;
      } while(SharedObjectProfiles::existsByContextAndCode($context, $code));
      
      return $code;
    } // getUniqueCodeForContext
    
    /**
     * Return true if that's a sharing profile for given context and code
     * 
     * @param string $context
     * @param string $code
     * @return boolean
     */
    static function existsByContextAndCode($context, $code) {
      return (boolean) DB::executeFirstCell('SELECT COUNT(id) FROM ' . TABLE_PREFIX . 'shared_object_profiles WHERE sharing_context = ? AND sharing_code = ?', $context, $code);
    } // existsByContextAndCode
    
    /**
     * Delete entries by parents
     * 
     * $parents is an array where key is parent type and value is array of 
     * object ID-s of that particular parent
     * 
     * @param array $parents
     */
    static function deleteByParents($parents) {
      if(is_foreachable($parents)) {
        foreach($parents as $parent_type => $parent_ids) {
          DB::execute('DELETE FROM ' . TABLE_PREFIX . 'shared_object_profiles WHERE parent_type = ? AND parent_id IN (?)', $parent_type, $parent_ids);
        } // foreach
      } // if
    } // deleteByParents
    
    /**
     * Delete by parent types
     * 
     * @param array $types
     */
    static function deleteByParentTypes($types) {
      DB::execute('DELETE FROM ' . TABLE_PREFIX . 'shared_object_profiles WHERE parent_type IN (?) ', $types);
    } // deleteByParentTypes
  
  }