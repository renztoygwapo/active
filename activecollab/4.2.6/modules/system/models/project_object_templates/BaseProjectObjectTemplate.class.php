<?php

  /**
   * BaseProjectObjectTemplate class
   *
   * @package ActiveCollab.modules.system
   * @subpackage models
   */
  abstract class BaseProjectObjectTemplate extends ApplicationObject {
  
    /**
     * Name of the table where records are stored
     *
     * @var string
     */
    protected $table_name = 'project_object_templates';
    
    /**
     * All table fields
     *
     * @var array
     */
    protected $fields = array('id', 'type', 'subtype', 'template_id', 'parent_id', 'value', 'position', 'file_size');
    
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
        return $underscore ? 'project_object_template' : 'ProjectObjectTemplate';
      } else {
        return $underscore ? 'project_object_templates' : 'ProjectObjectTemplates';
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
     * Return value of subtype field
     *
     * @return string
     */
    function getSubtype() {
      return $this->getFieldValue('subtype');
    } // getSubtype
    
    /**
     * Set value of subtype field
     *
     * @param string $value
     * @return string
     */
    function setSubtype($value) {
      return $this->setFieldValue('subtype', $value);
    } // setSubtype

    /**
     * Return value of template_id field
     *
     * @return integer
     */
    function getTemplateId() {
      return $this->getFieldValue('template_id');
    } // getTemplateId
    
    /**
     * Set value of template_id field
     *
     * @param integer $value
     * @return integer
     */
    function setTemplateId($value) {
      return $this->setFieldValue('template_id', $value);
    } // setTemplateId

    /**
     * Return value of parent_id field
     *
     * @return integer
     */
    function getParentId() {
      return $this->getFieldValue('parent_id');
    } // getParentId
    
    /**
     * Set value of parent_id field
     *
     * @param integer $value
     * @return integer
     */
    function setParentId($value) {
      return $this->setFieldValue('parent_id', $value);
    } // setParentId

    /**
     * Return value of value field
     *
     * @return string
     */
    function getValue() {
      return $this->getFieldValue('value');
    } // getValue
    
    /**
     * Set value of value field
     *
     * @param string $value
     * @return string
     */
    function setValue($value) {
      return $this->setFieldValue('value', $value);
    } // setValue

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
          case 'subtype':
            return parent::setFieldValue($real_name, (string) $value);
          case 'template_id':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'parent_id':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'value':
            return parent::setFieldValue($real_name, (string) $value);
          case 'position':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'file_size':
            return parent::setFieldValue($real_name, (integer) $value);
        } // switch

        throw new InvalidParamError('name', $name, "Field $name (maps to $real_name) does not exist in this table");
      } // if
    } // setFieldValue
  
  }