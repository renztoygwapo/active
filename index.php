<?php

  /**
   * Make sure that all requests are routed through /public/index.php
   */

  header('HTTP/1.1 302 Found');
  if(isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on' || $_SERVER['SERVER_PORT'] == 443) {
    header('Location: https://' . $_SERVER['HTTP_HOST'] . rtrim(str_replace('\\', '/', dirname($_SERVER['PHP_SELF'])), '/') . '/public/index.php' . (isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'] ? "?$_SERVER[QUERY_STRING]" : ''));
  } else {
    header('Location: http://' . $_SERVER['HTTP_HOST'] . rtrim(str_replace('\\', '/', dirname($_SERVER['PHP_SELF'])), '/') . '/public/index.php' . (isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'] ? "?$_SERVER[QUERY_STRING]" : ''));
  } // if