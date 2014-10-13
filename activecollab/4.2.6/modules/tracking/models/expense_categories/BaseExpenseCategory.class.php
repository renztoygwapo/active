<?php

  /**
   * BaseExpenseCategory class
   *
   * @package ActiveCollab.modules.tracking
   * @subpackage models
   */
  abstract class BaseExpenseCategory extends ApplicationObject {
  
    /**
     * Name of the table where records are stored
     *
     * @var string
     */
    protected $table_name = 'expense_categories';
    
    /**
     * All table fields
     *
     * @var array
     */
    protected $fields = array('id', 'name', 'is_default');
    
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
        return $underscore ? 'expense_category' : 'ExpenseCategory';
      } else {
        return $underscore ? 'expense_categories' : 'ExpenseCategories';
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
        case 'is_default':
          return parent::setFieldValue($real_name, (boolean) $value);
      } // switch
      
      throw new InvalidParamError('name', $name, "Field $name (maps to $real_name) does not exist in this table");
    } // switch
  
  }