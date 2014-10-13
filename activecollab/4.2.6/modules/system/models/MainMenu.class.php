<?php

  /**
   * Application level main menu implementation
   * 
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class MainMenu extends FwMainMenu {

    /**
     * Load status bar items
     * 
     * @param IUser $user
     * @param boolean $detailed
     * @param array|boolean $item_restrictions
     */
    function load(IUser $user, $detailed = false, $item_restrictions = false) {
      if($this->isLoaded()) {
        return;
      } // if
      
      parent::load($user, $detailed, $item_restrictions);
      
      $this->remove('users');

      // people
      if ($this->isAllowed('people')) {
        $this->addAfter('people', lang('People'), Router::assemble('people'), AngieApplication::getImageUrl('main-menu/people.png', SYSTEM_MODULE), null, 'homepage');
      } // if

      // projects
      if ($this->isAllowed('projects')) {
        $user_projects = Projects::findForQuickAdd($user); // same method is used by "detailed" method below

        $projects_item = array(
          'hotkey'  => 'p'
        );

        if (count($user_projects) < MAIN_MENU_PROJECTS_LIMIT) {
          $projects_section_tabs = new WireframeTabs();
          $projects_section_tabs->add('projects', lang('Projects'), Router::assemble('projects'));
          EventsManager::trigger('on_projects_tabs', array(&$projects_section_tabs, &$user));

          $projects_item['popup'] = array(
            'header'          => array('title'   => lang('Projects')),
            'tabs'            => $projects_section_tabs,
            'handler'         => 'projects_menu',
            'initial_refresh' => true,
            'additional'      => array(
              'manage_projects_url'   => Router::assemble('projects'),
            )
          );

          if (Projects::canAdd($user)) {
            $projects_item['popup']['additional']['add_project_url'] = Router::assemble('projects_add');
          } // if

          if ($this->isDetailed()) {
            // get the currently set grouping
            list ($projects, $projects_tabs_map, $project_groups) = Projects::findForMainMenu($user);
            $projects_item['popup']['additional']['projects'] = $projects;
            $projects_item['popup']['additional']['projects_tabs_map'] = $projects_tabs_map;
            $projects_item['popup']['additional']['project_groups'] = $project_groups;
          } // if
        } // if

        $this->addAfter('projects', lang('Projects'), Router::assemble('projects'), AngieApplication::getImageUrl('main-menu/projects.png', SYSTEM_MODULE), $projects_item, 'people');
      } // if
    } // load
    
  }