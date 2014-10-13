<?php

  /**
   * Parent created activity log callback
   * 
   * @package angie.frameworks.activity_logs
   * @subpackage models
   */
  class ParentActivityLogCallback extends JavaScriptCallback {
    
    /**
     * With target lang
     *
     * @var string
     */
    private $with_target_lang = '';
    
    /**
     * Without target langu
     *
     * @var string
     */
    private $without_target_lang = '';

    /**
     * With target lang value for RSS subject
     *
     * @var string
     */
    private $rss_subject_with_target_lang = '';

    /**
     * Without target lang for RSS subject
     *
     * @var string
     */
    private $rss_subject_without_target_lang = '';
    
    /**
     * Action name
     * 
     * @var string
     */
    private $action_name_lang = '';
    
    /**
     * Author lang
     * 
     * @var string
     */
    private $author_lang = '';
  
    /**
     * Render callback definition
     * 
     * @return string
     */
    function render() {
    	$interface = AngieApplication::getPreferedInterface();
    	
    	if(!$this->author_lang && $this->author_lang !== false) {
    		$interface == AngieApplication::INTERFACE_DEFAULT ? $this->setAuthorLang(lang('by <a href=":user_url">:user_name</a>')) : $this->setAuthorLang(lang(':user_name'));
    	} // if
    	
    	$cell_element = ($interface == AngieApplication::INTERFACE_DEFAULT || $interface == AngieApplication::INTERFACE_PRINTER) ? 'td' : 'span';
    	
    	$data = array(
    		'with_target' => $this->with_target_lang,
    		'without_target' => $this->without_target_lang,
    		'action_name' => $this->action_name_lang,
    		'author' => $this->author_lang,
    	);
    	
    	return '(function (entry, author, parent, target, interface) {
    		var row = $(this);
			  var data = ' . JSON::encode($data) . ';
			  var lang_params = {
			    "user_name" : author.display_name, 
			    "user_url" : author.urls.view, 
			    "type_lowercase" : parent.verbose_type_lowercase, 
			    "type" : parent.verbose_type, 
			    "name_short" : App.excerpt(parent.name, 60), 
			    "name" : parent.name, 
			    "url" : parent.urls.view
			  };
			  
			  // Target params
			  if(typeof(target) == "object" && target) {
    			lang_params["target_type_lowercase"] = target.verbose_type_lowercase;
    			lang_params["target_type"] = target.verbose_type;
    			lang_params["target_name_short"] = App.excerpt(target.name, 60);
    			lang_params["target_name"] = target.name;
    			lang_params["target_url"] = target.urls.view;
    		} // if
			
			  // Action name
			  if (data.action_name) {
			    var action_cell = row.find("td.action");
			    if (action_cell.length) {
			      action_cell.html(data.action_name);
			    }; // if
			  }; // if
			  
			  // Subject
		    var subject_cell = row.find("' . $cell_element . '.subject");
		    if (subject_cell.length) {
		    	if(typeof(target) == "object" && target) {
		    		subject_cell.html(App.lang(data.with_target, lang_params));
		    	} else {
		    		subject_cell.html(App.lang(data.without_target, lang_params));
		    	} // if
		    }; // if
			  
			  // Author
			  if (data.author) {
			    var author_cell = row.find("' . $cell_element . '.author");
			    if (author_cell.length) {
			      author_cell.html(App.lang(data.author, lang_params));
			    }; // if
			  }; // if
    	})';
    } // render

    /**
     * Render RSS title
     *
     * $author can be an instance of IUser class or described user object
     *
     * @param array $entry
     * @param mixed $author
     * @param $parent
     * @param $target
     * @return string
     */
    function renderRssSubject($entry, $author, $parent, $target = null) {
      $lang_params = array(
        'user_name' => $author instanceof IUser ? $author->getDisplayName(true) : $author['display_name'],
        'user_url' => $author instanceof Iuser ? $author->getViewUrl() : $author['urls']['view'],
        'type_lowercase' => $parent['verbose_type_lowercase'],
        'type' => $parent['verbose_type'],
        'name_short' => substr_utf($parent['name'], 0, 60),
        'name' => $parent['name'],
        'url' => $parent['urls']['short'],
      );

      if($target) {
        $lang_params['target_type_lowercase'] = $target['verbose_type_lowercase'];
        $lang_params['target_type'] = $target['verbose_type'];
        $lang_params['target_name_short'] = substr_utf($target['name'], 0, 60);
        $lang_params['target_name'] = $target['name'];
        $lang_params['target_url'] = $target['urls']['view'];

        if(empty($this->rss_subject_with_target_lang)) {
          return $entry['action'];
        } // if

        return Globalization::lang($this->rss_subject_with_target_lang, $lang_params);
      } else {
        if(empty($this->rss_subject_without_target_lang)) {
          return $entry['action'];
        } // if

        return Globalization::lang($this->rss_subject_without_target_lang, $lang_params);
      } // if
    } // renderRssSubject
    
    // ---------------------------------------------------
    //  Internal, language settings
    // ---------------------------------------------------
    
    /**
     * Set both with and without target lang value
     * 
     * @param string $value
     */
    function setLang($value) {
      $this->with_target_lang = $this->without_target_lang = $value;
    } // setLang
    
    /**
     * Set with target lang
     * 
     * @param string $value
     */
    function setWithTargetLang($value) {
      $this->with_target_lang = $value;
    } // setWithTargetLang
    
    /**
     * Set without target lang
     * 
     * @param string $value
     */
    function setWithoutTargetLang($value) {
      $this->without_target_lang = $value;
    } // setWithoutTargetLang

    /**
     * Set both with and without target value at once
     *
     * @param string $value
     */
    function setRssSubject($value) {
      $this->rss_subject_with_target_lang = $this->rss_subject_without_target_lang = $value;
    } // setRssSubject

    /**
     * Set RSS subject with target lang value
     *
     * @param string $value
     */
    function setRssSubjectWithTarget($value) {
      $this->rss_subject_with_target_lang = $value;
    } // setRssSubjectWithTarget

    /**
     * Set RSS without target lang value
     *
     * @param string $value
     */
    function setRssSubjectWithoutTarget($value) {
      $this->rss_subject_without_target_lang = $value;
    } // setRssSubjectWithoutTarget
    
    /**
     * Set the action name
     * 
     * @param string $action_name
     */
    function setActionNameLang($action_name) {
   		$this->action_name_lang = $action_name;
    } // setActionNameLang
    
    /**
     * set the author string
     * 
     * @param string $author
     */
    function setAuthorLang($author) {
    	$this->author_lang = $author;
    } // setAuthorLang
    
  }