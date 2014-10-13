<?php

  /**
   * Project object specific comment implementation
   *
   * @package activeCollab.modules.system
   * @subpackage model
   */
  abstract class ProjectObjectComment extends Comment {
    
    /**
     * Parepare object options
     *
     * @param User $user
     * @param Smarty $smarty
     * @return NamedList
     */
    function prepareOptions(User $user, &$smarty) {
      $options = parent::prepareOptions($user, $smarty);
      
      if($this->getVisibility() <= VISIBILITY_PRIVATE && $user->canSeePrivate()) {
        AngieApplication::useHelper('object_visibility', SYSTEM_MODULE);
        
        $options->addBefore('visibility', array(
          'content' => smarty_function_object_visibility(array(
            'object' => $this, 
            'user' => $user, 
          ), $smarty)
        ), 'permalink');
      } // if
      
      return $options;
    } // prepareOptions
    
    /**
     * Return project to which this comment belongs to
     *
     * @return Project
     */
    function getProject() {
      if($this->getParent() instanceof ProjectObject) {
        return $this->getParent()->getProject();
      } // if
      
      throw new InvalidInstanceError('parent', $this->getParent(), 'Parent is expected to be an instance of ProjectObject class');
    } // getProject
    
    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------
    
    /**
     * Returns true if $user can update this comment
     *
     * @param User $user
     * @return boolean
     */
    function canEdit(User $user) {
      return $user->isProjectManager() || parent::canEdit($user);
    } // canEdit

    /**
     * Returns true if $user can delete this comment
     *
     * @param User $user
     * @return boolean
     */
    function canDelete(User $user) {
      return $user->isProjectManager() || parent::canDelete($user);
    } // canDelete
    
  }