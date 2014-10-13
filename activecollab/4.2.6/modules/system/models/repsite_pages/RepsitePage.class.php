<?php

	/**
	   * RepsitePage class
	   *
	   * @package activeCollab.modules.system
	   * @subpackage model
	   */
  	class RepsitePage extends BaseRepsitePage {


  		function validate(ValidationErrors &$errors) {
        if($this->validatePresenceOf('name')) {
          if($this->isNew()) {
            $in_use = RepsitePages::isPageNameInUse($this->getName());
          } else {
            $in_use = RepsitePages::isPageNameInUse($this->getName(), $this->getId());
          } // if

          if($in_use) {
            $errors->addError(lang('Page name needs to be unique'), 'name');
          } // if
        } else {
          $errors->addError(lang('Page name is required'), 'name');
        } // if
        
        /*if($this->getIsOwner() && $this->getState() < STATE_VISIBLE) {
          $errors->addError(lang("Owner company can't be archived, trashed or deleted"));
        } // if*/

      } // validate

  		function save() {
  			$pagename_changed = $this->isModifiedField('name');

  			$save = parent::save();

  			if($pagename_changed){

  			}

  			return $save;
  		}

      function getViewUrl() {
        return Router::assemble('repsite_admin');
      } // getViewUrl


  	}