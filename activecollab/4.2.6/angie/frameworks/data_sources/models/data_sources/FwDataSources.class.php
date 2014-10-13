<?php

  /**
   * DataSources class
   *
   * @package angie.frameworks.data_sources
   * @subpackage models
   */
  class FwDataSources extends BaseDataSources {

    /**
     * Return slice of data sources definitions based on given criteria
     *
     * @param integer $num
     * @param array $exclude
     * @param integer $timestamp
     * @return DBResult
     */
    static function getSlice($num = 10, $exclude = null, $timestamp = null) {
      if($exclude) {
        return DataSources::find(array(
          'conditions' => array('id NOT IN (?)', $exclude),
          'order' => 'id',
          'limit' => $num,
        ));
      } else {
        return DataSources::find(array(
          'order' => 'id',
          'limit' => $num,
        ));
      } // if
    } // getSlice

    /**
     * Return test connection Url
     *
     * @return string
     */
    static function getTestConnectionUrl() {
      return Router::assemble('data_source_test_connection');
    } //getTestConnectionUrl
  
  }