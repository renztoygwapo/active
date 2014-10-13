<?php

  /**
   * Project object category implementation
   *
   * @package activeCollab.modules.system
   * @subpackage models
   */
  abstract class ProjectObjectCategory extends Category {
    
    /**
     * Copy project to a category
     *
     * @param Project $project
     * @param boolean $bulk
     * @return ProjectObjectCategory
     * @throws Exception
     */
    function copyToProject(Project $project, $bulk = false) {
      try {
        DB::beginWork('Copy project object category to a project @ ' . __CLASS__);
      
        $copy = $this->copy();
        $copy->setParent($project);
        $copy->save();
        
        EventsManager::trigger('on_project_object_category_copied', array(&$this, &$copy, &$project));
        DB::commit('Project object category copied to a project @ ' . __CLASS__);
      } catch(Exception $e) {
        DB::rollback('Failed to copy project object category to a project @ ' . __CLASS__);
        throw $e;
      } // try
      
      return $copy;
    } // copyToProject
    
    // ---------------------------------------------------
    //  Interfaces
    // ---------------------------------------------------
    
    /**
     * Routing context parameters
     *
     * @var array
     */
    private $routing_context_params = false;
    
    /**
     * Return routing context parameters
     *
     * @return mixed
     */
    function getRoutingContextParams() {
      if($this->routing_context_params === false) {
        $this->routing_context_params = array(
          'project_slug' => $this->getParent()->getSlug(), 
          'category_id' => $this->getId(), 
        );
      } // if
      
      return $this->routing_context_params;
    } // getRoutingContextParams
    
    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------
    
    /**
     * Returns true if $user can rename this category
     *
     * @param User $user
     * @return boolean
     */
    function canEdit(User $user) {
      return $user->isProjectManager() || $this->getParent()->isLeader($user);
    } // canEdit
    
    /**
     * Returns true if user can delete this category
     *
     * @param User $user
     * @return boolean
     */
    function canDelete(User $user) {
      return $user->isProjectManager() || $this->getParent()->isLeader($user);
    } // canDelete
    
    // ---------------------------------------------------
    //  System
    // ---------------------------------------------------
    
    /**
     * Validate before save
     *
     * @param ValidationErrors $errors
     */
    function validate(ValidationErrors &$errors) {
      if(!$this->validatePresenceOf('parent_type') || !$this->validatePresenceOf('parent_id')) {
        $errors->addError(lang('Project is required'), 'parent');
      } // if
      
      parent::validate($errors);
    } // validate
    
    /**
     * Delete this category
     *
     * @throw Exception
     */
    function delete() {
      try {
        DB::beginWork('Deleting category @ ' . __CLASS__);
        
        // Delete parent category
        parent::delete();

        // we have to unlink all project objects that belonged to that category
        $objects = DB::execute('SELECT id, type FROM ' . TABLE_PREFIX . 'project_objects WHERE category_id = ?', $this->getId());
        if (is_foreachable($objects)) {
          $by = Authentication::getLoggedUser();
          $on = new DateTimeValue();

          // create modification logs
          $modifications = array();
          foreach ($objects as $object) {
            DB::execute('INSERT INTO ' . TABLE_PREFIX . 'modification_logs (parent_type, parent_id, created_on, created_by_id, created_by_name, created_by_email, is_first) VALUES (?, ?, ?, ?, ?, ?, ?)', $object['type'], $object['id'], $on, $by->getId(), $by->getDisplayName(), $by->getEmail(), 0);
            $modifications[] = DB::prepare('(?, ?, ?)', DB::lastInsertId(), 'category_id', null);
          } // foreach

          // insert modification logs values
          if (is_foreachable($modifications)) {
            DB::execute('INSERT INTO ' . TABLE_PREFIX .'modification_log_values (modification_id, field, value) VALUES ' . implode(', ', $modifications));
          } // if

          // unset category_id field for all objects
          DB::execute('UPDATE ' . TABLE_PREFIX . 'project_objects SET category_id = ? WHERE category_id = ?', null, $this->getId());

          // remove  cache
          AngieApplication::cache()->removeByModel('project_objects');
        } // if

        DB::commit('Category deleted @ ' . __CLASS__);
      } catch(Exception $e) {
        DB::rollback('Failed to delete category @ ' . __CLASS__);
        throw $e;
      } // try
    } // delete
    
  }