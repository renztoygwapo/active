<?php

  /**
   * Text Document versions helper implementation
   *
   * @package activeCollab.modules.files
   * @subpackage models
   */
  class ITextDocumentVersionsImplementation {
    
    /**
     * Parent object instance
     *
     * @var TextDocument
     */
    protected $object;
    
    /**
     * Construct text document versions helper
     *
     * @param TextDocument $object
     */
    function __construct(TextDocument $object) {
      if($object instanceof TextDocument) {
        $this->object = $object;
      } else {
        throw new InvalidInstanceError('object', $object, 'TextDocument');
      } // if
    } // __construct
    
    /**
     * Returns true if this text document has version
     *
     * @return boolean
     */
    function has() {
      
    } // has
    
    /**
     * Cached text document versions
     *
     * @var DBResult
     */
    private $versions = false;
    
    /**
     * Returns text document versions
     *
     * @return DBResult
     */
    function get() {
      if($this->versions === false) {
        $this->versions = TextDocumentVersions::findByTextDocument($this->object);
      } // if
      
      return $this->versions;
    } // get
    
    /**
     * Return previous version
     * 
     * @return FileVersion
     */
    function previous() {
      return TextDocumentVersions::find(array(
        'conditions' => array('text_document_id = ?', $this->object->getId()), 
        'order' => 'created_on DESC', 
      	'one' => TRUE
      ));
    } // previous
    
    /**
     * Return version
     * 
     * @param integer $version_num
     * @return TextDocumentVersion
     */
    function getVersion($version_num) {
    	return TextDocumentVersions::findByVersionNum($this->object, $version_num);
    } // getVersion
    
    /**
     * Create new text document version based on parent object
     *
     * @param boolean $save
     * @return TextDocumentVersion
     */
    function create($save = true) {
    	$version = new TextDocumentVersion();

    	// PK
    	$version->setTextDocumentId($this->object->getId());
    	$version->setVersionNum($this->object->getVersionNum());
    	
    	// Data
    	$version->setName($this->object->getName());
    	$version->setBody($this->object->getBody());
      $version->setCreatedOn($this->object->getLastVersionOn());
      $version->setCreatedBy($this->object->getLastVersionBy());

      if ($save) {
      	$version->save();
      } // if

      // update the main object
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
     * Revert to version
     *
     * @param TextDocumentVersion $version
     * @throws Exception
     * @return boolean
     */
    function revert(TextDocumentVersion $version) {
      try {
        DB::beginWork('Reverting to version @ ' . __CLASS__);

        // Save current version
        $this->create();

        // Revert properties
        $this->object->setName($version->getName());
        $this->object->setBody($version->getBody());
        $this->object->setUpdatedBy($version->getCreatedBy());

        $this->object->save();

        DB::commit('Reverted to an older version @ ' . __CLASS__);
      } catch(Exception $e) {
        DB::rollback('Failed to revert to older version @ ' . __CLASS__);
        throw $e;
      } // try
    } // revertToVersion
    
    /**
     * Add version related data to describe array
     *
     * @param IUser $user
     * @param boolean $detailed
     * @param boolean $for_interface
     * @return array
     */
    function describe(IUser $user, $detailed = false, $for_interface = false) {
    	$result = array();
    	$versions = $this->object->versions()->get();
    	if ($versions) {
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
     * @return array
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