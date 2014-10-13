<?php

  /**
   * Module used to define activeCollab project section (files, discussions etc)
   * 
   * @package activeCollab.resources
   */
  class ActiveCollabProjectSectionModule extends AngieModule {
    
    /**
     * Name of the project object class (or classes) that this module uses
     *
     * @var string
     */
    protected $project_object_classes;

    /**
     * Name of category class used by this section
     *
     * @var string
     */
    protected $category_class;
    
    /**
     * Return project object classes that are managed with this module
     * 
     * @return array
     */
    protected function getProjectObjectClasses() {
      if($this->project_object_classes) {
        return is_array($this->project_object_classes) ? $this->project_object_classes : array($this->project_object_classes);
      } else {
        return null;
      } // if
    } // getProjectObjectClasses

    /**
     * Return category class used by this module
     *
     * @return string
     */
    protected function getCategoryClass() {
      return $this->category_class;
    } // getCategoryClass
    
    /**
     * Uninstall project object related module
     */
    function uninstall() {
      if($this->isInstalled()) {
        
        // First do the clean-up of module related project data
        try {
          DB::beginWork('Uninstalling module @ ' . __CLASS__);
          
          $classes = $this->getProjectObjectClasses();
          
          if(is_foreachable($classes)) {
            $project_objects_table = TABLE_PREFIX . 'project_objects';
            
            DB::execute("DELETE FROM $project_objects_table WHERE type IN (?)", $classes);
            
            $class_name = first($classes);
            
            if(class_exists($class_name, true)) {
              $instance = new $class_name();
              
              if($instance instanceof ProjectObject) {
                Reminders::deleteByParentTypes($classes);
                ActivityLogs::deleteByParentTypes($classes);
                
                // Comments
                if($instance instanceof IComments) {
                  Comments::deleteByParentTypes($classes);
                } // if
                
                // Subtasks
                if($instance instanceof ISubtasks) {
                  Subtasks::deleteByParentTypes($classes);
                } // if
                
                // Attachments
                if($instance instanceof IAttachments) {
                  Attachments::deleteByParentTypes($classes);
                } // if
                
                // Assignees
                if($instance instanceof IAssignees) {
                  Assignments::deleteByParentTypes($classes);
                } // if
                
                // Subscriptions
                if($instance instanceof ISubscriptions) {
                  Subscriptions::deleteByParentTypes($classes);
                } // if
                
                // Favorites
                if($instance instanceof ICanBeFavorite) {
                  Favorites::deleteByParentTypes($classes);
                } // if
                
                if($instance instanceof ISharing) {
                  SharedObjectProfiles::deleteByParentTypes($classes);
                } // if
                
                if(AngieApplication::isModuleLoaded('tracking') && $instance instanceof ITracking) {
                  Estimates::deleteByParentTypes($classes);
                  Expenses::deleteByParentTypes($classes);
                  TimeRecords::deleteByParentTypes($classes);
                } // if
              } // if
            } // if
          } // if

          if($this->getCategoryClass()) {
            Categories::deleteByType($this->getCategoryClass());
          } // if
          
          DB::commit('Module uninstalled @ ' . __CLASS__);
        } catch(Exception $e) {
          DB::rollback('Failed to uninstall module @ ' . __CLASS__);
          throw $e;
        } // try
        
        // And than uninstall the module, clear cache etc
        parent::uninstall();
      } else {
        throw new Error("Module $this->name is not installed");
      } // if
    } // uninstall
  
  }