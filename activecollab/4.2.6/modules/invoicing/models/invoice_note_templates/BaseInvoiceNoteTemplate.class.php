<?php

  /**
   * BaseInvoiceNoteTemplate class
   *
   * @package ActiveCollab.modules.invoicing
   * @subpackage models
   */
  abstract class BaseInvoiceNoteTemplate extends ApplicationObject {
  
    /**
     * Name of the table where records are stored
     *
     * @var string
     */
    protected $table_name = 'invoice_note_templates';
    
    /**
     * All table fields
     *
     * @var array
     */
    protected $fields = array('id', 'name', 'content', 'position', 'is_default');
    
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
        return $underscore ? 'invoice_note_template' : 'InvoiceNoteTemplate';
      } else {
        return $underscore ? 'invoice_note_templates' : 'InvoiceNoteTemplates';
      } // if
    } // getModelName

    /**
     * Name of AI field (if any)
     *
     * @var string
     */
    protected $auto_increment = 'id';
    

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
     * Return value of name field
     *
     * @return string
     */
    function getName() {
      return $this->getFieldValue('name');
    } // getName
    
    /**
     * Set value of name field
     *
     * @param string $value
     * @return string
     */
    function setName($value) {
      return $this->setFieldValue('name', $value);
    } // setName

    /**
     * Return value of content field
     *
     * @return string
     */
    function getContent() {
      return $this->getFieldValue('content');
    } // getContent
    
    /**
     * Set value of content field
     *
     * @param string $value
     * @return string
     */
    function setContent($value) {
      return $this->setFieldValue('content', $value);
    } // setContent

    /**
     * Return value of position field
     *
     * @return integer
     */
    function getPosition() {
      return $this->getFieldValue('position');
    } // getPosition
    
    /**
     * Set value of position field
     *
     * @param integer $value
     * @return integer
     */
    function setPosition($value) {
      return $this->setFieldValue('position', $value);
    } // setPosition

    /**
     * Return value of is_default field
     *
     * @return boolean
     */
    function getIsDefault() {
      return $this->getFieldValue('is_default');
    } // getIsDefault
    
    /**
     * Set value of is_default field
     *
     * @param boolean $value
     * @return boolean
     */
    function setIsDefault($value) {
      return $this->setFieldValue('is_default', $value);
    } // setIsDefault

    /**
     * Set value of specific field
     *
     * @param string $name
     * @param mixed $value
     * @return mixed
     * @throws InvalidParamError
     */
    function setFieldValue($name, $value) {
      switch($real_name = $this->realFieldName($name)) {
        case 'id':
          return parent::setFieldValue($real_name, (integer) $value);
        case 'name':
          return parent::setFieldValue($real_name, (string) $value);
        case 'content':
          return parent::setFieldValue($real_name, (string) $value);
        case 'position':
          return parent::setFieldValue($real_name, (integer) $value);
        case 'is_default':
          return parent::setFieldValue($real_name, (boolean) $value);
      } // switch
      
      throw new InvalidParamError('name', $name, "Field $name (maps to $real_name) does not exist in this table");
    } // switch
  
  }