<?php

  /**
   * Has many association
   *
   * @package angie.library.database
   */
  abstract class DataAssociationHasMany extends DataAssociation {

    /**
     * Source instance
     *
     * @var DataObject
     */
    protected $source_instance;

    /**
     * Name of the connection field in target table
     *
     * @var string
     */
    protected $field_name;

    /**
     * Name of the filed that we will stack results by
     *
     * @var integer
     */
    protected $stack_by;

    /**
     * How target records should be ordered
     *
     * @var string
     */
    protected $order_by;

    /**
     * Construct has many relation
     *
     * @param DataObject $source_instance
     * @param mixed $params
     * @throws InvalidInstanceError
     */
    function __construct(DataObject $source_instance, $params = null) {
      $expected_source_instance_class = $this->getSourceInstanceClass();

      if($source_instance instanceof $expected_source_instance_class) {
        $this->source_instance = $source_instance;

        $this->field_name = isset($params['field_name']) ? $params['field_name'] : $this->getSourceModelName(true, true) . '_id';

        $this->stack_by = isset($params['stack_by']) ? $params['stack_by'] : null;
        $this->order_by = isset($params['order_by']) ? $params['order_by'] : null;

        if(empty($this->order_by)) {
          $this->order_by = empty($this->stack_by) ? 'id' : $this->stack_by;
        } // if
      } else {
        throw new InvalidInstanceError('source_instance', $source_instance, $expected_source_instance_class);
      } // if
    } // __construct

    /**
     * Returns true if there are related target objecets with source object
     *
     * @return bool
     */
    function has() {
      return $this->count() > 0;
    } // has

    /**
     * Return humber of connected source objects
     *
     * @return integer
     */
    function count() {
      return call_user_func(array($this->getTargetManagerClass(), 'count'), array($this->getFieldName() . ' = ?', $this->source_instance->getId()));
    } // count

    /**
     * Return number of items that are connected with the source object that meet the given conditions
     *
     * @param mixed $conditions
     * @return integer
     */
    protected function countWithConditions($conditions) {
      return call_user_func(array($this->getTargetManagerClass(), 'count'), '((' . DB::prepare($this->getFieldName() . ' = ?', $this->source_instance->getId()) . ') AND (' . DB::prepareConditions($conditions) . '))');
    } // countWithConditions

    /**
     * Return maching records from target table
     *
     * @return DataObject[]
     */
    function get() {
      return call_user_func(array($this->getTargetManagerClass(), 'find'), array(
        'conditions' => array($this->getFieldName() . ' = ?', $this->source_instance->getId()),
        'order_by' => $this->getOrderBy(),
      ));
    } // get

    /**
     * Return first item in the collection
     *
     * @return DataObject
     */
    function first() {
      return call_user_func(array($this->getTargetManagerClass(), 'find'), array(
        'conditions' => array($this->getFieldName() . ' = ?', $this->source_instance->getId()),
        'order_by' => $this->getOrderBy(),
        'one' => true,
      ));
    } // first

    /**
     * Return ID-s of related objects in target table
     *
     * @return array
     */
    function getIds() {
      return DB::executeFirstColumn('SELECT id FROM ' . $this->getTargetTableName() . ' WHERE ' . $this->getFieldName() . ' = ?', $this->source_instance->getId());
    } // getIds

    /**
     * Return next position value
     *
     * @return integer
     * @throws NotImplementedError
     */
    function getNextPosition() {
      if($this->stack_by) {
        return (integer) DB::executeFirstCell('SELECT MAX(' . $this->stack_by . ') FROM ' . $this->getTargetTableName() . ' WHERE ' . $this->getFieldName() . ' = ?', $this->source_instance->getId()) + 1;
      } else {
        throw new NotImplementedError(__METHOD__, 'This association is not stacked');
      } // if
    } // getNextPosition

    /**
     * Walk through each instance
     *
     * @param Closure $callback
     */
    function each(Closure $callback) {
      $all = $this->get();

      if($all) {
        foreach($all as $object) {
          $callback($object);
        } // foreach
      } // if
    } // each

    /**
     * Add objects to the relation
     *
     * @throws Exception
     * @throws InvalidInstanceError
     */
    function add() {
      $num_args = func_num_args();

      if($num_args > 1) {
        $objects = func_get_args();
      } elseif($num_args == 1) {
        $objects = func_get_arg(0);

        if(!is_array($objects)) {
          $objects = array($objects);
        } // if
      } else {
        throw new InvalidInstanceError('object', null, $this->getTargetInstanceClass(), 'This function expects one of more ' . $this->getTargetInstanceClass() . ' instances');
      } // if

      $source_instance_id = $this->source_instance->getId();
      $connection_field_name = $this->getFieldName();
      $expected_target_instance_class = $this->getTargetInstanceClass();

      $next_position = $this->stack_by ? $this->getNextPosition() : null;

      try {
        $source_object_signature = $this->getSourceModelName(true, true) . ' #' . $source_instance_id;
        $target_objects_short_name = $this->getTargetModelName(true, true);

        DB::beginWork("Connecting $source_object_signature with a list of $target_objects_short_name objects @ " . __CLASS__);

        foreach($objects as $object) {
          if($object instanceof $expected_target_instance_class) {
            $object->setFieldValue($connection_field_name, $source_instance_id);

            if($next_position) {
              $object->setFieldValue($this->stack_by, $next_position++);
            } // if

            $object->save();
          } else {
            throw new InvalidInstanceError('object', $object, $expected_target_instance_class);
          } // if
        } // foreach

        DB::commit("Connected $source_object_signature with a list of $target_objects_short_name objects @ " . __CLASS__);
      } catch(Exception $e) {
        DB::rollback("Failed to connect $source_object_signature with a list of $target_objects_short_name objects @ " . __CLASS__);
        throw $e;
      } // try
    } // add

    /**
     * Return name of the source model
     *
     * @param boolean $underscore
     * @param boolean $singular
     * @return string
     */
    abstract protected function getSourceModelName($underscore = false, $singular = false);

    /**
     * Return name of the target manager class
     *
     * @return string
     */
    protected function getSourceManagerClass() {
      return $this->getSourceModelName();
    } // getSourceManagerClass

    /**
     * Return name of the target instance class
     *
     * @return string
     */
    protected function getSourceInstanceClass() {
      return $this->getSourceModelName(false, true);
    } // getSourceInstanceClass

    /**
     * Return source table name
     *
     * @param boolean $with_prefix
     * @return string
     */
    protected function getSourceTableName($with_prefix = true) {
      return $with_prefix ? TABLE_PREFIX . $this->getSourceModelName(true) : $this->getSourceModelName(true);
    } // getSourceTableName

    /**
     * Return name of the target model
     *
     * @param boolean $underscore
     * @param boolean $singular
     * @return string
     */
    abstract protected function getTargetModelName($underscore = false, $singular = false);

    /**
     * Return name of the target manager class
     *
     * @return string
     */
    protected function getTargetManagerClass() {
      return $this->getTargetModelName();
    } // getTargetManagerClass

    /**
     * Return name of the target instance class
     *
     * @return string
     */
    protected function getTargetInstanceClass() {
      return $this->getTargetModelName(false, true);
    } // getTargetInstanceClass

    /**
     * Return target table name
     *
     * @param boolean $with_prefix
     * @return string
     */
    protected function getTargetTableName($with_prefix = true) {
      return $with_prefix ? TABLE_PREFIX . $this->getTargetModelName(true) : $this->getTargetModelName(true);
    } // getTargetTableName

    /**
     * Return connection field name
     *
     * @return string
     */
    function getFieldName() {
      return $this->field_name;
    } // getFieldName

    /**
     * Return order by
     *
     * @return string
     */
    function getOrderBy() {
      return $this->order_by;
    } // getOrderBy

  }