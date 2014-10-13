<?php

  /**
   * Project categories context
   *
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class IProjectCategoriesContextImplementation extends ICategoriesContextImplementation {
    
    /**
     * Enter description here...
     *
     * @param ICategoriesContext $object
     * @throws InvalidInstanceError
     */
    function __construct(ICategoriesContext $object) {
      if($object instanceof Project) {
        parent::__construct($object);
      } else {
        throw new InvalidInstanceError('object', $object, 'Project');
      } // if
    } // __construct
    
    /**
     * Returns true if $user can manage categories of a given type
     *
     * @param User $user
     * @param string $type
     * @return boolean
     * @throws InvalidParamError
     */
    function canManage(User $user, $type = null) {
      if(empty($type)) {
        throw new InvalidParamError('type', $type, 'Type value is required');
      } // if
      
      return $user->isProjectManager() || $this->object->isLeader($user) || ($user->projects()->getPermission(Inflector::underscore(substr($type, 0, -8)), $this->object) >= ProjectRole::PERMISSION_MANAGE);
    } // canManage
    
    /**
     * Import master categories into parent project
     *
     * @param User $by
     * @throws Exception
     */
    function importMasterCategories(User $by) {
      try {
        DB::beginWork('Importing master categories @ ' . __CLASS__);
        
        $category_definitions = array();
        EventsManager::trigger('on_master_categories', array(&$category_definitions));
        
        if(is_foreachable($category_definitions)) {
          foreach($category_definitions as $category_definition) {
            $category_type = $category_definition['type'];
            
            $default_categories = $category_definition['value'];
            if(!is_foreachable($default_categories)) {
              $default_categories = array('General');
            } // if
            
            if(is_foreachable($category_definition['value'])) {
              foreach($category_definition['value'] as $category_name) {
                if(trim($category_name) != '') {
                  $category = new $category_type();
                  
                  $category->setParent($this->object);
                  $category->setName($category_name);
                  $category->setCreatedBy($by);
                  
                  $category->save();
                } // if
              } // foreach
            } // if
          } // foreach
        } // if
        
        DB::commit('Master categories imported @ ' . __CLASS__);
      } catch(Exception $e) {
        DB::rollback('Failed to import master categories @ ' . __CLASS__);
        
        throw $e;
      } // try
    } // importMasterCategories
    
    /**
     * Get event namespace by type
     * 
     * @param string $type
     * @return string
     */
    function getEventNamespaceByType($type = null) {
    	return FwCategories::getEventNamespaceByType($type).'.'.'project_'.$this->object->getId();
    } // getEventNamespaceByType
    
  }