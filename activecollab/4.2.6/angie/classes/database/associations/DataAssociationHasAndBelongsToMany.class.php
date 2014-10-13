<?php

  /**
   * Has and belongs to many association
   *
   * @package angie.library.database
   */
  abstract class DataAssociationHasAndBelongsToMany extends DataAssociation {

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
      return DB::executeFirstCell('SELECT COUNT(*) FROM ' . $this->getConnectionTableName() . ' WHERE ' . $this->getSourceKeyName() . ' = ?', $this->source_instance->getId());
    } // count

    /**
     * Return number of items that are connected with the source object that meet the given conditions
     *
     * @param mixed $conditions
     * @return integer
     */
    protected function countWithConditions($conditions) {
      return DB::executeFirstCell('SELECT COUNT(*) FROM ' . $this->getConnectionTableName() . ' WHERE ' . $this->getSourceKeyName() . ' = ? AND (' . DB::prepareConditions($conditions) . ')', $this->source_instance->getId());
    } // countWithConditions

    /**
     * Return maching records from target table
     *
     * @return DataObject[]
     */
    function get() {
      $source_key = $this->getSourceKeyName();
      $target_key = $this->getTargetKeyName();

      $t = $this->getTargetTableName();
      $c = $this->getConnectionTableName();

      return call_user_func(array($this->getTargetManagerClass(), 'findBySQL'), "SELECT target_table.* FROM $t AS target_table, $c AS connection_table WHERE connection_table.{$source_key} = ? AND target_table.id = connection_table.{$target_key}", $this->source_instance->getId());
    } // get

    /**
     * Return ID-s of related objects in target table
     *
     * @return array
     */
    function getIds() {
      return DB::executeFirstColumn('SELECT ' . $this->getTargetKeyName() . ' FROM ' . $this->getTargetTableName() . ' WHERE ' . $this->getSourceKeyName() . ' = ?', $this->source_instance->getId());
    } // getIds

    /**
     * Return next position value
     *
     * @return integer
     * @throws NotImplementedError
     */
    function getNextPosition() {
      if($this->stack_by) {
        return (integer) DB::executeFirstCell('SELECT MAX(' . $this->stack_by . ') FROM ' . $this->getConnectionTableName() . ' WHERE ' . $this->getSourceKeyName() . ' = ?', $this->source_instance->getId()) + 1;
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
      $expected_target_instance_class = $this->getTargetInstanceClass();
      $source_key_name = $this->getSourceKeyName();
      $target_key_name = $this->getTargetKeyName();

      $next_position = $this->stack_by ? $this->getNextPosition() : null;

      try {
        $source_object_signature = $this->getSourceModelName(true, true) . ' #' . $source_instance_id;
        $target_objects_short_name = $this->getTargetModelName(true, true);

        DB::beginWork("Connecting $source_object_signature with a list of $target_objects_short_name objects @ " . __CLASS__);

        $fields = array($source_key_name, $target_key_name);

        if($this->stack_by) {
          $fields[] = $this->stack_by;
        } // if

        $batch = new DBBatchInsert($this->getConnectionTableName(), $fields, 50, DBBatchInsert::REPLACE_RECORDS);

        foreach($objects as $object) {
          if($object instanceof $expected_target_instance_class) {
            $to_insert = array($source_instance_id, $object->getId());

            if($this->stack_by) {
              $to_insert[] = $next_position++;
            } // if

            $batch->insertArray($to_insert);
          } else {
            throw new InvalidInstanceError('object', $object, $expected_target_instance_class);
          } // if
        } // foreach

        $batch->done();

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
     * Return soruce key name
     *
     * @return string
     */
    protected function getSourceKeyName() {
      return $this->getSourceModelName(true, true) . '_id';
    } // getSourceKeyName

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
     * Return soruce key name
     *
     * @return string
     */
    protected function getTargetKeyName() {
      return $this->getTargetModelName(true, true) . '_id';
    } // getTargetKeyName

    /**
     * Return connection table name
     *
     * @param boolean $with_prefix
     * @return string
     */
    protected function getConnectionTableName($with_prefix = true) {
      return ($with_prefix ? TABLE_PREFIX : '') . $this->getSourceModelName(true) . '_' . $this->getTargetModelName(true);
    } // getConnectionTableName

    /**
     * Return order by
     *
     * @return string
     */
    function getOrderBy() {
      return $this->order_by;
    } // getOrderBy

  }