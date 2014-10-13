<?php

  /**
   * PHP Excel library initialisation file
   *
   * @package angie.vendor.phpexcel
   */

  const PHP_EXCEL_FOR_ANGIE_PATH = __DIR__;

  AngieApplication::setForAutoload(array(
    'PhpExcelForAngie' => PHP_EXCEL_FOR_ANGIE_PATH . '/PhpExcelForAngie.class.php',
  ));

  PhpExcelForAngie::includePhpExcel();