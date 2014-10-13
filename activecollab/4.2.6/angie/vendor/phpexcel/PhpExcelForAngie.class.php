<?php

  /**
   * PHPExcel library interface for Angie
   *
   * @package angie.vendor.phpexcel
   */
  final class PhpExcelForAngie {

    /**
     * Include PHP Excel
     */
    static function includePhpExcel() {
      require_once PHP_EXCEL_FOR_ANGIE_PATH . '/phpexcel/PHPExcel.php';

      AngieApplication::registerAutoloader(function($pClassName) {
        if (strpos($pClassName, 'PHPExcel') !== false) {
          $pClassFilePath = PHP_EXCEL_FOR_ANGIE_PATH . '/phpexcel/' . str_replace('_', '/', $pClassName) . '.php';

          if (file_exists($pClassFilePath) && is_readable($pClassFilePath)) {
            require($pClassFilePath);
          } // if
        } // if
      });
    } // includePhpExcel

  }