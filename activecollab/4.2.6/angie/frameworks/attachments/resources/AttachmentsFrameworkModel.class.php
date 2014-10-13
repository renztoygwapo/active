<?php

  /**
   * Attachments framework model definition
   *
   * @package angie.frameworks.attachments
   * @subpackage resources
   */
  class AttachmentsFrameworkModel extends AngieFrameworkModel {
    
    /**
     * Construct attachments framework model definition
     *
     * @param AttachmentsFramework $parent
     */
    function __construct(AttachmentsFramework $parent) {
      parent::__construct($parent);
      
      $this->addModel(DB::createTable('attachments')->addColumns(array(
        DBIdColumn::create(), 
        DBTypeColumn::create('Attachment'), 
        DBParentColumn::create(), 
        DBStateColumn::create(),  
        DBNameColumn::create(150), 
        DBStringColumn::create('mime_type', 255, 'application/octet-stream'), 
        DBIntegerColumn::create('size', 10, '0')->setUnsigned(true), 
        DBStringColumn::create('location', 50), 
        DBStringColumn::create('md5', 32), 
        DBActionOnByColumn::create('created', true), 
        DBAdditionalPropertiesColumn::create(), 
      )))->setTypeFromField('type');
    } // __construct
    
  }