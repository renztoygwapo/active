<?php

  /**
   * Class MigrateTextDocumentAttachments
   *
   * Fix type for attachments added to text documents
   *
   * @package activecollab.modules.files
   */
  class MigrateTextDocumentAttachments extends AngieModelMigration {

    /**
     * Migrate up
     */
    function up() {
      $this->execute("UPDATE " . TABLE_PREFIX . "attachments SET type = 'Attachment' WHERE type = 'ProjectObjectAttachment' AND parent_type = 'TextDocument'");
    } // up
    
  }