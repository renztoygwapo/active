<?php

  /**
   * Framework level application objects implementation
   * 
   * @package angie.frameworks.environment
   * @subpackage models
   */
  abstract class FwApplicationObjects {
    
    /**
     * Cached context domains
     *
     * @var array
     */
    static private $context_domains = array();
    
    /**
     * Return all context domains that given user can access
     * 
     * @param IUser $user
     */
    static function getContextDomains(IUser $user, $callback = null) {
      $user_id = $user->getId();
      
      if(!isset(self::$context_domains[$user_id])) {
        $contexts = array();
        
        self::$context_domains[$user_id] = array();
        EventsManager::trigger('on_context_domains', array(&$user, &$contexts));
        
        if($callback instanceof Closure) {
          $callback($contexts);
        } // if
        
        self::$context_domains[$user_id] = $contexts;
      } // if
      
      return self::$context_domains[$user_id];
    } // getContextDomains
    
    /**
     * Return contexts that given user can and can't see
     * 
     * @param IUser $user
     * @param ApplicationObject $in
     * @param array $exclude
     * @return array
     */
    static function getVisibileContexts(IUser $user, $in = null, $include_domains = null) {
      $contexts = array();
      $ignore_contexts = array();
      
      EventsManager::trigger('on_visible_contexts', array(&$user, &$contexts, &$ignore_contexts, $in, $include_domains));
      
      return array($contexts, $ignore_contexts);
    } // getVisibileContexts
    
    // ---------------------------------------------------
    //  Remember / forget object context
    // ---------------------------------------------------
    
    /**
     * Return array of actions that are used for system to rebuild object contexts
     * 
     * @return array
     */
    static function getRebuildContextsActions() {
      $actions = array(Router::assemble('object_contexts_admin_clean') => lang('Clean up existing log entries'));
      EventsManager::trigger('on_rebuild_object_contexts_actions', array(&$actions));
      
      return $actions;
    } // getRebuildContextsActions
    
    /**
     * Calculate size of activity log index
     * 
     * @return integer
     */
    static function calculateObjectContextsIndexSize() {
      $row = DB::executeFirstRow('SHOW TABLE STATUS LIKE ?', TABLE_PREFIX . 'object_contexts');
      
      if($row && isset($row['Data_length']) && isset($row['Index_length'])) {
        return $row['Data_length'] + $row['Index_length'];
      } else {
        return 0;
      } // if
    } // calculateObjectContextsIndexSize
    
    /**
     * Return object context for a given object
     * 
     * @param IObjectContext $object
     * @return string
     */
    static function getContext(IObjectContext $object) {
      return $object->getObjectContextDomain() . ':' . $object->getObjectContextPath();
    } // getContext
    
    /**
     * Return context for a given object that we have cached in the database
     * 
     * @param IObjectContext $object
     * @return string
     */
    static function getRememberedContext(IObjectContext $object) {
      return DB::executeFirstCell('SELECT context FROM ' . TABLE_PREFIX . 'object_contexts WHERE parent_type = ? AND parent_id = ?', get_class($object), $object->getId());
    } // getRememberedContext
    
    /**
     * Cache context of a given object
     * 
     * @param IObjectContext $object
     * @return string
     */
    static function rememberContext(IObjectContext $object) {
      $context = $object->getObjectContextDomain() . ':' . $object->getObjectContextPath();
      DB::execute('INSERT INTO ' . TABLE_PREFIX . 'object_contexts (parent_type, parent_id, context) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE context=VALUES(context)', get_class($object), $object->getId(), $context);
      return $context;
    } // rememberContext
    
    /**
     * Update remembered context
     * 
     * @param IObjectContext $object
     * @return string
     * @throws Exception
     */
    static function updateRememberedContext(IObjectContext $object) {
      $remembered_context = self::getRememberedContext($object);
      
      if($remembered_context) {
        $new_context = $object->getObjectContextDomain() . ':' . $object->getObjectContextPath();
        
        if($remembered_context != $new_context) {
          try {
            DB::beginWork("Updating object context from '$remembered_context' to '$new_context' @ " . __CLASS__);
          
            $rows = DB::execute('SELECT id, context FROM ' . TABLE_PREFIX . 'object_contexts WHERE context LIKE ?', "$remembered_context%");
            if($rows) {
              foreach($rows as $row) {
                DB::execute('UPDATE ' . TABLE_PREFIX . 'object_contexts SET context = ? WHERE id = ?', str_replace($remembered_context, $new_context, $row['context']), $row['id']);
              } // foreach
            } // if
            
            EventsManager::trigger('on_object_context_changed', array(&$object, &$remembered_context, &$new_context));
            
            DB::commit("Object context updated from '$remembered_context' to '$new_context' @ " . __CLASS__);
          } catch(Exception $e) {
            DB::rollback("Failed to update object context from '$remembered_context' to '$new_context' @ " . __CLASS__);
            throw $e;
          } // try
          
          return $new_context;
        } else {
          return $remembered_context;
        } // if
      } else {
        return self::rememberContext($object);
      } // if
    } // updateRememberedContext
    
    /**
     * Forget context for a given object
     * 
     * @param IObjectContext $object
     */
    static function forgetContexts(IObjectContext $object) {
      DB::execute('DELETE FROM ' . TABLE_PREFIX . 'object_contexts WHERE parent_type = ? AND parent_id = ?', get_class($object), $object->getId());
    } // forgetContexts

    /**
     * Delete object contexts by parent types
     *
     * @param array $types
     */
    static function cleanUpContextsByParentTypes($types) {
      if($types) {
        DB::execute('DELETE FROM ' . TABLE_PREFIX . 'object_contexts WHERE parent_type IN (?)', $types);
      } // if
    } //cleanUpContextsByParentTypes
    
    /**
     * Clean up object contexts
     */
    static function cleanUpContexts() {
      DB::execute('DELETE FROM ' . TABLE_PREFIX . 'object_contexts');
    } // cleanUpContexts
    
  }