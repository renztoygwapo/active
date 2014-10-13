<?php

  /**
   * DataSource class
   *
   * @package angie.frameworks.data_sources
   * @subpackage models
   */
  abstract class FwDataSource extends BaseDataSource implements IRoutingContext {
  
    // Put custom methods here

    /**
     * Return data source name
     *
     * @return mixed
     */
    abstract function getDataSourceName();

    /**
     * Render data source options
     *
     * @param IUser $user
     * @return mixed
     */
    abstract function renderOptions(IUser $user);

    /**
     * Return import URL
     *
     * @return mixed
     */
    abstract function getImportUrl();

    /**
     * Return icon URL
     *
     * @return mixed
     */
    abstract function getIconUrl();

    /**
     * Validate import objects
     *
     * @param $params
     * @return mixed
     */
    abstract function validate_import($params);

    /**
     * Actually do import
     *
     * @param $params
     * @return mixed
     */
    function import($params) {
      ini_set('max_execution_time', 0);
    }//import

    /**
     * Return username
     *
     * @return string
     */
    public function getUsername() {
      return $this->getAdditionalProperty('username');
    } //getUsername

    /**
     * Set username
     *
     * @param $value
     * @return string
     */
    public function setUsername($value) {
      return $this->setAdditionalProperty('username', $value);
    } //setUsername

    /**
     * Return password
     *
     * @return string
     */
    public function getPassword() {
      return $this->getAdditionalProperty('password');
    } //getPassword

    /**
     * Set password
     *
     * @param $value
     * @return string
     */
    public function setPassword($value) {
      return $this->setAdditionalProperty('password', $value);
    } //setPassword

    /**
     * Return true if child class has method "testConnection"
     *
     * @return boolean
     */
    public function canTestConnection() {
      return method_exists($this, "testConnection");
    } //canTestConnection

    /**
     * Routing context name
     *
     * @var string
     */
    protected $routing_context = false;

    /**
     * Return routing context name
     *
     * @return string
     */
    function getRoutingContext() {
      if($this->routing_context === false) {
        $this->routing_context = 'data_source';
      } // if

      return $this->routing_context;
    } // getRoutingContext

    /**
     * Routing context parameters
     *
     * @var array
     */
    private $routing_context_params = false;

    /**
     * Return routing context parameters
     *
     * @return array
     */
    function getRoutingContextParams() {
      if($this->routing_context_params === false) {
        $this->routing_context_params = array(
          'data_source_id' => $this->getId(),
        );
      } // if

      return $this->routing_context_params;
    } // getRoutingContextParams

  }