<?php

  /**
   * Fix welcome message typo
   *
   * @package activeCollab.modules.system
   * @subpackage migrations
   */
  class MigrateFixWelcomeMessage extends AngieModelMigration {

    /**
     * Migrate up
     */
    function up() {
      $val = $this->getConfigOptionValue('identity_client_welcome_message');

      if($val == "Welcome to our project collaboration environment! You will find all your projects when you click on 'Projects' icon in the main navigation. To get back to this page, you can always click on 'Home Screen' menu item.") {
        $this->setConfigOptionValue('identity_client_welcome_message', "Welcome to our project collaboration environment! You will find all your projects by clicking the 'Projects' icon in the main menu. To return to this page, click on the 'Home Screen' menu item.");
      } // if
    } // up

  }