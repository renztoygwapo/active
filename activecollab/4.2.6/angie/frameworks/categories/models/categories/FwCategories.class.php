<?php

  /**
   * Framework level category management implementation
   *
   * @package angie.frameworks.categories
   * @subpackage models
   */
  abstract class FwCategories extends BaseCategories {
    
    /**
     * Return categories based on input parameters
     * 
     * Result can be filtered by parent or type, both or none (all categories)
     *
     * @param mixed $parent
     * @param string $type
     */
    static function findBy($parent = null, $type = null) {
      $conditions = array();
      
      if($parent) {
        if($parent instanceof ICategoriesContext) {
          $conditions[] = DB::prepare('(parent_type = ? AND parent_id = ?)', get_class($parent), $parent->getId());
        } else {
          throw new InvalidInstanceError('parent', $parent, 'ICategoriesContext', '$parent is expected to be ICategoriesContext instance');
        } // if
      } // if
      
      if($type) {
        $conditions[] = DB::prepare('(type = ?)', $type);
      } // if
      
      return Categories::find(array(
        'conditions' => count($conditions) ? implode(' AND ', $conditions) : null, 
        'order' => 'name', 
      ));
    } // find
    
    /**
     * Return category ID - name map based on input parameters
     * 
     * Result can be filtered by parent or type, both or none (all categories)
     *
     * @param mixed $parent
     * @param string $type
     * @return array|null
     * @throws InvalidInstanceError
     */
    static function getIdNameMap($parent = null, $type = null) {
      $conditions = array();

      $cache_key = null;

      if($parent) {
        if($parent instanceof ICategoriesContext) {
          $conditions[] = DB::prepare('(parent_type = ? AND parent_id = ?)', get_class($parent), $parent->getId());
        } else {
          throw new InvalidInstanceError('parent', $parent, 'ICategoriesContext');
        } // if
      } // if
      
      if($type) {
        $conditions[] = DB::prepare('(type IN (?))', $type);
      } // if

      if ($parent && is_string($type)) {
        $cache_key = "categories_" . strtolower($type);

        $cached_values = AngieApplication::cache()->getByObject($parent, $cache_key);

        if ($cached_values) {
          return $cached_values;
        } // if
      } // if
      
      if(count($conditions)) {
        $rows = DB::execute('SELECT id, name FROM ' . TABLE_PREFIX . 'categories WHERE ' . implode(' AND ', $conditions) . ' ORDER BY name');
      } else {
        $rows = DB::execute('SELECT id, name FROM ' . TABLE_PREFIX . 'categories ORDER BY name');
      } // if
      
      if($rows) {
        $result = array();
        
        foreach($rows as $row) {
          $result[(integer) $row['id']] = $row['name'];
        } // foreach

        if (!is_null($cache_key)) {
          AngieApplication::cache()->setByObject($parent, $cache_key, $result);
        } // if

        return $result;
      } else {
        return null;
      } // if
    } // getIdNameMap
    
    /**
     * Return category ID-s by list of category names
     * 
     * @param array $names
     * @param string $type
     * @param ICategoriesContext $parent
     * @return array
     */
    static function getIdsByNames($names, $type, $parent = null) {
      if($names) {
        if($parent instanceof ICategoriesContext) {
          $ids = DB::executeFirstColumn('SELECT id FROM ' . TABLE_PREFIX . 'categories WHERE parent_type = ? AND parent_id = ? AND name IN (?) AND type = ?', get_class($parent), $parent->getId(), $names, $type);
        } else {
          $ids = DB::executeFirstColumn('SELECT id FROM ' . TABLE_PREFIX . 'categories WHERE name IN (?) AND type = ?', $names, $type);
        } // if
        
        if($ids) {
          foreach($ids as $k => $v) {
            $ids[$k] = (integer) $v;
          } // foreach
        } // if
        
        return $ids;
      } else {
        return null;
      } // if
    } // getIdsByNames
    
    /**
     * Search target context for category with the given name and return its ID 
     * if it exists
     *
     * @param integer $id_in_source_context
     * @param ICategoriesContext $target_context
     * @return integer
     */
    static function getMatchingCategoryId($id_in_source_context , ICategoriesContext $target_context) {
      $category = DB::executeFirstRow('SELECT type, name FROM ' . TABLE_PREFIX . 'categories WHERE id = ?', $id_in_source_context);
      if($category) {
        $id_in_target_context = DB::executeFirstCell('SELECT id FROM ' . TABLE_PREFIX . 'categories WHERE parent_type = ? AND parent_id = ? AND type = ? AND name = ?', get_class($target_context), $target_context->getId(), $category['type'], $category['name']);
        
        if($id_in_source_context) {
          return (integer) $id_in_target_context;
        } // if
      } // if
      
      return null;
    } // getMatchingCategoryId

    /**
     * Remove all categories based on category type
     *
     * @param string $type
     */
    static function deleteByType($type) {
      DB::transact(function() use ($type) {
        DB::execute('DELETE FROM ' . TABLE_PREFIX . 'categories WHERE type = ?', $type);
        DB::execute('DELETE FROM ' . TABLE_PREFIX . 'modification_logs WHERE parent_type = ?', $type);
      }, 'Delete categories by type @ ' . __CLASS__);
    } // deleteByType

    /**
     * Remove all categories based on category parent
     *
     * @param int $parent_id
     */
    static function deleteByParent($parent_id) {
      DB::transact(function() use ($parent_id) {
        DB::execute('DELETE FROM ' . TABLE_PREFIX . 'categories WHERE parent_id = ?', $parent_id);
        DB::execute('DELETE FROM ' . TABLE_PREFIX . 'modification_logs WHERE parent_id = ?', $parent_id);
      }, 'Delete categories by parent ID @ ' . __CLASS__);
    } // deleteByParent
    
    /**
     * Get event namespace by type
     * 
     * @param string $type
     * @return string
     */
    static function getEventNamespaceByType($type = null) {
    	return Inflector::underscore($type) . '_updated';
    } // getEventNamespaceByType

    /**
     * @param null|ICategoriesContext $object
     * @param null|string $type
     */
    static function dropCache($object = null, $type = null) {
      if ($object instanceof ICategoriesContext && is_string($type)) {
        AngieApplication::cache()->removeByObject($object, "categories_" . $type);
      } // if
    } // dropCache
    
  }