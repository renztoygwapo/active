<?php

  /**
   * Introduce mention tag
   *
   * @package angie.migrations
   */
  class MigrateIntroduceMentionTag extends AngieModelMigration {

    /**
     * Migrate up
     */
    function up() {
      $config_option_name = 'whitelisted_tags';
      $whitelisted_tags = $this->getConfigOptionValue($config_option_name);
      $whitelisted_tags['visual_editor']['span'][] = 'object-id';
      $this->setConfigOptionValue($config_option_name, $whitelisted_tags);
    } // up

  }