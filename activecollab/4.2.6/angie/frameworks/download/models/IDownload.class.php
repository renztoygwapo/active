<?php

  /**
   * Interface that marks objects that can be downloaded
   *
   * @package angie.frameworks.environment
   * @subpackage models
   */
  interface IDownload {
    
    /**
     * Return download helper
     * 
     * @return IDownloadImplementation
     */
    function download();
    
    // ---------------------------------------------------
    //  Getters and setters
    // ---------------------------------------------------
    
    /**
     * Return name of the parent object
     *
     * @return string
     */
    function getName();
    
    /**
     * Set file name
     *
     * @param string $value
     * @return string
     */
    function setName($value);
    
    /**
     * Return MIME type of the file
     *
     * @return string
     */
    function getMimeType();
    
    /**
     * Set MIME type value
     *
     * @param string $value
     * @return string
     */
    function setMimeType($value);
    
    /**
     * Return file size
     * 
     * Even though we can read it from the disk, we always save it, so we can 
     * easily access the value without the need to use file system
     *
     * @return integer
     */
    function getSize();
    
    /**
     * Set file size value
     *
     * @param integer $value
     * @return integer
     */
    function setSize($value);
    
    /**
     * Return file name in /upload folder
     *
     * @return string
     */
    function getLocation();
    
    /**
     * Set file location
     *
     * @param string $value
     * @return string
     */
    function setLocation($value);
    
  }

?>