<?php

  /**
   * List projects based on given criteria
   * 
   * @package activeCollab.modules.system
   * @subpackage models
   */
  abstract class ProjectsHomescreenWidget extends HomescreenWidget {
    
    /**
     * Return widget name
     * 
     * @return string
     */
    function getName() {
      return lang('Projects');
    } // getName
    
    /**
     * Return name of the group that this widget belongs to
     * 
     * @return string
     */
    function getGroupName() {
      return lang('Projects');
    } // getGroupName
    
    /**
     * Return widget description
     * 
     * @return string
     */
    function getDescription() {
      return lang('List projects that match given criteria');
    } // getDescription
    
    /**
     * Return widget body
     * 
     * @param IUser $user
     * @param string $widget_id
     * @param string $column_wrapper_class
     * @return string
     */
    function renderBody(IUser $user, $widget_id, $column_wrapper_class = null) {
      $projects = $this->getProjects($user);
      
      if($projects) {
        if($this->showFavoritesColumn()) {
          AngieApplication::useHelper('favorite_object', FAVORITES_FRAMEWORK);
        } // if

        $favorite_projects = $other_projects = '';
        
        foreach($projects as $project) {
          $project_row = '<tr project_id="' . $project->getId() . '">';

          $project_row .= '<td class="icon left" width="16px"><img src="' . clean($project->avatar()->getUrl(IProjectAvatarImplementation::SIZE_SMALL)) . '"></td>';
          $project_row .= '<td class="name">' . object_link($project, null, array('class' => 'quick_view_item')) . '</td>';

          if (($label = $project->label()->get()) instanceof Label) {
            $rendered_label = $label->render(true);
          } else {
            $rendered_label = '';
          } // if

          $project_row .= '<td class="project_options">' . $rendered_label . ProjectProgress::renderRoundProjectProgress($project) . '</td>';
          if($this->showFavoritesColumn()) {
            $project_row .= '<td class="favorite right" width="16px">' . smarty_function_favorite_object(array(
              'object' => $project, 
              'user' => $user, 
            ), SmartyForAngie::getInstance())  . '</td>';
          } // if

          $project_row .= '</tr>';

          if($this->showFavoritesOnTop() && Favorites::isFavorite($project, $user)) {
            $favorite_projects .= $project_row;
          } else {
            $other_projects .= $project_row;
          } // if
        } // foreach

        return '<table class="common projects_for_projects_homescreen_widget" cellspacing="0"><tbody>' . $favorite_projects . $other_projects . '</tbody></table>';
        
        //return "$result</tbody></table>";
      } else {
        return '<p>' . clean($this->getNoProjectsMessage()) . '</p>';
      } // if
    } // renderBody

    // ---------------------------------------------------
    //  Customer renderer
    // ---------------------------------------------------
    
    /**
     * Return projects that match filter configured by the user
     * 
     * @param IUser $user
     * @return Project[]
     */
    abstract function getProjects(IUser $user);
    
    /**
     * Show favorite projects column
     * 
     * @return boolean
     */
    function showFavoritesColumn() {
      return true;
    } // showFavoritesColumn

    /**
     * Returns true if favorite projects should be on top of the list
     *
     * @return bool
     */
    function showFavoritesOnTop() {
      return false;
    } // showFavoritesOnTop
    
    /**
     * Return message that is displayed when there are no projects to list
     * 
     * @return string
     */
    function getNoProjectsMessage() {
      return lang('There are no projects that match the given criteria');
    } // getNoProjectsMessage
    
    // ---------------------------------------------------
    //  Options
    // ---------------------------------------------------
    
    /**
     * Returns true if this widget has additional options
     * 
     * @return boolean
     */
    function hasOptions() {
      return true;
    } // hasOptions
    
    /**
     * Render widget options form section
     * 
     * @param IUser $user
     * @return string
     */
    function renderOptions(IUser $user) {
      $view = SmartyForAngie::getInstance()->createTemplate($this->getOptionsViewPath());
      
      $view->assign(array(
        'widget' => $this, 
        'user' => $user, 
        'widget_data' => $this->getOptionsViewWidgetData(), 
      ));
      
      return $view->fetch();
    } // renderOptions
    
    /**
     * Return options view path
     * 
     * @return string
     */
    protected function getOptionsViewPath() {
      return get_view_path('projects_options', 'homescreen_widgets', SYSTEM_MODULE, AngieApplication::INTERFACE_DEFAULT);
    } // getOptionsViewPath
    
    /**
     * Return options view widget data
     * 
     * @return array
     */
    protected function getOptionsViewWidgetData() {
      return array(
        'caption' => $this->getCaption(),  
      );
    } // getOptionsViewWidgetData

    /**
     * Projects widget has caption
     *
     * @return bool
     */
    function hasCaption() {
      return true;
    } // hasCaption
    
  }