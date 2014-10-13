<?php

  /**
   * BasePublicTaskForm class
   *
   * @package ActiveCollab.modules.tasks
   * @subpackage models
   */
  abstract class BasePublicTaskForm extends ApplicationObject {
  
    /**
     * Name of the table where records are stored
     *
     * @var string
     */
    protected $table_name = 'public_task_forms';
    
    /**
     * All table fields
     *
     * @var array
     */
    protected $fields = array('id', 'project_id', 'slug', 'name', 'body', 'is_enabled', 'raw_additional_properties');
    
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
        return $underscore ? 'public_task_form' : 'PublicTaskForm';
      } else {
        return $underscore ? 'public_task_forms' : 'PublicTaskForms';
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
     * Return value of project_id field
     *
     * @return integer
     */
    function getProjectId() {
      return $this->getFieldValue('project_id');
    } // getProjectId
    
    /**
     * Set value of project_id field
     *
     * @param integer $value
     * @return integer
     */
    function setProjectId($value) {
      return $this->setFieldValue('project_id', $value);
    } // setProjectId

    /**
     * Return value of slug field
     *
     * @return string
     */
    function getSlug() {
      return $this->getFieldValue('slug');
    } // getSlug
    
    /**
     * Set value of slug field
     *
     * @param string $value
     * @return string
     */
    function setSlug($value) {
      return $this->setFieldValue('slug', $value);
    } // setSlug

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
     * Return value of body field
     *
     * @return string
     */
    function getBody() {
      return $this->getFieldValue('body');
    } // getBody
    
    /**
     * Set value of body field
     *
     * @param string $value
     * @return string
     */
    function setBody($value) {
      return $this->setFieldValue('body', $value);
    } // setBody

    /**
     * Return value of is_enabled field
     *
     * @return boolean
     */
    function getIsEnabled() {
      return $this->getFieldValue('is_enabled');
    } // getIsEnabled
    
    /**
     * Set value of is_enabled field
     *
     * @param boolean $value
     * @return boolean
     */
    function setIsEnabled($value) {
      return $this->setFieldValue('is_enabled', $value);
    } // setIsEnabled

    /**
     * Return value of raw_additional_properties field
     *
     * @return string
     */
    function getRawAdditionalProperties() {
      return $this->getFieldValue('raw_additional_properties');
    } // getRawAdditionalProperties
    
    /**
     * Set value of raw_additional_properties field
     *
     * @param string $value
     * @return string
     */
    function setRawAdditionalProperties($value) {
      return $this->setFieldValue('raw_additional_properties', $value);
    } // setRawAdditionalProperties

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
          case 'project_id':
            return parent::setFieldValue($real_name, (integer) $value);
          case 'slug':
            return parent::setFieldValue($real_name, (string) $value);
          case 'name':
            return parent::setFieldValue($real_name, (string) $value);
          case 'body':
            return parent::setFieldValue($real_name, (string) $value);
          case 'is_enabled':
            return parent::setFieldValue($real_name, (boolean) $value);
          case 'raw_additional_properties':
            return parent::setFieldValue($real_name, (string) $value);
        } // switch

        throw new InvalidParamError('name', $name, "Field $name (maps to $real_name) does not exist in this table");
      } // if
    } // setFieldValue
  
  }