<?php

  /**
   * Introduce alternative user addresses
   *
   * @package angie.migrations
   */
  class MigrateIntroduceUserAddresses extends AngieModelMigration {

    /**
     * Migrate up
     */
    function up() {
      $this->createTable('user_addresses', array(
        DBIntegerColumn::create('user_id', 10, '0')->setUnsigned(true),
        DBStringColumn::create('email', 150, ''),
      ), array(
        DBIndexPrimary::create(array('user_id', 'email')),
      ));
    } // up

    /**
     * Migrate down
     */
    function down() {
      $this->dropTable('user_addresses');
    } // down

  }