<?php 

	/**
	 * BaseRepsitePage class
	 *
	 * @package ActiveCollab.modules.system
	 * @subpackage models
	 */
	abstract class BaseRepsitePage extends ApplicationObject {
		/**
		 * Name of the table where records are stored
		 *
		 * @var string
		 */
    	protected $table_name = 'repsite_pages';
  		
  		/**
     	 * All table fields
    	 *
    	 * @var array
    	 */
    	protected $fields = array('id', 'name', 'page_html', 'state', 'page_url');

    	/**
	     * Primary key fields
	     *
	     * @var array
	     */
    	protected $primary_key = array('id');

    	/**
	     * Return name of this model
	     *
	     * @param boolean $underscore
	     * @param boolean $singular
	     * @return string
	     */
	    function getModelName($underscore = false, $singular = false) {
			if($singular) {
				return $underscore ? 'repsite_page' : 'RepsitePage';
			} else {
				return $underscore ? 'repsite_pages' : 'RepsitePages';
			} // if
	    } // getModelName

	    /**
	     * Name of AI field (if any)
	     *
	     * @var string
	     */
    	protected $auto_increment = 'id';


		// ---------------------------------------------------
	    //  Fields
	    // ---------------------------------------------------

    	/**
	     * Return value of id field
	     *
	     * @return integer
	     */
	    function getId() {
	      	return $this->getFieldValue('id');
	    } // getId
    
	    /**
	     * Set value of id field
	     *
	     * @param integer $value
	     * @return integer
	     */
		function setId($value) {
			return $this->setFieldValue('id', $value);
		} // setId
    	

    	function getName() {
    		return $this->getFieldValue('name');
    	}

    	function setName() {
    		return $this->setFieldValue('name', $value);
    	}


    	function getPageUrl() {
    		return $this->getFieldValue('page_url');
    	}

    	function setPageUrl() {
    		return $this->setFieldValue('page_url', $value);
    	}

    	 /**
	     * Return value of state field
	     *
	     * @return integer
	     */
	    function getState() {
	      return $this->getFieldValue('state');
	    } // getState
	    
	    /**
	     * Set value of state field
	     *
	     * @param integer $value
	     * @return integer
	     */
	    function setState($value) {
	      return $this->setFieldValue('state', $value);
	    } // setState

    	

    	function getPageHtml() {
    		return $this->getFieldValue('page_html');
    	}

    	function setPageHtml($value) {
    		return $this->setFieldValue('page_html', $value);
    	}


    	/**
	     * Set value of specific field
	     *
	     * @param string $name
	     * @param mixed $value
	     * @return mixed
	     * @throws InvalidParamError
	     */
	    function setFieldValue($name, $value) {
			$real_name = $this->realFieldName($name);

			if($value === null) {
				return parent::setFieldValue($real_name, null);
			} else {
				switch($real_name) {
					case 'id':
						return parent::setFieldValue($real_name, (integer) $value);
					case 'name':
						return parent::setFieldValue($real_name, (string) $value);
					case 'page_html':
						return parent::setFieldValue($real_name, (string) $value);
					case 'state':
            			return parent::setFieldValue($real_name, (integer) $value);
            		case 'page_url':
            			return parent::setFieldValue($real_name, (string) $value);

				} // switch

				throw new InvalidParamError('name', $name, "Field $name (maps to $real_name) does not exist in this table");
			} // if
	    } // setFieldValue


  	}