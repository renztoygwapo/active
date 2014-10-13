<?php

  /**
   * My projects home screen widget
   * 
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class MyProjectsHomescreenWidget extends ProjectsHomescreenWidget {
  
    /**
     * Return widget name
     * 
     * @return string
     */
    function getName() {
      return lang('My Projects');
    } // getName
    
    /**
     * Return widget description
     * 
     * @return string
     */
    function getDescription() {
      return lang('List all active projects that active user is assigned to');
    } // getDescription
    
    /**
     * Return projects that match filter configured by the user
     * 
     * @param IUser $user
     * @return Project[]
     */
    function getProjects(IUser $user) {
      return Projects::findActiveByUser($user);
    } // getProjects
    
    /**
     * Return message that is displayed when there are no projects to list
     * 
     * @return string
     */
    function getNoProjectsMessage() {
      return lang('There are no active projects that you are currently working on');
    } // getNoProjectsMessage

    /**
     * Returns true if favorite projects should be on top of the list
     *
     * @return bool
     */
    function showFavoritesOnTop() {
      return true;
    } // showFavoritesOnTop
    
  }