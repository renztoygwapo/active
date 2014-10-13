<?php
  /**
   * TCPDF init file
   * 
   * @package angie.library.tcpdf
   */

  define('TCPDF_LIB_PATH', ANGIE_PATH . '/classes/tcpdf');
  define('TCPDF_FONTS_PATH', TCPDF_LIB_PATH.'/fonts');
  
  // Paper sizes
  define('PAPER_FORMAT_A3' , 'A3');
  define('PAPER_FORMAT_A4' , 'A4');
  define('PAPER_FORMAT_A5' , 'A5');
  define('PAPER_FORMAT_LETTER' , 'Letter');
  define('PAPER_FORMAT_LEGAL' , 'Legal');
  
  define('DEFAULT_PAPER_FORMAT', PAPER_FORMAT_A3);
  
  // Paper orientation
  define('PAPER_ORIENTATION_PORTRAIT', 'Portrait');
  define('PAPER_ORIENTATION_LANDSCAPE', 'Landscape');
  
  define('DEFAULT_PAPER_ORIENTATION', PAPER_ORIENTATION_PORTRAIT);
  
  // TCPDF internal constants that are required
	define('K_TCPDF_EXTERNAL_CONFIG', true);
	define('K_PATH_MAIN', TCPDF_LIB_PATH);
	define('K_PATH_FONTS', TCPDF_FONTS_PATH . '/');
	define('K_CELL_HEIGHT_RATIO', 1.25);
	define('K_PATH_CACHE', WORK_PATH . '/');
  require_once(TCPDF_LIB_PATH.'/tcpdf.php');
?>