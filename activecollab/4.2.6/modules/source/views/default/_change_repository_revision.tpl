<div id="{$_change_repository_revision_id}" class="change_revision">
  <input type="text" value="{$revision_number}" /><button type="button"><span>{lang}Change revision{/lang}</span></button>
  <div id="change_revision_status"></div>
</div>
  
<script type="text/javascript">
	var change_revision_form = $('#{$_change_repository_revision_id nofilter}');
	var change_revision_button = change_revision_form.find('button');
	var change_revision_field = change_revision_form.find('input[type="text"]');
    var change_revision_repository_type = '{$_change_repository_revision_repository_type}';

  {literal}
		var change_revision = function (revision_number, browse_url, check_revision_url) {
		  change_revision_form.block();
	
	    if (change_revision_repository_type == 'SvnRepository') {
	      if (revision_number != 'head' && (((revision_number!=parseInt(revision_number-0,10)) || revision_number <= 0))) {
          change_revision_form.unblock();
	        alert(App.lang('Revision number must be a positive number!'));
	        change_revision_field.focus();
	      } else {
	        window.location = App.extendUrl(browse_url, {r : revision_number});
	      } // if
	    } else {
	      if (revision_number != 'head' && /\W/.test(revision_number) ) {
	        alert(App.lang('Revision name must be alphanumeric string!'));
	      } else {
	        if (revision_number === 'head') {
	          window.location = App.extendUrl(browse_url, {r : revision_number});
	        } // if
	
	        $.ajax({
	          url : App.extendUrl(check_revision_url, {revision_name : revision_number}),
	          success : function (response) {
	            if ($.trim(response) === 'false') {
                change_revision_form.unblock();
                alert(App.lang('This revision does not exist. Please try again.'));
      	        change_revision_field.focus();
	            } else {
	              window.location = App.extendUrl(browse_url, {r : response});    
	            } // if
	          },
            error : function () {
              change_revision_form.unblock();
              alert(App.lang('There has been an error. Please try again or contact an administrator.'));
    	        change_revision_field.focus();
            }
	        });
	      } // if
	    } // if
		};
	{/literal}

  change_revision_button.click(function() {
    change_revision(change_revision_field.val(), '{$_change_repository_revision_url nofilter}', '{$_change_repository_revision_test_url nofilter}');
  });

  change_revision_field.keypress(function(e) {
    if (e.which == 13) {
      change_revision(change_revision_field.val(), '{$_change_repository_revision_url nofilter}', '{$_change_repository_revision_test_url nofilter}');
    }
  });

  change_revision_field.focus(function () {
    if (change_revision_field.val().toLowerCase() == 'head') {
      change_revision_field.attr('value', '');
    } // if
  })
  
  change_revision_field.blur(function () {
    if (change_revision_field.val() == '') {
      change_revision_field.attr('value', 'head');
    } // if
  })
</script>