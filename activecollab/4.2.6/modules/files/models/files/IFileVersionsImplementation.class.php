<?php

  /**
   * File versions helper implementation
   *
   * @package activeCollab.modules.files
   * @subpackage models
   */
  class IFileVersionsImplementation {
    
    /**
     * Parent object instance
     *
     * @var File
     */
    protected $object;
    
    /**
     * Construct file versions helper
     *
     * @param File $object
     */
    function __construct(File $object) {
      if($object instanceof File) {
        $this->object = $object;
      } else {
        throw new InvalidInstanceError('object', $object, 'File');
      } // if
    } // __construct
    
    /**
     * Cached has versions cache
     *
     * @var boolean
     */
    private $has_versions = null;
    
    /**
     * Returns true if this file has version
     *
     * @return boolean
     */
    function has() {
      if($this->has_versions === null) {
        if($this->versions === false) {
          $this->has_versions = (boolean) $this->count();
        } else {
          $this->has_versions = !empty($this->versions);
        } // if
      } // if
      
      return $this->has_versions;
    } // has
    
    /**
     * Cached file versions
     *
     * @var DBResult
     */
    private $versions = false;
    
    /**
     * Returns file versions
     *
     * @return DBResult
     */
    function get() {
      if($this->versions === false) {
        $this->versions = FileVersions::findByFile($this->object);
      } // if
      
      return $this->versions;
    } // get

    /**
     * Return number of versions that parent file has
     *
     * @return int
     */
    function count() {
      return FileVersions::countByFile($this->object);
    } // count
    
    /**
     * Return previous version
     * 
     * @return FileVersion
     */
    function previous() {
      return FileVersions::findLastByFile($this->object);
    } // previous
    
    /**
     * Create new file version based on parent object
     *
     * @param boolean $save
     * @return FileVersion
     */
    function create($save = true) {
      $version = new FileVersion();
      
      // PK
      $version->setFileId($this->object->getId());
      $version->setVersionNum($this->object->getVersionNum());
      
      // File meta data
      $version->setName($this->object->getName());
      $version->setMimeType($this->object->getMimeType());
      $version->setSize($this->object->getSize());
      $version->setLocation($this->object->getLocation());
      $version->setMd5($this->object->getMd5());
			$version->setCreatedOn($this->object->getLastVersionOn());
	    $version->setCreatedBy($this->object->getLastVersionBy());
      
      if($save) {
        $version->save();
      } // if
      
      // Increment document's version number
      $this->object->setVersionNum($this->object->getVersionNum() + 1);
			$this->object->setUpdatedBy(Authentication::getLoggedUser());
      $this->object->setUpdatedOn(new DateTimeValue());
      $this->object->setLastVersionBy($this->object->getUpdatedBy());
      $this->object->setLastVersionOn($this->object->getUpdatedOn());
      
      if ($save) {
      	$this->object->save();
      } // if
      
      return $version;
    } // create
    
    /**
     * Add version related data to describe array
     * 
     * @param IUser $user
     * @param boolean $detailed
     * @param boolean $for_interface
     */
    function describe(IUser $user, $detailed = false, $for_interface = false) {
    	$result = array();
    	$versions = $this->object->versions()->get();
    	if (is_foreachable($versions)) {
    		foreach ($versions as $version) {
    			$result[] = $version->describe($user, $detailed, $for_interface);
    		} // foreach
    	} // if
    	
			return $result;
    } // describe

    /**
     * Add version related data to describe array
     *
     * @param IUser $user
     * @param boolean $detailed
     */
    function describeForApi(IUser $user, $detailed = false) {
      $result = array();
      $versions = $this->object->versions()->get();
      if ($versions) {
        foreach ($versions as $version) {
          $result[] = $version->describeForApi($user);
        } // foreach
      } // if

      return $result;
    } // describeForApi
    
  }