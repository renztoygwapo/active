<?php

  /**
   * Favorite projects home screen widget
   * 
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class FavoriteProjectsHomescreenWidget extends ProjectsHomescreenWidget {
    
    /**
     * Return widget name
     * 
     * @return string
     */
    function getName() {
      return lang('Favorite Projects');
    } // getName
    
    /**
     * Return widget description
     * 
     * @return string
     */
    function getDescription() {
      return lang('List projects that active user marked as favorite');
    } // getDescription
    
    /**
     * Return projects that match filter configured by the user
     * 
     * @param IUser $user
     * @return array
     */
    function getProjects(IUser $user) {
      $favorite_project_ids = Favorites::findIdsByUserAndType($user, 'Project');
      
      if($favorite_project_ids) {
        return Projects::findByIds($favorite_project_ids);
      } else {
        return null;
      } // if
    } // getProjects
    
    /**
     * Return message that is displayed when there are no projects to list
     * 
     * @return string
     */
    function getNoProjectsMessage() {
      return lang('You did not mark any projects as favorite');
    } // getNoProjectsMessage
    
  }