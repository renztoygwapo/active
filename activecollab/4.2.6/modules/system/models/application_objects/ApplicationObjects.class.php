<?php

  /**
   * Application objects manager
   * 
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class ApplicationObjects extends FwApplicationObjects {
    
    /**
     * Return all context domains that given user can access
     * 
     * activeCollab level ApplicationObjects ignores $callback parameter!
     * 
     * @param IUser $user
     * @param Closure $callback
     */
    static function getContextDomains(IUser $user, $callback = null) {
      return parent::getContextDomains($user, function (&$contexts) {
        if($contexts) {
          array_remove_by_value($contexts, 'users'); // Replaced with people context in activeCollab
        } else {
          $contexts = array();
        } // if
        
        $contexts[] = 'projects';
        $contexts[] = 'people';
      });
    } // getContextDomains
    
    /**
     * Rebuild project object contexts
     * 
     * @param array $types
     * @param string $project_context
     */
    static function rebuildProjectObjectContexts($types, $project_context) {
      $project_objects_table = TABLE_PREFIX . 'project_objects';
      
      try {
        DB::beginWork('Updating project object activity logs @ ' . __CLASS__);
        
        $objects = DB::execute("SELECT id, type, project_id, visibility FROM $project_objects_table WHERE type IN (?) AND state >= ?", $types, STATE_DELETED);
        if($objects instanceof DBResult) {
          $objects->setCasting(array(
            'id' => DBResult::CAST_INT, 
            'project_id' => DBResult::CAST_INT, 
            'visibility' => DBResult::CAST_INT,  
          ));
          
          $batch = DB::batchInsert(TABLE_PREFIX . 'object_contexts', array('parent_type', 'parent_id', 'context'));
          
          foreach($objects as $object) {
            $batch->insert($object['type'], $object['id'], "projects:projects/$object[project_id]/$project_context/" . ($object['visibility'] == VISIBILITY_PRIVATE ? 'private' : 'normal') . "/$object[id]");
          } // foreach
          
          $batch->done();
        } // if
        
        DB::commit('Project object activity logs updated @ ' . __CLASS__);
      } catch(Exception $e) {
        DB::rollback('Failed to update project object activity logs');
        throw $e;
      } // try
    } // rebuildProjectObjectContexts
    
  }