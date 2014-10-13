<?php

  /**
   * Collec files proxy request handler
   * 
   * Class that implements basic behavior for type of handlers that collect 
   * files and forward them to the browser in a single request (collected CSS 
   * files, JS files etc)
   * Enter description here ...
   * 
   * @package angie.frameworks.environemtn
   * @subpackage proxies
   */
  abstract class CollectFilesProxy extends ProxyRequestHandler {
    
    /**
     * Cache use GZIP value
     *
     * @var boolean
     */
    private $use_gzip = null;
    
    /**
     * File pre-processor callback
     *
     * @var Closure
     */
    private $pre_processor;
    
    /**
     * Return content type of the data that handler will forward to the browser
     * 
     * @return string
     */
    abstract protected function getContentType();
    
    /**
     * Return array of files that need to be forwarded to the browser
     * 
     * @return array
     */
    abstract protected function getFiles();
    
    /**
     * Handle proxy request
     */
    function execute() {
      if($this->useGzip()) {
        @ob_start('ob_gzhandler');
      } else {
        ob_start();
      } // if

      // current hash dependable of version
      $hash = md5(implode(',', $_GET));
      
      // Set long expiration URL
      header('Content-type: ' . $this->getContentType());
      header("Cache-Control: public, max-age=315360000");
      header("Pragma: public");
      header("Expires: " . gmdate("D, d M Y H:i:s", (time() + 315360000)) . " GMT");
      header("Etag: " . $hash);
      
      // cache file if we have same version in cache and on the server
      $cached_hash = isset($_SERVER['HTTP_IF_NONE_MATCH']) && $_SERVER['HTTP_IF_NONE_MATCH'] ? $_SERVER['HTTP_IF_NONE_MATCH'] : null;
      if (COLLECTOR_CHECK_ETAG && $cached_hash && $cached_hash == $hash) {
      	header("HTTP/1.1 304 Not Modified");
      	die();
      } // if

      // Unlock session file, so we don't block further requests
      session_write_close();
      
      $pre_processor = $this->getPreProcessor();
      
      // And done, read all files and provide them in a single request
      foreach($this->getFiles() as $file) {
        if(!is_file($file)) {
          continue;
        } // if
        
        print $this->beforeFileContent($file);
        
        // Load and pre-process content
        $contents = file_get_contents($file);
        
        if($pre_processor instanceof Closure) {
          $contents = $pre_processor($contents, $file);
        } // if
        
        print $contents;
      } // foreach
    } // execute
    
    /**
     * Return true if we'll use GZIP output buffer to serve the data
     * 
     * @return boolean
     */
    protected function useGzip() {
      if($this->use_gzip === null) {
        $this->use_gzip = defined('COMPRESS_ASSET_REQUESTS') && COMPRESS_ASSET_REQUESTS && extension_loaded('zlib') && !((boolean) ini_get('zlib.output_compression'));
      } // if
      
      return $this->use_gzip;
    } // useGzip
    
    /**
     * Get files from a given folder and populate files array
     * 
     * @param array $files
     * @param string $folder
     * @param array $load_first
     */
    protected function collectFilesFromDir(&$files, $dir, $load_first = null) {
      if($load_first) {
        $load_first = (array) $load_first;
      } // if
      
      if(is_dir($dir)) {
        if($load_first) {
          foreach($load_first as $file) {
            if(is_file("$dir/$file") && !in_array("$dir/$file", $files)) {
              $files[] = "$dir/$file";
            } // if
          } // foreach
        } // if
        
        $dir_files = array();
        
        $d = dir($dir);
        while(($entry = $d->read()) !== false) {
          if(substr($entry, 0, 1) == '.' || ($load_first && in_array($entry, $load_first))) {
            continue;
          } // if
          
          if (in_array("$dir/$entry", $files)) {
          	continue;
          } // if
          
          $dir_files[] = "$dir/$entry";
        } // while
        $d->close();
        
        if(count($dir_files)) {
          sort($dir_files);
          
          $files = array_merge($files, $dir_files);
        } // if
      } // if
    } // collectFilesFromDir
    
    /**
     * Line that will be printed before file's content
     * 
     * Usually, we'll output file name before file content so we can see in the 
     * output what files are loaded
     * 
     * @param string $file
     * @return string
     */
    protected function beforeFileContent($file) {
      if(defined('APPLICATION_MODE') && (APPLICATION_MODE == 'in_debug_mode' || APPLICATION_MODE == 'in_development')) {
        return "\n\n/** $file **/\n\n";
      } else {
        return "\n\n";
      } // if
    } // beforeFileContent
    
    /**
     * Called after file content is sent to output buffer
     * 
     * @param string $file
     * @return string
     */
    protected function afterFileContent($file) {
      return "\n\n";
    } // afterFileContent
    
    /**
     * Return pre-processor closure
     * 
     * @return Closure
     */
    function getPreProcessor() {
      return $this->pre_processor;
    } // getPreProcessor
    
    /**
     * Set file pre-processor
     * 
     * @param $callback
     */
    function setPreProcessor($callback) {
      if($callback === null || $callback instanceof Closure) {
        $this->pre_processor = $callback;
      } else {
        throw new InvalidInstanceError('$callback', $callback, '$callback needs to be Closure instance or NULL');
      } // if
    } // setPreProcessor
    
  }