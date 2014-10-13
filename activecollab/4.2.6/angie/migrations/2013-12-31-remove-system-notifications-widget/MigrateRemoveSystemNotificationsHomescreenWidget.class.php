<?php

  /**
   * Remove all instances where system notifications home screen widget was used
   *
   * @package activeCollab.modules.system
   * @subpackage migrations
   */
  class MigrateRemoveSystemNotificationsHomescreenWidget extends AngieModelMigration {

    /**
     * Migrate up
     */
    function up() {
      $this->execute('DELETE FROM ' . TABLE_PREFIX . 'homescreen_widgets WHERE type = ?', 'SystemNotificationsHomescreenWidget');
    } // up

  }