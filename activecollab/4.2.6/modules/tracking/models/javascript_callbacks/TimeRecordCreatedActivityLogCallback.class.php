<?php

  /**
   * Time record created activity log
   * 
   * @package activeCollab.modules.tracking
   * @subpackage models
   */
  class TimeRecordCreatedActivityLogCallback extends JavaScriptCallback {
  
    /**
     * Render callback definition
     * 
     * @return string
     */
    function render() {      
    	$interface = AngieApplication::getPreferedInterface();
    	
    	$cell_element = $interface == AngieApplication::INTERFACE_DEFAULT ? 'td' : 'span';

      // Make sure that these phrases are captured by lang index extractor and added to client side index:
      // App.lang('<strong>One</strong> billable hour submitted to <a href=":target_url">:target_name</a> :target_type_lowercase');
      // App.lang('<strong>:hours</strong> billable hours submitted to <a href=":target_url">:target_name</a> :target_type_lowercase');
      // App.lang('<strong>One</strong> non billable hour submitted to <a href=":target_url">:target_name</a> :target_type_lowercase');
      // App.lang('<strong>:hours</strong> non billable hours submitted to <a href=":target_url">:target_name</a> :target_type_lowercase');
      // App.lang('by <a href=":user_url">:user_name</a>');

      $one_billable_hour_with_target_lang = '<strong>One</strong> billable hour submitted to <a href=":target_url">:target_name</a> :target_type_lowercase';
      $billable_hours_with_target_lang = '<strong>:hours</strong> billable hours submitted to <a href=":target_url">:target_name</a> :target_type_lowercase';

      $one_non_billable_hour_with_target_lang = '<strong>One</strong> non billable hour submitted to <a href=":target_url">:target_name</a> :target_type_lowercase';
      $non_billable_hours_with_target_lang = '<strong>:hours</strong> non billable hours submitted to <a href=":target_url">:target_name</a> :target_type_lowercase';

      $by_user_lang = 'by <a href=":user_url">:user_name</a>';

    	return '(function (entry, author, parent, target, interface) {
      	var row = $(this);
      	
      	if(interface == "'.AngieApplication::INTERFACE_DEFAULT.'") {
      		if(parent.billable_status >= 1) {
	        	if(parent.value == 1) {
	    				var subject = ' . JSON::encode($one_billable_hour_with_target_lang) .';
	    			} else {
	    				var subject = ' . JSON::encode($billable_hours_with_target_lang) . ';
	    			} // if
	    		} else {
	    			if(parent.value == 1) {
	    				var subject = ' . JSON::encode($one_non_billable_hour_with_target_lang) . ';
	    			} else {
	    				var subject = ' . JSON::encode($non_billable_hours_with_target_lang) . ';
	    			} // if
	    		} // if
      	} else if(interface == "'.AngieApplication::INTERFACE_PHONE.'") {
      		if(parent.billable_status >= 1) {
	        	if(parent.value == 1) {
	    				var subject = App.lang("<strong>One</strong> billable hour submitted to :target_name :target_type_lowercase");
	    			} else {
	    				var subject = App.lang("<strong>:hours</strong> billable hours submitted to :target_name :target_type_lowercase");
	    			} // if
	    		} else {
	    			if(parent.value == 1) {
	    				var subject = App.lang("<strong>One</strong> non billable hour submitted to :target_name :target_type_lowercase");
	    			} else {
	    				var subject = App.lang("<strong>:hours</strong> non billable hours submitted to :target_name :target_type_lowercase");
	    			} // if
	    		} // if
      	} else if(interface == "'.AngieApplication::INTERFACE_PRINTER.'") {
      		if(parent.billable_status >= 1) {
	        	if(parent.value == 1) {
	    				var subject = App.lang("<strong>One</strong> billable hour submitted to :target_name :target_type_lowercase");
	    			} else {
	    				var subject = App.lang("<strong>:hours</strong> billable hours submitted to :target_name :target_type_lowercase");
	    			} // if
	    		} else {
	    			if(parent.value == 1) {
	    				var subject = App.lang("<strong>One</strong> non billable hour submitted to :target_name :target_type_lowercase");
	    			} else {
	    				var subject = App.lang("<strong>:hours</strong> non billable hours submitted to :target_name :target_type_lowercase");
	    			} // if
	    		} // if
      	} // if
      
		    var action_cell = row.find("td.action");
		    if (action_cell.length) {
		      action_cell.html(App.lang("Submitted"));
		    }; // if
			  
			  var subject_cell = row.find("' . $cell_element . '.subject");
			  if (subject_cell.length) {
	      	subject_cell.html(App.lang(subject, {
	      		"hours" : App.numberFormat(parent.value),
		    		"target_name" : typeof(target) == "object" && target ? target.name : "", 
		    		"target_url" : typeof(target) == "object" && target ? target.urls.view : "",
						"target_type_lowercase" : typeof(target) == "object" && target ? target.verbose_type_lowercase : "",
						"target_type" : typeof(target) == "object" && target ? target.verbose_type : "",
    			}));
			  }; // if
			  
				var author_cell = row.find("' . $cell_element . '.author");
			  if (author_cell.length) {
			  	if(interface == "'.AngieApplication::INTERFACE_DEFAULT.'") {
			  		author_cell.html(App.lang(' . JSON::encode($by_user_lang) . ', {
				    	"user_url" : author.urls.view,
				      "user_name" : author.display_name
	    			}));
			  	} else if(interface == "'.AngieApplication::INTERFACE_PHONE.'") {
			  		author_cell.html(author.display_name);
			  	} else if(interface == "'.AngieApplication::INTERFACE_PRINTER.'") {
			  		author_cell.html(author.display_name);
			  	} // if
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
     * @param array $parent
     * @param array $target
     * @return string
     */
    function renderRssSubject($entry, $author, $parent, $target = null) {
      if($parent['value'] == 1) {
        if($parent['billable_status'] == BILLABLE_STATUS_NOT_BILLABLE) {
          return lang('One non billable hour submitted to ":target_name" :target_type_lowercase', array(
            'target_name' => $target ? $target['name'] : null,
            'target_type_lowercase' => $target ? $target['verbose_type_lowercase'] : null,
          ));
        } else {
          return lang('One billable hour submitted to ":target_name" :target_type_lowercase', array(
            'target_name' => $target ? $target['name'] : null,
            'target_type_lowercase' => $target ? $target['verbose_type_lowercase'] : null,
          ));
        } // if
      } else {
        if($parent['billable_status'] == BILLABLE_STATUS_NOT_BILLABLE) {
          return lang(':value non billable hours submitted to ":target_name" :target_type_lowercase', array(
            'value' => $parent['value'],
            'target_name' => $target ? $target['name'] : null,
            'target_type_lowercase' => $target ? $target['verbose_type_lowercase'] : null,
          ));
        } else {
          return lang(':value billable hours submitted to ":target_name" :target_type_lowercase', array(
            'value' => $parent['value'],
            'target_name' => $target ? $target['name'] : null,
            'target_type_lowercase' => $target ? $target['verbose_type_lowercase'] : null,
          ));
        } // if
      } // if
    } // renderRssSubject
    
  }