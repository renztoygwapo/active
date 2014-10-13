<?php

  /**
   * Migration does not exist error
   *
   * @package angie.library.errors
   */
  class MigrationDnxError extends Error {

    /**
     * Construct error object
     *
     * @param string $migration_name
     * @param string $changeset_name
     * @param string $message
     */
    function __construct($migration_name, $changeset_name, $message = null) {
      if(empty($message)) {
        $message = "Migration '$migration_name' not found in '$changeset_name' change-set";
      } // if

      parent::__construct($message, array(
        'migration_name' => $migration_name,
        'changeset_name' => $changeset_name,
      ));
    } // __construct

  }