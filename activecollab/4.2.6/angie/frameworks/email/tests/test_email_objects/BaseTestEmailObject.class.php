<?php

  /**
   * BaseTestEmailObject class
   *
   * @package activeCollab.modules.system
   * @subpackage models
   */
  abstract class BaseTestEmailObject extends ApplicationObject {
  
    /**
     * Name of the table where records are stored
     *
     * @var string
     */
    protected $table_name = 'test_email_objects';
    
    /**
     * All table fields
     *
     * @var array
     */
    protected $fields = array('id', 'name', 'body');
    
    /**
     * Primary key fields
     *
     * @var array
     */
    protected $primary_key = array('id');
    
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
     * Set value of specific field
     *
     * @param string $name
     * @param mided $value
     * @return mixed
     */
    function setFieldValue($name, $value) {
      switch($real_name = $this->realFieldName($name)) {
        case 'id':
          return parent::setFieldValue($real_name, intval($value));
        case 'name':
          return parent::setFieldValue($real_name, strval($value));
        case 'body':
          return parent::setFieldValue($real_name, strval($value));
      } // switch
      
      throw new InvalidParamError('name', $name, "Field $name (maps to $real_name) does not exist in this table");
    } // switch
  
  }

?>