<?php

  /**
   * Exception that is thrown when export fails
   *
   * @package angie.frameworks.reports
   * @subpackage models
   */
  class DataFilterExportError extends Error {

    /**
     * Error code
     *
     * @var integer
     */
    protected $export_code;

    /**
     * Construct new error instance
     *
     * @param integer $code
     * @param string $message
     */
    function __construct($code, $message = null) {
      if($message === null) {
        switch($code) {
          case DataFilter::EXPORT_ERROR_ALREADY_STARTED:
            $message = 'Export already initiated';
            break;
          case DataFilter::EXPORT_ERROR_CANT_OPEN_HANDLE:
            $message = 'Cannot open temp file handle for export';
            break;
          case DataFilter::EXPORT_ERROR_HANDLE_NOT_OPEN:
            $message = 'Export temp file handle not open';
            break;
          default:
            $message = 'Unknown export error';
        } // switch
      } // if

      $this->export_code = $code;

      parent::__construct($message, array(
        'code' => $code,
      ));
    } // __construct

  }