<?php

  /**
   * Framework level language model implementation
   *
   * @package angie.frameworks.globalization
   * @subpackage models
   */
  abstract class FwLanguage extends BaseLanguage implements IRoutingContext {
  	
  	/**
  	 * Constants
  	 */
  	const DICTIONARY_SERVERSIDE = 'serverside';
  	const DICTIONARY_CLIENTSIDE = 'clientside';
  	
    /**
     * Return locale code without UTF-8 suffix
     * 
     * @return string
     */
    function getLocaleCode() {
      return Languages::getLocaleCode($this->getLocale());
    }//getLocaleCode

  	/**
  	 * Prepare language for translation
  	 * 
  	 * @param String $starting_letter
  	 * @return DBResult
  	 */
  	function prepareForTranslation($starting_letter = null) {
  		$phrases_table = TABLE_PREFIX . 'language_phrases';
  		$translations_table = TABLE_PREFIX . 'language_phrase_translations';
  		$temp_table = 'temp_table';
  		
  		$query = DB::prepare("SELECT DISTINCT($phrases_table.hash), $phrases_table.phrase, $temp_table.translation FROM $phrases_table LEFT JOIN (SELECT * FROM $translations_table WHERE language_id = ?) AS $temp_table ON $phrases_table.hash = $temp_table.phrase_hash", $this->getId());
  		
  		if ($starting_letter && in_array($starting_letter, Globalization::getAlphabet())) {
  			$query.= " WHERE $phrases_table.phrase LIKE '" . $starting_letter . "%'"; 
  		} else if ($starting_letter && !in_array($starting_letter, Globalization::getAlphabet())) {
  			$query.= " WHERE $phrases_table.phrase NOT REGEXP '^[a-z]'";
  		} // if
  		
  		return DB::execute($query);
  	} // prepareForTranslation
  	
  	/**
  	 * Get language translation
  	 * 
  	 * @param string $dictionary
  	 * @return array
  	 */
  	function getTranslation($dictionary) {
      if($dictionary != self::DICTIONARY_CLIENTSIDE && $dictionary != self::DICTIONARY_SERVERSIDE) {
        $dictionary = 'combined';
      } // if

      $language_id = $this->getId();

  		return AngieApplication::cache()->getByObject($this, array('translations', $dictionary), function() use ($language_id, $dictionary) {
        $phrases_table = TABLE_PREFIX . 'language_phrases';
        $translations_table = TABLE_PREFIX . 'language_phrase_translations';

        $query = "SELECT DISTINCT($phrases_table.hash), $phrases_table.phrase, $translations_table.translation FROM $phrases_table LEFT JOIN $translations_table ON $phrases_table.hash = $translations_table.phrase_hash WHERE $translations_table.language_id = ? AND $translations_table.translation IS NOT NULL";

        // filter by dictionary
        if ($dictionary == Language::DICTIONARY_CLIENTSIDE) {
          $query .= ' AND is_clientside = 1';
        } else if ($dictionary == Language::DICTIONARY_SERVERSIDE) {
          $query .= ' AND is_serverside = 1';
        } // if

        $result = DB::execute($query, $language_id);

        if($result) {
          $translation = array();

          foreach ($result as $single_result) {
            $translation[$single_result['phrase']] = $single_result['translation'];
          } // foreach

          return $translation;
        } else {
          return array();
        } // if
      });
  	} // getTranslation
  	
  	/**
  	 * Set translation for this language overwriting existing ones
  	 * 
  	 * @param $translations
     * @param bool $phrase_is_hash
     * @return bool|DbResult
  	 */
    function setTranslation($translations, $phrase_is_hash = false) {
  		if (!is_foreachable($translations)) {
  			return false;
  		} // if
  		
  		$query = array();
  		foreach ($translations as $phrase => $translation) {
        if ($translation) {
	  			if ($phrase_is_hash) {
	  				$query[] = DB::prepare('(?, ?, ?)', $this->getId(), $phrase, HtmlPurifierForAngie::purify($translation));
	  			} else {
	  				$query[] = DB::prepare('(?, md5(?), ?)', $this->getId(), $phrase, HtmlPurifierForAngie::purify($translation));
	  			} // if
  			} // if
  		} // foreach
  		
  		if (!count($query)) {
  			return false;
  		} // if
  		
			// save translations
			$result = DB::execute('REPLACE INTO ' . TABLE_PREFIX .'language_phrase_translations (language_id, phrase_hash, translation) VALUES ' . implode(', ', $query));

  		// remove cache for this language
      AngieApplication::cache()->removeByObject($this, 'translations');
			
			$this->setLastUpdatedOn(new DateTimeValue());
			$this->save();
  		
  		return $result;
  	} // setTranslation

    /**
     * Unset translation
     *
     * @param string $phrase
     * @param boolean $phrase_is_hash
     */
    function unsetTranslation($phrase, $phrase_is_hash = false) {
      DB::execute('DELETE FROM ' . TABLE_PREFIX . 'language_phrase_translations WHERE language_id = ? AND phrase_hash = ?', $this->getId(), ($phrase_is_hash ? $phrase : md5($phrase)));
    } // unsetTranslation
    
    /**
     * Set value of specific field
     *
     * @param string $name
     * @param mixed $value
     * @return mixed
     */
    function setFieldValue($name, $value) {
      if($name == 'locale' && $value && !str_ends_with(strtolower($value), 'utf-8')) {
        $value = "{$value}.UTF-8"; // Make sure that we include charset in locale
      } // if
      
      return parent::setFieldValue($name, $value);
    } // setFieldValue
    
    /**
     * Return array or property => value pairs that describes this object
     *
     * $user is an instance of user who requested description - it's used to get
     * only the data this user can see
     *
     * @param IUser $user
     * @param boolean $detailed
     * @param boolean $for_interface
     * @return array
     */
    function describe(IUser $user, $detailed = false, $for_interface = false) {
      $result = parent::describe($user, $detailed, $for_interface);
      
      $result['is_default'] = $this->isDefault();
      $result['locale'] = $this->getLocale();
      $result['decimal_separator'] = $this->getDecimalSeparator();
      $result['thousands_separator'] = $this->getThousandsSeparator();
      $result['urls']['export'] = $this->getExportUrl();
      
      return $result;
    } // describe

    /**
     * Return array or property => value pairs that describes this object
     *
     * $user is an instance of user who requested description - it's used to get
     * only the data this user can see
     *
     * @param IUser $user
     * @param boolean $detailed
     * @return array
     */
    function describeForApi(IUser $user, $detailed = false) {
      return array(
        'id' => $this->getId(),
        'name' => $this->getName(),
        'locale' => $this->getLocale(),
      );
    } // describeForApi
    
    /**
     * Prepare list of options that $user can use
     *
     * @param IUser $user
     * @param NamedList $options
     * @param string $interface
     * @return NamedList
     */
    protected function prepareOptionsFor(IUser $user, NamedList $options, $interface = AngieApplication::INTERFACE_DEFAULT) {
      $options->add('export_language', array(
        'text' => lang('Export'), 
        'url' => $this->getExportUrl(), 
        'onclick' => new TargetBlankCallback() 
      ));

      $options->add('update_language', array(
        'text' => lang('Update'),
        'url' => $this->getUpdateUrl(),
        'onclick' => new FlyoutFormCallback()
      ));
            
      if($this->canDelete($user)) {
        $options->add('delete_language', array(
          'text' => 'Delete', 
          'url' => $this->getDeleteUrl(), 
          'icon' => '',
          'onclick' => new AsyncLinkCallback(array(
          	'confirmation' => lang('Are you sure that you want to delete this language?'), 
          	'success_event' => 'language_deleted', 
            'success_message' => lang(':name language has been deleted', array('name' => $this->getName()))
          )), 
       ));
      } // if
       
      parent::prepareOptionsFor($user, $options, $interface);
    } // prepareOptionsFor
    
    /**
     * Returns true if this language is default
     *
     * @return boolean
     */
    function isDefault() {
      return $this->getId() == ConfigOptions::getValue('language');
    } // isDefault
    
    /**
     * Returns true if this locale is built in the code
     *
     * @return boolean
     */
    function isBuiltIn() {
    	return $this->getLocale() == BUILT_IN_LOCALE;
    } // isBuiltIn
    
    // ---------------------------------------------------
    //  Interface implementation
    // ---------------------------------------------------
    
    /**
     * Return routing context name
     *
     * @return string
     */
    function getRoutingContext() {
      return 'language';
    } // getRoutingContext
    
    /**
     * Return routing context parameters
     *
     * @return mixed
     */
    function getRoutingContextParams() {
      return array('language_id' => $this->getId());
    } // getRoutingContextParams
    
    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------

    /**
     * Returns true if $user can edit this language
     *
     * @param User $user
     * @return boolean
     */
    function canEdit(User $user) {
      if($this->isBuiltIn()) {
        return false;
      } // if

      return $user->isAdministrator();
    } // canDelete
    
    /**
     * Returns true if $user can delete this language
     *
     * @param User $user
     * @return boolean
     */
    function canDelete(User $user) {
      if($this->isBuiltIn() || $this->isDefault()) {
        return false;
      } // if
      
      return $user->isAdministrator();
    } // canDelete
    
    // ---------------------------------------------------
    //  URL-s
    // ---------------------------------------------------
    
    /**
     * Return view language URL
     *
     * @return string
     */
    function getViewUrl() {
    	return Router::assemble('admin_language', array(
    	  'language_id' => $this->getId(),
    	));
    } // getViewUrl
    
    /**
     * Return export language URL
     *
     * @return string
     */
    function getExportUrl() {
    	return Router::assemble('admin_language_export', array(
    	  'language_id' => $this->getId(),
    	));
    } // getViewUrl

    /**
     * Return update language URL
     *
     * @return string
     */
    function getUpdateUrl() {
      return Router::assemble('admin_language_update', array(
        'language_id' => $this->getId(),
      ));
    } // getViewUrl
    
    /**
     * Return edit language URL
     *
     * @return string
     */
    function getEditUrl() {
    	return Router::assemble('admin_language_edit', array(
    	  'language_id' => $this->getId(),
    	));
    } // getEditUrl
    
    /**
     * Return view language URL
     *
     * @return string
     */
    function getDeleteUrl() {
    	return Router::assemble('admin_language_delete', array('language_id' => $this->getId(),));
    } // getDeleteUrl    
    
    /**
     * Return edit translation file URL
     *
     * @param string $filename
     * @return string
     */
    function getEditTranslationUrl($filename) {
    	return Router::assemble('admin_language_edit_translation', array(
    	  'language_id' => $this->getId(),
    	));
    } // getEditTranslationFileUrl
    
    // ---------------------------------------------------
    //  System
    // ---------------------------------------------------
    
    /**
     * Validate before save
     *
     * @param ValidationErrors $errors
     */
    function validate(ValidationErrors &$errors) {
      if($this->validatePresenceOf('name')) {
        if(!$this->validateUniquenessOf('name')) {
      	  $errors->addError(lang('Language name needs to be unique'), 'name');
      	} // if
      } else {
        $errors->addError(lang('Language name is required'), 'name');
      } // if

    	if($this->validatePresenceOf('locale')) {
    	  if(strtolower($this->getLocale()) == 'en_us.utf-8') {
    	    $errors->addError(lang('en_US.UTF-8 locale is reserved by the system'), 'locale');
    	  } // if
        if (!$this->validateUniquenessOf('locale')) {
          $errors->addError(lang('This language locale already exists in the system'));
        } //if
    	} else {
    	  $errors->addError(lang('Language locale is required'), 'locale');
    	} // if
    } // validate
    
    /**
     * Removes object and files from filesystem
     */
    function delete() {
      try {
        DB::beginWork('Removing language @ ' . __CLASS__);
        
        DB::execute('DELETE FROM ' . TABLE_PREFIX . 'language_phrase_translations WHERE language_id = ?',$this->getId());
        ConfigOptions::removeByValue('language', $this->getId());

        AngieApplication::cache()->removeByObject($this, 'translations');

        parent::delete();
        
        DB::commit('Language removed @ ' . __CLASS__);
      } catch(Exception $e) {
        DB::rollback('Failed to remove language @ ' . __CLASS__);
        throw $e;
      } // try
    } // deleted
    
  }