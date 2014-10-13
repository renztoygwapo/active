<?php

  /**
   * General purpose and compatibility constants
   *
   * @package angie
   */

  // Some nice to have regexps
  const EMAIL_FORMAT = "/^([a-z0-9+_]|\\-|\\.)+@(([a-z0-9_]|\\-)+\\.)+[a-z]{2,6}\$/i";
  const URL_FORMAT = '/^(http|https):\/\/(([\w]+:)?\/\/)?(([\d\w]|%[a-fA-f\d]{2,2})+(:([\d\w]|%[a-fA-f\d]{2,2})+)?@)?([\d\w][-\d\w]{0,253}[\d\w]\.)+[\w]{2,6}(:[\d]+)?(\/([-+_~.\d\w]|%[a-fA-f\d]{2,2})*)*(\?(\&?([-+_~.\d\w]|%[a-fA-f\d]{2,2})=?)*)?(#([-+_~.\d\w]|%[a-fA-f\d]{2,2})*)?$/';
  const IP_URL_FORMAT = "/^(http|https):\/\/((1?\d{1,2}|2[0-4]\d|25[0-5])\.){3}(1?\d{1,2}|2[0-4]\d|25[0-5])((:[0-9]{1,5})?\/.*)?$/";
  const IP_FORMAT = "/^((1?\d{1,2}|2[0-4]\d|25[0-5])\.){3}(1?\d{1,2}|2[0-4]\d|25[0-5])$/";

  const DATE_MYSQL = 'Y-m-d';
  const DATETIME_MYSQL = 'Y-m-d H:i:s';
  const EMPTY_DATE = '0000-00-00';
  const EMPTY_DATETIME = '0000-00-00 00:00:00';
 
  // Comparision operators
  const COMPARE_LT = '<';
  const COMPARE_LE = '<=';
  const COMPARE_GT = '>';
  const COMPARE_GE = '>=';
  const COMPARE_EQ = '==';
  const COMPARE_NE = '!=';
  
  const FORMAT_HTML = 'html';
  const FORMAT_XML = 'xml';
  const FORMAT_JSON = 'json';
  const FORMAT_ICAL = 'ical';
  
  const FEED_RSS = 'application/rss+xml';
  const FEED_ATOM = 'application/atom+xml';
  
  // HTTP errors
  const HTTP_ERR_BAD_REQUEST = 400;
  const HTTP_ERR_UNAUTHORIZED = 401;
  const HTTP_ERR_FORBIDDEN = 403;
  const HTTP_ERR_NOT_FOUND = 404;
  const HTTP_ERR_INVALID_PROPERTIES = 409;
  const HTTP_ERR_CONFLICT = 409;
  const HTTP_ERR_OPERATION_FAILED = 500;
  
  // Directory separator
  defined('DIRECTORY_SEPARATOR') or define('DIRECTORY_SEPARATOR', strtoupper(substr(PHP_OS, 0, 3) == 'WIN') ? '\\' : '/');