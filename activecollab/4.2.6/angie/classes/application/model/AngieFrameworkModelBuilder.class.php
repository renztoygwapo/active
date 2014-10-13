<?php

  /**
   * Model generator class
   *
   * @package angie.library.application
   * @subpackage application
   */
  class AngieFrameworkModelBuilder {

    // Supported association types
    const BELONGS_TO = 'belongs_to';
    const HAS_ONE = 'has_one';
    const HAS_MANY = 'has_many';
    const HAS_AND_BELONGS_TO_MANY = 'has_and_belongs_to_many';
    
    /**
     * Model that this builder instance belongs to
     *
     * @var AngieFrameworkModel
     */
    protected $model;
    
    /**
     * Name of the table that this model builder is added to
     *
     * @var DBTable
     */
    protected $table;
    
    /**
     * Name of the class that base object extends
     *
     * @var string
     */
    protected $base_object_extends = 'ApplicationObject';
    
    /**
     * Name of the class that base manager extends
     *
     * @var string
     */
    protected $base_manager_extends = 'DataManager';

    /**
     * Obect is abstract
     *
     * @var bool
     */
    protected $object_is_abstract = false;

    /**
     * Manager is abstract
     *
     * @var bool
     */
    protected $manager_is_abstract = false;
    
    /**
     * Construct instances based on class name stored in a field
     *
     * @var string
     */
    protected $type_from_field;

    /**
     * Generate permissions in model class
     *
     * @var bool
     */
    protected $generate_permissions = false;

    /**
     * Generate view edit and delete URL-s
     *
     * @var bool
     */
    protected $generate_urls = false;
    
    /**
     * Value used for ordering records
     *
     * @var string
     */
    protected $order_by;

    /**
     * Name of the module where this model is injected to (works only for framework models)
     *
     * @var string
     */
    protected $inject_into = 'system';

    /**
     * Model associations
     *
     * @var array
     */
    protected $associations = array();
    
    /**
     * Construct new model builder instance
     *
     * @param AngieFrameworkModel $model
     * @param DBTable $table
     * @throws InvalidInstanceError
     */
    function __construct(AngieFrameworkModel $model, DBTable $table) {
      $this->model = $model;

      if($table instanceof DBTable) {
        $this->table = $table;
      } else {
        throw new InvalidInstanceError('table', $table, 'DBTable');
      } // if
    } // __construct

    // ---------------------------------------------------
    //  Associations
    // ---------------------------------------------------

    /**
     * Define belongs to association
     *
     * @param $target_model_name
     * @param string $as
     * @param array $params
     * @return AngieFrameworkModelBuilder
     * @throws Error
     */
    function &belongsTo($target_model_name, $as = null, $params = null) {
      return $this->addAssociation(AngieFrameworkModelBuilder::BELONGS_TO, $target_model_name, $as, $params);
    } // belongsTo

    /**
     * Define has one association
     *
     * @param $target_model_name
     * @param string $as
     * @param array $params
     * @return AngieFrameworkModelBuilder
     * @throws Error
     */
    function &hasOne($target_model_name, $as = null, $params = null) {
      return $this->addAssociation(AngieFrameworkModelBuilder::HAS_ONE, $target_model_name, $as, $params);
    } // belongsTo

    /**
     * Has many association
     *
     * Parameters:
     *
     * - stack_by - Stack target objects by this field (position for example)
     * - order_by - How to order target objects
     *
     * @param string $target_model_name
     * @param string $as
     * @param array $params
     * @return AngieFrameworkModelBuilder
     * @throws Error
     */
    function &hasMany($target_model_name, $as = null, $params = null) {
      return $this->addAssociation(AngieFrameworkModelBuilder::HAS_MANY, $target_model_name, $as, $params);
    } // hasMany

    /**
     * Has and belongs to many
     *
     * @param $target_model_name
     * @param string $as
     * @param array $params
     * @return AngieFrameworkModelBuilder
     * @throws Error
     */
    function &hasAndBelongsToMany($target_model_name, $as = null, $params = null) {
      return $this->addAssociation(AngieFrameworkModelBuilder::HAS_AND_BELONGS_TO_MANY, $target_model_name, $as, $params);
    } // hasAndBelongsToMany

    /**
     * Add association to associations array
     *
     * @param string $type
     * @param string $target_model_name
     * @param string $as
     * @param array $params
     * @return AngieFrameworkModelBuilder
     * @throws Error
     */
    private function &addAssociation($type, $target_model_name, $as = null, $params = null) {
      if(empty($as)) {
        $as = $target_model_name;
      } // if

      if(array_key_exists($as, $this->associations)) {
        throw new Error("Association '$as' already exists");
      } // if

      $this->associations[$as] = array(
        'type' => $type,
        'target_model_name' => $target_model_name,
      );

      if($params && is_foreachable($params)) {
        $this->associations[$as] = array_merge($this->associations[$as], $params);
      } // if

      return $this;
    } // setAssociation

    // ---------------------------------------------------
    //  Generation
    // ---------------------------------------------------

    /**
     * Return parent mode instance
     *
     * @return AngieFrameworkModel
     */
    function getModel() {
      return $this->model;
    } // getModel

    /**
     * Return fields
     *
     * @return NamedList
     */
    function getFields() {
      return $this->table->getColumns();
    } // getFields

    /**
     * Return destination module name
     *
     * @return string
     */
    function getDestinationModuleName() {
      return $this->model->getParent() instanceof AngieModule ? $this->model->getParent()->getName() : $this->getInjectInto();
    } // getDestinationModuleName

    /**
     * Return destination path of the module
     *
     * @return string
     */
    function getDestinationModulePath() {
      return $this->model->getParent() instanceof AngieModule ? $this->model->getParent()->getPath() : APPLICATION_PATH . '/modules/' . $this->getInjectInto();
    } // getDestinationModulePath
    
    // ---------------------------------------------------
    //  Getters and setters
    // ---------------------------------------------------
    
    /**
     * Return base_object_extends
     *
     * @return string
     */
    function getBaseObjectExtends() {
    	return $this->base_object_extends;
    } // getBaseObjectExtends
    
    /**
     * Set base_object_extends
     *
     * @param string $value
     * @return AngieFrameworkModelBuilder
     */
    function &setBaseObjectExtends($value) {
      $this->base_object_extends = $value;
      
      return $this;
    } // setBaseObjectExtends
    
    /**
     * Return base_manager_extends
     *
     * @return string
     */
    function getBaseManagerExtends() {
    	return $this->base_manager_extends;
    } // getBaseManagerExtends
    
    /**
     * Set base_manager_extends
     *
     * @param string $value
     * @return AngieFrameworkModelBuilder
     */
    function &setBaseManagerExtends($value) {
      $this->base_manager_extends = $value;
      
      return $this;
    } // setBaseManagerExtends

    /**
     * Returns true if object should be abstract class
     *
     * @return bool
     */
    function getObjectIsAbstract() {
      return $this->object_is_abstract;
    } // getObjectIsAbstract

    /**
     * Set whether object should be abstract class
     *
     * @param boolean $value
     * @return AngieFrameworkModelBuilder
     */
    function &setObjectIsAbstract($value) {
      $this->object_is_abstract = (boolean) $value;

      return $this;
    } // setObjectIsAbstract

    /**
     * Returns true if manager should be abstract class
     *
     * @return bool
     */
    function getManagerIsAbstract() {
      return $this->manager_is_abstract;
    } // getManagerIsAbstract

    /**
     * Set whether manager should be abstract class
     *
     * @param boolean $value
     * @return AngieFrameworkModelBuilder
     */
    function &setManagerIsAbstract($value) {
      $this->manager_is_abstract = (boolean) $value;

      return $this;
    } // setManagerIsAbstract
    
    /**
     * Return type_from_field
     *
     * @return string
     */
    function getTypeFromField() {
    	return $this->type_from_field;
    } // getTypeFromField
    
    /**
     * Set type_from_field
     *
     * @param string $value
     * @return AngieFrameworkModelBuilder
     */
    function &setTypeFromField($value) {
      $this->type_from_field = $value;
      
      return $this;
    } // setTypeFromField

    /**
     * Return generate permissions flag
     *
     * @return bool
     */
    function getGeneratePermissions() {
      return $this->generate_permissions;
    } // getGeneratePermissions

    /**
     * Set generate permissions
     *
     * @param boolean $value
     * @return AngieFrameworkModelBuilder
     */
    function &setGeneratePermissions($value) {
      $this->generate_permissions = (boolean) $value;

      return $this;
    } // setGeneratePermissions

    /**
     * Return generate URL-s flag
     *
     * @return bool
     */
    function getGenerateUrls() {
      return $this->generate_urls;
    } // getGenerateUrls

    /**
     * Set generate URL-s flag
     *
     * @param boolean $value
     * @return AngieFrameworkModelBuilder
     */
    function &setGenerateUrls($value) {
      $this->generate_urls = (boolean) $value;

      return $this;
    } // setGenerateUrls
    
    /**
     * Return order_by
     *
     * @return string
     */
    function getOrderBy() {
    	return $this->order_by;
    } // getOrderBy
    
    /**
     * Set order_by
     *
     * @param string $value
     * @return AngieFrameworkModelBuilder
     */
    function &setOrderBy($value) {
      $this->order_by = $value;
      
      return $this;
    } // setOrderBy

    /**
     * Return inject into module name
     *
     * @return string
     */
    function getInjectInto() {
      return $this->inject_into;
    } // getInjectInto

    /**
     * Set inject into module name
     *
     * @param $value
     * @return AngieFrameworkModelBuilder
     */
    function &setInjectInto($value) {
      $this->inject_into = $value;

      return $this;
    } // setInjectInto

    /**
     * Return list of model associations
     *
     * @return array
     */
    function getAssociations() {
      return $this->associations;
    } // getAssociations
    
  }