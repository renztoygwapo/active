<?php

  /**
   * General set of functions for file handling
   *
   * @package angie.functions
   */

  /**
   * Check if specific folder is writable. 
   * 
   * is_writable() function has problems on Windows because it does not really 
   * checks for ACLs; it checks just the value of Read-Only property and that 
   * is incorect on some Windows installations.
   * 
   * This function will actually try to create (and delete) a test file in order
   * to check if folder is really writable
   *
   * @param string $path
   * @return boolean
   */
  function folder_is_writable($path) {
    if(!is_dir($path)) {
      return false;
    } // if
    
    do {
      $test_file = with_slash($path) . sha1(uniqid(rand(), true));
    } while(is_file($test_file));
    
    $put = @file_put_contents($test_file, 'test');
    if($put === false) {
      return false;
    } // if
    
    @unlink($test_file);
    return true;
  } // folder_is_writable
  
  /**
   * Check if specific file is writable
   * 
   * This function will try to open target file for writing (just open it!) in order to
   * make sure that this file is really writable. There are some known problems with
   * is_writable() on Windows (see description of folder_is_writable() function for more 
   * details).
   * 
   * @see folder_is_writable() function
   * @param string $path
   * @param bool $check_for_existance
   * @return boolean
   */
  function file_is_writable($path, $check_for_existance = true) {
    if(is_file($path)) {
      $open = @fopen($path, 'a+');
      if($open === false) {
        return false;
      } // if

      @fclose($open);
      return true;
    } else {
      if($check_for_existance) {
        return false;
      } else {
        return folder_is_writable(dirname($path));
      } // if
    } // if
  } // file_is_writable
  
  /**
   * Return the files a from specific directory
   * 
   * This function will walk through $dir and read all file names. Result can be filtered by file extension (accepted 
   * param is single extension or array of extensions). If $recursive is set to true this function will walk recursivlly 
   * through subfolders.
   *
   * Example:
   * <pre>
   * $files = get_files($dir, array('doc', 'pdf', 'xst'));
   * foreach($files as $file_path) {
   *   print $file_path;
   * } // if
   * </pre>
   *
   * @param string $dir
   * @param mixed $extension
   * @param boolean $recursive
   * @return array
   */
  function get_files($dir, $extension = null, $recursive = false) {
    if(!is_dir($dir)) {
      return false;
    } // if
    
    $dir = with_slash($dir);
    if(!is_null($extension)) {
      if(is_array($extension)) {
        foreach($extension as $k => $v) {
          $extension[$k] = strtolower($v);
        } // foreach
      } else {
        $extension = strtolower($extension);
      } // if
    } // if
    
    $d = dir($dir);
    $files = array();
    
    while(($entry = $d->read()) !== false) {
      if(str_starts_with($entry, '.')) {
        continue;
      } // if
      
      $path = $dir . $entry;
      
      if(is_file($path)) {
        if(is_null($extension)) {
          $files[] = $path;
        } else {
          if(is_array($extension)) {
            if(in_array(strtolower(get_file_extension($path)), $extension)) {
              $files[] = $path;
            } // if
          } else {
            if(strtolower(get_file_extension($path)) == $extension) {
              $files[] = $path;
            } // if
          } // if
        } // if
      } elseif(is_dir($path)) {
        if($recursive) {
          $subfolder_files = get_files($path, $extension, true);
          if(is_array($subfolder_files)) {
            $files = array_merge($files, $subfolder_files);
          } // if
        } // if
      } // if
      
    } // while
    
    $d->close();
    return count($files) > 0 ? $files : null;
  } // get_files
  
  /**
   * Return the folder list in provided directory folders are returned with 
   * absolute path
   * 
   * This function ignores hidden folders!
   * 
   * @param string $dir
   * @param boolean $recursive
   * @return array
   */
  function get_folders($dir, $recursive = false) {
    if(is_dir($dir)) {
      $folders = array();
    
      if($dirstream = @opendir($dir)) {
        while(false !== ($filename = readdir($dirstream))) {
           $path = with_slash($dir) . $filename;
           if(substr($filename, 0, 1) != '.' && is_dir($path)) {
            $folders[] = $path;
            if($recursive) {
              $sub_folders = get_folders($path, $recursive);
              if(is_array($sub_folders)) {
                $folders = array_merge($folders, $sub_folders);
              } // if
            } // if
          } // if
        } // while
      } // if
      
      closedir($dirstream);
      return $folders;
    } else {
      return false;
    } // if
  } // get_folders
  
  /**
   * get folders with priority
   * 
   * @param string $dir
   * @param array $load_first
   * @return string
   */
  function get_folders_with_priority($dir, $load_first) {
    if (!is_dir($dir)) {
      return false;
    } // if
    
    $load_first = (array) $load_first;
    $result = array();
    
    if (is_foreachable($load_first)) {
      foreach ($load_first as $priority_folder) {
        $possible_folder = "$dir/$priority_folder"; 
        if (is_dir($possible_folder)) {
          $result[] = $possible_folder;
        } // if
      } // foreach
    } // if
    
    $d = dir($dir);
     while(($entry = $d->read()) !== false) {
       $possible_folder = "$dir/$entry"; 
       
      if (substr($entry, 0, 1) == '.' || !is_dir($possible_folder) || in_array($entry, $load_first)) {
        continue;
      }  // if
      
      $result[] = $possible_folder;
    } // while
    $d->close();
    
    return $result;
  } // get_folders_with_priority
  
  /**
   * Return file extension from specific filename. Examples:
   * 
   * get_file_extension('index.php') -> returns 'php'
   * get_file_extension('index.php', true) -> returns '.php'
   * get_file_extension('Blog.class.php', true) -> returns '.php'
   *
   * @param string $path File path
   * @param boolean $leading_dot Include leading dot
   * @return string
   */
  function get_file_extension($path, $leading_dot = false) {
    $filename = basename($path);
    $dot_offset = (boolean) $leading_dot ? 0 : 1;
    
    if( ($pos = strrpos($filename, '.')) !== false ) {
      return substr($filename, $pos + $dot_offset, strlen($filename));
    } // if
    
    return '';
  } // get_file_extension
   
  /**
   * Get mime type
   *
   * @param string $file
   * @param string $real_filename
   * @param bool $use_native_functions - if false, mime type will be determined by file extension
   *
   * @return string
   */
  function get_mime_type($file, $real_filename = null, $use_native_functions = true) {
    if (function_exists('mime_content_type') && $use_native_functions) {
      $mime_type = trim(mime_content_type($file));
      if (!$mime_type) {
        return 'application/octet-stream';
      } // if
      $mime_type = explode(';', $mime_type);
      return $mime_type[0];
    } else if (function_exists('finfo_open') && function_exists('finfo_file') && $use_native_functions) {
      $finfo = finfo_open(FILEINFO_MIME_TYPE);
      $mime_type = finfo_file($finfo, $file);
      finfo_close($finfo);
      return $mime_type;
    } else {
      if ($real_filename) {
        $file = $real_filename;
      } // if
      
      $mime_types = array(
        'txt'    => 'text/plain',
        'htm'    => 'text/html',
        'html'  => 'text/html',
        'php'    => 'text/html',
        'css'    => 'text/css',
        'js'    => 'application/javascript',
        'json'  => 'application/json',
        'xml'    => 'application/xml',
        'swf'    => 'application/x-shockwave-flash',
        'flv'    => 'video/x-flv',
        'png'    => 'image/png',
        'jpe'    => 'image/jpeg',
        'jpeg'  => 'image/jpeg',
        'jpg'    => 'image/jpeg',
        'gif'    => 'image/gif',
        'bmp'    => 'image/bmp',
        'ico'    => 'image/vnd.microsoft.icon',
        'tiff'  => 'image/tiff',
        'tif'    => 'image/tiff',
        'svg'    => 'image/svg+xml',
        'svgz'  => 'image/svg+xml',
        'zip'    => 'application/zip',
        'rar'    => 'application/x-rar-compressed',
        'exe'    => 'application/x-msdownload',
        'msi'    => 'application/x-msdownload',
        'cab'    => 'application/vnd.ms-cab-compressed',
        'mp3'    => 'audio/mpeg',
        'qt'    => 'video/quicktime',
        'mov'    => 'video/quicktime',
        'pdf'    => 'application/pdf',
        'psd'    => 'image/vnd.adobe.photoshop',
        'ai'    => 'application/postscript',
        'eps'    => 'application/postscript',
        'ps'    => 'application/postscript',
        'doc'    => 'application/msword',
        'rtf'    => 'application/rtf',
        'xls'    => 'application/vnd.ms-excel',
        'ppt'    => 'application/vnd.ms-powerpoint',
        'odt'    => 'application/vnd.oasis.opendocument.text',
        'ods'    => 'application/vnd.oasis.opendocument.spreadsheet',
      );
  
      $extension = strtolower(get_file_extension($file));
      if (array_key_exists($extension, $mime_types)) {
        return $mime_types[$extension];
      } else {
        return 'application/octet-stream';
      } // if
    } // if
  } // get_mime_type
  
  /**
   * Walks recursively through directory and calculates its total size
   *
   * @param string $dir Directory
   * @param boolean $skip_files_starting_with_dot
   * @return integer
   */
  function dir_size($dir, $skip_files_starting_with_dot = true) {
    $totalsize = 0;
    
    if($dirstream = @opendir($dir)) {
      while(false !== ($filename = readdir($dirstream))) {
        $path = with_slash($dir) . $filename;

        if (is_link($path)) continue;

        if ($skip_files_starting_with_dot) {
          if(($filename != '.') && ($filename != '..') && ($filename[0]!='.')) {
            if (is_file($path)) $totalsize += filesize($path);
            if (is_dir($path)) $totalsize += dir_size($path, $skip_files_starting_with_dot);
          } // if
        } else {
          if(($filename != '.') && ($filename != '..')) {
            if (is_file($path)) $totalsize += filesize($path);
            if (is_dir($path)) $totalsize += dir_size($path, $skip_files_starting_with_dot);
          } // if
        }
      } // while
    } // if
    
    closedir($dirstream);
    return $totalsize;
  } // dir_size
  
  /**
   * Create a new directory
   * 
   * This function will try to create a directory in $path. If $make_writable is 
   * set to true it will also try to chmod it so PHP can write files in it
   *
   * @param string $path
   * @param boolean $make_writable
   * @return boolean
   */
  function create_dir($path, $make_writable = false) {
    if(mkdir($path)) {
      if($make_writable) {
        if(!chmod($path, 0777)) {
          return false;
        } // if
      } // if
    } else {
      return false;
    } // if
    
    return true;
  } // create_dir
  
  /**
   * does the same as mkdir function on php5, except it's compatible with php4,
   * so folders are created recursive
   *
   * @param string $path
   * @param integer $mode
   * @param string $restriction_path
   * @return boolean
   */
  function recursive_mkdir($path, $mode = 0777, $restriction_path = '/') {
    if (DIRECTORY_SEPARATOR == '/') {
      if (strpos($path,$restriction_path) !== 0) {
        return false;
      } // if
    } else {
      if (strpos(fix_slashes(strtolower($path)), fix_slashes(strtolower($restriction_path))) !== 0) {
        return false;
      } // if
    } // if

    $start_path = substr($path,0,strlen($restriction_path));
    $allowed_path = substr($path, strlen($restriction_path));
    $original_path = $path;
    $path = fix_slashes($allowed_path);
    $dirs = explode('/' , $path);
    $count = count($dirs);
    $path = '';
    for ($i = 0; $i < $count; ++$i) {
      if ($i == 0) {
        $path = $start_path;
      } // if
      if (DIRECTORY_SEPARATOR == '\\' && $path=='') {
        $path .= $dirs[$i];
      } else {
        $path .= '/' . $dirs[$i];
      } // if
      if (!is_dir($path)) {
        if(mkdir($path, $mode)) {
          if(DIRECTORY_SEPARATOR  != '\\' && !chmod($path, $mode)) {
            return false;
          } // if
        } else {
          return false;
        } // if
      } // if
    } // if
    
    return is_dir($original_path);
  } // recursive_mkdir
  
  /**
   * Recursive remove directory
   *
   * @param string $folder
   * @param string $restriction_path
   * @return boolean
   */
  function recursive_rmdir($folder, $restriction_path = '/') {
    if (DIRECTORY_SEPARATOR == '/') {
      if (strpos($folder, $restriction_path) !== 0) {
        return false;
      } // if
    } else {
      if (strpos(strtolower($folder), strtolower($restriction_path)) !== 0) {
        return false;
      } // if
    } // if
    
    if (is_dir($folder)) {
      $folders = get_folders($folder);
      $files = get_files($folder);
      
      if(is_array($folders) && is_array($files)) {
        $paths = array_merge($folders, $files);
      } elseif($folders) {
        $paths = $folders;
      } elseif($files) {
        $paths = $files;
      } else {
        $paths = null;
      } // if
      
      if($paths) {
        foreach($paths as $path) {
          if (is_dir($path) && !is_link($path)) {
            recursive_rmdir($path, $restriction_path);
          } else {
            unlink($path);
          } // if
        }  // foreach
      } // if
      
      return rmdir($folder);
    } // if
    
    return true;
  } // recursive_mkdir
    
  /**
    * Delete $dir only if $base_dir is parent of $dir
    *
    * @param string $dir
    * @param string $base_dir
    * @return boolean
    */
  function safe_delete_dir($dir, $base_dir) {
    if (strpos($dir, $base_dir) === 0) {
      return delete_dir($dir);
    }
    return false;
  } // safe_delete_dir
  
  /**
   * Remove specific directory
   * 
   * This function will walk recursivly through $dir and its subdirectories and delete all content
   *
   * @param string $dir Directory path
   * @return boolean
   */
  function delete_dir($dir) {
    if(!is_dir($dir)) {
      return false;
    } // if
    
    $dh = opendir($dir);
    while($file = readdir($dh)) {
      if(($file != ".") && ($file != "..")) {
        $fullpath = $dir . "/" . $file;
        
        if(is_dir($fullpath)) {
          delete_dir($fullpath);
        } else {
          unlink($fullpath);
        } // if
      } // if
    } // while

    closedir($dh);
    return (boolean) rmdir($dir);
  } // delete_dir
  
  /**
   * Remove all files and folders from a given directory
   * 
   * @param string $dir
   * @param boolean $ignore_hidden_files
   * @return boolean
   */
  function empty_dir($dir, $ignore_hidden_files = false) {
    if(is_dir($dir)) {
      $dh = opendir($dir);
      while($file = readdir($dh)) {
        if($file == '.' || $file == '..' || ($ignore_hidden_files && substr($file, 0, 1) == '.')) {
          continue;
        } // if

        $fullpath = $dir . "/" . $file;

        if(is_dir($fullpath)) {
          delete_dir($fullpath);
        } else {
          unlink($fullpath);
        } // if
      } // while
  
      closedir($dh);
      return true;
    } // if

    return false;
  } // empty_dir
  
  /**
   * Copy folder tree and returns a true if all tree is copied and false if there was errors
   *
   * @param string $source_dir
   * @param string $destination_dir
   * @param mixed $skip
   * @param boolean $create_destination
   * @return boolean
   */
  function copy_dir($source_dir, $destination_dir, $skip = null, $create_destination = false) {
    if(is_dir($source_dir)) {
      if(!is_dir($destination_dir)) {
        if(!$create_destination || !mkdir($destination_dir, 0777, true)) {
          return false;
        } // if
      } // if
      
      $result = true;
      
      $d = dir($source_dir);
      if($d) {
        while(false !== ($entry = $d->read())) {
          if($entry == '.' || $entry == '..') {
            continue;
          } // if
          
          if($skip && in_array($entry, $skip)) {
            continue;
          } // if
          
          if(is_dir("$source_dir/$entry")) {
            $result = $result && copy_dir("$source_dir/$entry", "$destination_dir/$entry", $skip, true);
          } elseif(is_file("$source_dir/$entry")) {
            if(copy("$source_dir/$entry", "$destination_dir/$entry")) {
              chmod("$destination_dir/$entry", 0777);
            } else {
              $result = false;
            } // if
          } // if
        } // while
      } // if
      
      $d->close();
      return $result;
    } // if
    
    return false;
  } // copy_dir
  
  /**
   * This function will return true if $dir_path is empty
   * 
   * If $ignore_hidden is set to true any file or folder which name starts with . will be ignored 
   *
   * @param string $dir_path
   * @param boolean $ignore_hidden
   * @return boolean
   */
  function is_dir_empty($dir_path, $ignore_hidden = false) {
    if(!is_dir($dir_path)) {
      return false;
    } // if
    
    $d = dir($dir_path);
    if($d) {
      while(false !== ($entry = $d->read())) {
        if(($entry == '.') || ($entry == '..')) {
          continue;
        } // if
        
        if($ignore_hidden && ($entry{0} == '.')) {
          continue;
        } // if
        
        $d->close();
        return false;
      } // while
    } // if
    
    $d->close();
    return true;
  } // is_dir_empty
  
  /**
   * Return path relative to a given path
   *
   * @param string $path
   * @param $relative_to
   * @return string
   */
  function get_path_relative_to($path, $relative_to) {
    return substr($path, strlen($relative_to));
  } // get_path_relative_to

  /**
   * Format filesize
   *
   * @param string $value
   * @param boolean $trim_zeros
   * @return string
   */
  function format_file_size($value, $trim_zeros = true) {
    $data = array(
      'TB' => 1099511627776,
      'GB' => 1073741824,
      'MB' => 1048576,
      'kb' => 1024,
    );

    // commented because of integer overflow on 32bit sistems
    // http://php.net/manual/en/language.types.integer.php#language.types.integer.overflow
    // $value = (integer) $value;
    foreach($data as $unit => $bytes) {
      $in_unit = $value / $bytes;
      if($in_unit > 0.9) {
        $formatted_number = number_format($in_unit, 2, NUMBER_FORMAT_DEC_SEPARATOR, NUMBER_FORMAT_THOUSANDS_SEPARATOR);

        if($trim_zeros) {
          $formatted_number = trim(rtrim($formatted_number, '0'), NUMBER_FORMAT_DEC_SEPARATOR);
        } // if

        return $formatted_number . $unit;
      } // if
    } // foreach

    return (!empty($value) ? $value : 0) . 'b';
  } // format_file_size

  /**
   * Get stdin content
   *
   * @return string
   */
  function get_stdin() {
    $content = '';

    // open stdin
    $stdin = fopen('php://stdin', 'r');

    // loop and get content
    while(!feof($stdin)){
      $content.= fgets($stdin, 4096);
    } // while

    // close stdin
    fclose($stdin);

    return $content;
  } // get_stdin

  /**
   * Decode base64 encoded file
   *
   * @param string $input_file
   * @param string $output_file
   * @return boolean
   */
  function base64_decode_file($input_file, $output_file) {
    $input_handle = fopen($input_file, 'r');
    if (!$input_handle) {
      return false;
    } // if
    $output_handle = fopen($output_file, 'w');
    if (!$output_handle) {
      return false;
    } // if
    
    while (!feof($input_handle)) {
      $encoded = fgets($input_handle);
      $decoded = base64_decode($encoded);
      fwrite($output_handle, $decoded);
    } // while;
    
    if (fclose($input_handle) && fclose($output_handle)) {
      return true;
    }
    return false;
  } // base64_decode_file

  /**
   * Decode quoted_printable encoded file
   *
   * @param string $input_file
   * @param string $output_file
   * @return boolean
   */
  function quoted_printable_decode_file($input_file, $output_file) {
    $input_handle = fopen($input_file, 'r');
    if (!$input_handle) {
      return false;
    } // if
    $output_handle = fopen($output_file, 'w');
    if (!$output_handle) {
      return false;
    } // if
    
    while (!feof($input_handle)) {
      $encoded = fgets($input_handle);
      $breaklines = 0;
      if (substr($encoded,-1) == "\n" || substr($encoded, -1) == "\r") {
        $breaklines ++;
      } // if
      if (substr($encoded,-2, 1) == "\n" || substr($encoded, -2, 1) == "\r") {
        $breaklines ++;
      } // if
      if (substr($encoded, -3 - $breaklines, 3) == '=0D') {
        $encoded = (substr($encoded, 0, -3 - $breaklines) . substr($encoded, - $breaklines));
      } // if
      $decoded = quoted_printable_decode($encoded);
      fwrite($output_handle, $decoded);
    } // while;
    
    if (fclose($input_handle) && fclose($output_handle)) {
      return true;
    } // if
    return false;
  } // quoted_printable_decode_file
  
  /**
   * Check if file source can be displayed
   *
   * @param string $filename
   * @return boolean
   */
  function file_source_can_be_displayed($filename) {
    return in_array(get_file_extension($filename), get_displayable_file_types());
  } // file_source_can_be_displayed
  
  /**
   * Get file extensions for files whose source makes sense when printed 
   *
   * @param null
   * @return array
   */
  function get_displayable_file_types() {
    return array(  
    'ada',
    'adb',
    'adp',
    'ads',
    'ans',
    'as',
    'asc',
    'asm',
    'asp',
    'aspx',
    'atom',
    'au3',
    'bas',
    'bat',
    'bmax',
    'bml',
    'c',
    'cbl',
    'cc',
    'cfm',
    'cgi',
    'cls',
    'cmd',
    'cob',
    'cpp',
    'cs',
    'css',
    'csv',
    'cxx',
    'd',
    'dif',
    'dist',
    'dtd',
    'e',
    'efs',
    'egg',
    'egt',
    'f',
    'f77',
    'for',
    'frm',
    'frx',
    'ftn',
    'ged',
    'gitattributes',
    'gitignore',
    'gm6',
    'gmd',
    'gml',
    'h',
    'hpp',
    'hs',
    'hta',
    'htaccess',
    'htm',
    'html',
    'hxx',
    'ici',
    'ictl',
    'ihtml',
    'inc',
    'inf',
    'info',
    'ini',
    'install',
    'java',
    'js',
    'jsfl',
    'json',
    'l',
    'las',
    'lasso',
    'lassoapp',
    'less',
    'log',
    'lua',
    'm',
    'm4',
    'makefile',
    'manifest',
    'md',
    'met',
    'metalink',
    'ml',
    'module',
    'mrc',
    'n',
    'ncf',
    'nfo',
    'nut',
    'p',
    'pas',
    'php',
    'php3',
    'php4',
    'php5',
    'phps',
    'phtml',
    'piv',
    'pl',
    'pm',
    'pp',
    'properties',
    'ps1',
    'ps1xml',
    'psc1',
    'psd1',
    'psm1',
    'py',
    'pyc',
    'pyi',
    'rb',
    'rdf',
    'resx',
    'rss',
    's',
    'scm',
    'scpt',
    'sh',
    'shtml',
    'spin',
    'sql',
    'ss',
    'stk',
    'svg',
    'tab',
    'tcl',
    'tpl',
    'txt',
    'vb',
    'vbp',
    'vbs',
    'xht',
    'xhtml',
    'xml',
    'xsl',
    'xslt',
    'xul',
    'y',
    'yml'
    );
  } // get_displayable_file_types
  
  /**
   * Check if file is an image
   *
   * @param string $filename
   * @return boolean
   */
  function file_is_image($filename) {
    return in_array(get_file_extension($filename), get_image_file_types());
  } // file_is_image
  
  /**
   * Get file extensions for image files  
   *
   * @param null
   * @return array
   */
  function get_image_file_types() {
    return array(  
    'gif',
    'jpg',
    'jpeg',
    'png',
    'wbmp',
    );
  } // get_image_file_types
  
  /**
   * Creates attachment from uploaded file
   *
   * @param array $file
   * @param ApplicationObject $parent
   * @return Attachment
   * @throws Error
   */
  function &make_attachment($file, $parent = null) {
    if (!isset($file) || !isset($file['tmp_name'])) {
      throw new Error(lang('File is not uploaded'));
    } // if
    
    $destination_file = AngieApplication::getAvailableUploadsFileName();
    
    if (!move_uploaded_file($file['tmp_name'], $destination_file)) {
      throw new Error(lang('Could not move uploaded file to uploads directory'));
    } // if
    
    $attachment = new Attachment();
    $attachment->setName($file['name']);
    $attachment->setLocation(basename($destination_file));
    $attachment->setMimeType(array_var($file,'type','application/octet-stream'));
    $attachment->setSize(array_var($file, 'size', 0));
    
    if($parent instanceof ApplicationObject) {
      $attachment->setParent($parent);
    } // if
    
    $attachment->save();
    
    return $attachment; 
  } // make_attachment
  
  /**
   * Move uploaded file to the temp directory and prepend filename with random hash
   * 
   * @param string $file
   * @param string $destination_sufix_name
   * @return string temp filename
   */
  function move_uploaded_file_to_temp_directory($file, $destination_sufix_name) {
    do {
      $temp_destination = WORK_PATH.'/'.make_string(10).'_'.$destination_sufix_name;
    } while (is_file($temp_destination));
    
    if (move_uploaded_file($file, $temp_destination)) {
      return $temp_destination;
    } // if

    return false;
  } // move_uploaded_file_to_temp_directory
  
  /**
   * Get descriptive message about upload error
   * 
   * @param integer $error_code
   * @return string
   */
  function get_upload_error_message($error_code) {
    switch ($error_code) {
      case UPLOAD_ERR_OK:
        $error_message = lang('File uploaded successfully');
      break;
      
      case UPLOAD_ERR_INI_SIZE:
        $error_message = lang('The uploaded file exceeds the upload_max_filesize directive in php.ini');
        break;
        
      case UPLOAD_ERR_FORM_SIZE:
        $error_message = lang('The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form');
        break;
        
      case UPLOAD_ERR_PARTIAL:
        $error_message = lang('The uploaded file was only partially uploaded');
        break;
        
      case UPLOAD_ERR_NO_FILE:
        $error_message = lang('No file was uploaded');
        break;
        
      case UPLOAD_ERR_NO_TMP_DIR:
        $error_message = lang('Missing a temporary folder');
        break;
        
      case UPLOAD_ERR_CANT_WRITE:
        $error_message = lang('Failed to write file to disk');
        break;
        
      case UPLOAD_ERR_EXTENSION:
        $error_message = lang('A PHP extension stopped the file upload');
        break;
      
      default:
        $error_message = lang('Unknown upload error occurred');
      break;
    }
    
    return $error_message;
  } // get_upload_error_message

  /**
   * PHP size to bytes
   *  - php sizes are usually used in ini files
   *    example: 128G, 50M, 11K etc..
   *
   * @param $val
   * @return int|string
   */
  function php_size_to_bytes($val) {
    $val = trim($val);
    $last = strtolower($val[strlen($val)-1]);
    switch($last) {
      // The 'G' modifier is available since PHP 5.1.0
      case 'g':
        $val *= 1024;
      case 'm':
        $val *= 1024;
      case 'k':
        $val *= 1024;
    }

    return $val;
  } // php_size_to_bytes
