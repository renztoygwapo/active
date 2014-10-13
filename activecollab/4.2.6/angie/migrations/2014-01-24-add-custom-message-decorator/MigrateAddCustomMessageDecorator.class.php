<?php

  /**
   * Add 'decorator' column to outgoing messages table
   *
   * @package angie
   * @subpackage migrations
   */
  class MigrateAddCustomMessageDecorator extends AngieModelMigration {

    /**
     * Migrate up
     */
    function up() {
      $this->execute("ALTER TABLE " . TABLE_PREFIX . "outgoing_messages ADD decorator VARCHAR (255) DEFAULT 'OutgoingMessageDecorator'");
    } // up

  }