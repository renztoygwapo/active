<?php

  /**
   * Comment created activity log callback
   * 
   * @package angie.frameworks.comments
   * @subpackage models
   */
  class CommentCreatedActivityLogCallback extends JavaScriptCallback {
  
    /**
     * Render callback definition
     *
     * @return string
     */
    function render() {
    	$interface = AngieApplication::getPreferedInterface();
    	
    	$cell_element = $interface == AngieApplication::INTERFACE_DEFAULT ? 'td' : 'span';

      // Make sure that lang index captures these strings and puts it in client side index:
      // App.lang('Comment posted on <a href=":target_url">":target_name"</a> :target_type_lowercase');
      // App.lang('by <a href=":user_url">:user_name</a>');

      $comment_posted_on = $interface == AngieApplication::INTERFACE_DEFAULT ?
      	'Comment posted on <a href=":target_url">":target_name"</a> :target_type_lowercase' :
      	'Comment posted on ":target_name" :target_type_lowercase';
      	
      $by_user_lang = 'by <a href=":user_url">:user_name</a>';

    	return '(function (entry, author, parent, target, interface) {
      	var row = $(this);
      	
      	if(typeof(target) == "object" && target) {
      		if(interface == "'.AngieApplication::INTERFACE_DEFAULT.'") {
			  		var subject = App.lang(' . JSON::encode($comment_posted_on) . ', {
	      			"target_type_lowercase" : target.verbose_type_lowercase, 
	      			"target_type" : target.verbose_type, 
	      			"target_name" : target.name, 
	      			"target_url" : target.urls.view
	      		});
			  	} else if(interface == "'.AngieApplication::INTERFACE_PHONE.'") {
			  		var subject = App.lang(' . JSON::encode($comment_posted_on) . ', {
	      			"target_type_lowercase" : target.verbose_type_lowercase, 
	      			"target_type" : target.verbose_type, 
	      			"target_name" : target.name, 
	      			"target_url" : target.urls.view
	      		});
			  	} else if(interface == "'.AngieApplication::INTERFACE_PRINTER.'") {
			  		var subject = App.lang(' . JSON::encode($comment_posted_on) . ', {
	      			"target_type_lowercase" : target.verbose_type_lowercase, 
	      			"target_type" : target.verbose_type, 
	      			"target_name" : target.name, 
	      			"target_url" : target.urls.view
	      		});
			  	}
      	} else {
      		var subject = App.lang("Comment posted");
      	} // if
      	
		    var action_cell = row.find("td.action");
		    if (action_cell.length) {
		      action_cell.html(App.lang("Posted"));
		    }; // if
			  
			  var subject_cell = row.find("' . $cell_element . '.subject");
			  if (subject_cell.length) {
	      	subject_cell.html(subject);
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
     * @param ApplicationObject $parent
     * @param ApplicationObject $target
     * @return string
     */
    function renderRssSubject($entry, $author, ApplicationObject $parent, $target = null) {
      return lang('Comment posted on ":target_name" :target_type_lowercase', array(
        'target_name' => $target ? $target['name'] : null,
        'target_type_lowercase' => $target ? $target['verbose_type_lowercase'] : null,
      ));
    } // renderRssSubject
    
  }