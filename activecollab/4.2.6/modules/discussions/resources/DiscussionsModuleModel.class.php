<?php

  // Include application specific model base
  require_once APPLICATION_PATH . '/resources/ActiveCollabModuleModel.class.php';

  /**
   * Discussions module model
   * 
   * @package activeCollab.modules.discussions
   * @subpackage models
   */
  class DiscussionsModuleModel extends ActiveCollabModuleModel {
  
    /**
     * Load initial framework data
     *
     * @param string $environment
     */
    function loadInitialData($environment = null) {
      $this->addConfigOption('discussion_categories', array('General'));

      $project_tabs = $this->getConfigOptionValue('project_tabs');

      if(!in_array('discussions', $project_tabs)) {
        $project_tabs[] = 'discussions';
        $this->setConfigOptionValue('project_tabs', $project_tabs);
      } // if
      
      parent::loadInitialData($environment);
    } // loadInitialData
    
  }