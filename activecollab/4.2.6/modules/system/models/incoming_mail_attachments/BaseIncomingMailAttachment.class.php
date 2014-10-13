<?php

  /**
   * BaseIncomingMailAttachment class
   *
   * @package ActiveCollab.modules.system
   * @subpackage models
   */
  abstract class BaseIncomingMailAttachment extends ApplicationObject {
  
    /**
     * Name of the table where records are stored
     *
     * @var string
     */
    protected $table_name = 'incoming_mail_attachments';
    
    /**
     * All table fields
     *
     * @var array
     */
    protected $fields = array('id', 'type', 'mail_id', 'temporary_filename', 'original_filename', 'content_type', 'file_size');
    
    /**
     * Primary key fields
     *
     * @var array
     */
    protected $primary_key = array('id');

    /**
     * Return name of this model
     *
     * @param boolean $underscore
     * @param boolean $singular
     * @return string
     */
    function getModelName($underscore = false, $singular = false) {
      if($singular) {
        return $underscore ? 'incoming_mail_attachment' : 'IncomingMailAttachment';
      } else {
        return $underscore ? 'incoming_mail_attachments' : 'IncomingMailAttachments';
      } // if
    } // getModelName

    /**
     * Name of AI field (if any)
     *
     * @var string
     */
    protected $auto_increment = 'id';
    // ---------------------------------------------------
    //  Fields
    // ---------------------------------------------------

    /**
     * Return value of id field
     *
     * @return integer
     */
    function getId() {
      return $this->getFieldValue('id');
    } // getId
    
    /**
     * Set value of id field
     *
     * @param integer $value
     * @return integer
     */
    function setId($value) {
      return $this->setFieldValue('id', $value);
    } // setId

    /**
     * Return value of type field
     *
     * @return string
     */
    function getType() {
      return $this->getFieldValue('type');
    } // getType
    
    /**
     * Set value of type field
     *
     * @param string $value
     * @return string
     */
    function setType($value) {
      return $this->setFieldValue('type', $value);
    } // setType

    /**
     * Return value of mail_id field
     *
     * @return integer
     */
    function getMailId() {
      return $this->getFieldValue('mail_id');
    } // getMailId
    
    /**
     * Set value of mail_id field
     *
     * @param integer $value
     * @return integer
     */
    function setMailId($value) {
      return $this->setFieldValue('mail_id', $value);
    } // setMailId

    /**
     * Return value of temporary_filename field
     *
     * @return string
     */
    function getTemporaryFilename() {
      return $this->getFieldValue('temporary_filename');
    } // getTemporaryFilename
    
    /**
     * Set value of temporary_filename field
     *
     * @param string $value
     * @return string
     */
    function setTemporaryFilename($value) {
      return $this->setFieldValue('temporary_filename', $value);
    } // setTemporaryFilename

    /**
     * Return value of original_filename field
     *
     * @return string
     */
    function getOriginalFilename() {
      return $this->getFieldValue('original_filename');
    } // getOriginalFilename
    
    /**
     * Set value of original_filename field
     *
     * @param string $value
     * @return string
     */
    function setOriginalFilename($value) {
      return $this->setFieldValue('original_filename', $value);
    } // setOriginalFilename

    /**
     * Return value of content_type field
     *
     * @return string
     */
    function getContentType() {
      return $this->getFieldValue('content_type');
    } // getContentType
    
    /**
     * Set value of content_type field
     *
     * @param string $value
     * @return string
     */
    function setContentType($value) {
      return $this->setFieldValue('content_type', $value);
    } // setContentType

    /**
     * Return value of file_size field
     *
     * @return integer
     */
    function getFileSize() {
      return $this->getFieldValue('file_size');
    } // getFileSize
    
    /**
     * Set value of file_size field
     *
     * @param integer $value
     * @return integer
     */
    function setFileSize($value) {
      return $this->setFieldValue('file_size', $value);
    } // setFileSize

    /**
     * Set value of specific field
     *
     * @param string $name
     * @param mixed $value
     * @return mixed
     * @throws InvalidParamError
     */
    function setFieldValue($name, $value) {
      $real_name = $this->realFieldName($name);

      if($value === null) {
        return parent::setFieldValue($real_name, null);
      } else {
        switch($real_name) {
          case 'id':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'type':
            return parent::setFieldValue($real_name, (string) $value);
          case 'mail_id':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'temporary_filename':
            return parent::setFieldValue($real_name, (string) $value);
          case 'original_filename':
            return parent::setFieldValue($real_name, (string) $value);
          case 'content_type':
            return parent::setFieldValue($real_name, (string) $value);
          case 'file_size':
            return parent::setFieldValue($real_name, (integer) $value);
        } // switch

        throw new InvalidParamError('name', $name, "Field $name (maps to $real_name) does not exist in this table");
      } // if
    } // setFieldValue
  
  }