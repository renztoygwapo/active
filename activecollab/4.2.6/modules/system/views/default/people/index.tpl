{title}People{/title}
{add_bread_crumb}Everyone{/add_bread_crumb}
{use_widget name="objects_list" module="environment"}
{use_widget name="form" module="environment"}

<div id="companies">
  <div class="empty_content">
      <div class="objects_list_title">{lang}People{/lang}</div>
      <div class="objects_list_icon"><img src="{image_url name='icons/48x48/companies.png' module=$smarty.const.SYSTEM_MODULE}" alt=""/></div>
      <div class="objects_list_details_actions">
          <ul>
          {if $can_add_company}
            <li><a href="{assemble route='people_invite'}" id="invite_people">{lang}Invite People{/lang}</a></li>
            <li><a href="{assemble route='people_companies_add'}" id="new_company">{lang}New Company{/lang}</a></li>
          {/if}
          {if $can_import_vcard}<li><a href="#" id="import_vcard" title="Import vCard">{lang}Import vCard{/lang}</a></li>{/if}
          </ul>
      </div>
      
      <div class="upload_to_flyout import_vcard">
      	{form action=Router::assemble('people_import_vcard') method='post' enctype='multipart/form-data' class="import_vcard_form"}
      	  {wrap field=vcard}
      	  	{file_field name="vcard" label="Upload vCard"}
      	  {/wrap}
      	  
      	  {submit}Upload vCard{/submit} 
  			{/form}
			</div>
			
      <div class="object_lists_details_tips">
        <h3>{lang}Tips{/lang}:</h3>
        <ul>
          <li>{lang}To select a company and load its details, please click on it in the list on the left{/lang}</li>
        </ul>
      </div>  
  </div>
</div>

<script type="text/javascript">
  $('#companies').each(function() {
    var wrapper = $(this);

    $('#invite_people').flyoutForm({
      'title' : App.lang('Invite People'),
      'success_message' : App.lang('People are invited'),
      'success_event' : 'people_invited',
      'width' : 950
    });

    $('#new_company').flyoutForm({
      'title' : App.lang('New Company'),
      'success_event' : 'company_created',
      'width' : 600
    });

    var print_url = '{assemble route=people print=1}';  

    var items = {$users|json nofilter};
    var companies_map = {$companies_map|map nofilter};
    
    var mass_edit_url = '{assemble route=people_mass_edit}';
    
    wrapper.objectsList({
      'id' : 'people',
      'items' : items,      
      'objects_type' : 'users',
      'show_items_count' : false,
      'enable_group_collapsing' : false,
      'print_url' : print_url,
      'required_fileds'   : ['id', 'company_id', 'name', 'avatar', 'email', 'is_archived', 'permalink'],
      'requirements' : {
        'is_archived' : 0,
      },
      'events' : App.standardObjectsListEvents(),
      'multi_url' : mass_edit_url,
      'multi_actions' : {$mass_manager|json nofilter},
      'prepare_item' : function (item) {
        return {
          'id' : item.id,
          'company_id' : item.company && typeof(item.company) == 'object' ? item.company.id : item.company_id,
          'name' : item.display_name,
          'avatar' : item.avatar,
          'email' : item.email,
          'is_archived' : item.state == 2 ? 1 : 0,
          'permalink' : item.permalink
        }
      },
      'render_item' : function (item) {
        return '<td class="avatar icon"><img src="' + item['avatar']['small'] + '" alt="" /></td><td class="name">' + App.clean(item['name']) + '</td>';
      },

      'search_index' : function (item) {
        return App.clean(item['name']);
      },

      'filtering' : [{
         'label'          : App.lang('Status'),
         'property'       : 'is_archived',
         'values'         : [{ 
            'label' : App.lang('All Companies'), 
            'value' : '', 
            'icon' : App.Wireframe.Utils.imageUrl('objects-list/active-and-completed.png', 'complete'), 
            'breadcrumbs' : App.lang('Everyone')
          }, { 
            'label' : App.lang('Active Companies'), 
            'value' : '0', 
            'icon' : App.Wireframe.Utils.imageUrl('objects-list/active.png', 'complete'), 
            'default' : true, 
            'breadcrumbs' : App.lang('Active Companies') 
          }, { 
            'label' : App.lang('Archived Companies'), 
            'value' : '1', 
            'icon' : App.Wireframe.Utils.imageUrl('objects-list/completed.png', 'complete'), 
            'breadcrumbs' : App.lang('Archived Companies')
          }]
      }],

      'grouping' : [{
        'label' : App.lang('By Company'),
        'property' : 'company_id',
        'map' : companies_map,
        'icon' : App.Wireframe.Utils.imageUrl('objects-list/group-by-category.png', 'categories'),
        'filters' : ['is_archived'],
        'default' : true,
        'clickable' : true,
        'searchable' : true,
        'show_empty' : true,
        'render_group' : function (group_id, group) {
          return App.clean(group['name']);
        }
      }]
    });

    // handle on user added
    App.Wireframe.Events.bind('user_created.content', function (event, user) {
      wrapper.objectsList('add_item', user);
    });

    // handle on user edited
    App.Wireframe.Events.bind('user_updated.content', function (event, user) {
      if (user.company.state == 3) {
        wrapper.objectsList('update_item', user);
      } // if
    });

    App.Wireframe.Events.bind('company_updated.content', function (event, company) {
      // if company is archived or made visible
      if (company.state >= 2) {
        if (company.users !== null && company.users.length) {
          $.each(company.users, function (index, user) {
            wrapper.objectsList('update_item', user, true);
          });
        } // if
      } // if

      // update counter of archived companies
      var user_count = 0;
      if (company.users && company.users.length) {
        $.each(company.users, function (index, user) {
          if (user.state == 2) {
            user_count++;
          } // if
        });
      } // if

      if (company.state == 3) {
        $('#page_title_actions #page_action_add_user').show();
      } else {
        $('#page_title_actions #page_action_add_user').hide();
      } // if

      App.widgets.InlineTabs.updateCount('company_inline_tabs', 'archived_users', user_count);
    });

    // handle on user deleted
    App.Wireframe.Events.bind('user_deleted.content', function (event, user) {
      if (user['state'] == 0) {
        wrapper.objectsList('load_empty');
      } else {
        wrapper.objectsList('delete_item', user.id);
      } // if
    });

    // handle on company deleted
    App.Wireframe.Events.bind('company_deleted.content', function (event, company) {
      if (company['state'] == 0) {
        wrapper.objectsList('load_empty');
      } else {
        $('#page_title_actions #page_action_add_user').hide();
      } // if
    });

    // handle on people invitation
    App.Wireframe.Events.bind('people_invited.content', function(event, company) {
      // update list of company users
      if(company.users && company.users.length) {
        $.each(company.users, function(index, user) {
          wrapper.objectsList('update_item', user, true);
        });
      } // if
    });

    App.objects_list_keep_companies_map_up_to_date(wrapper, 'company_id', 'content', true);

    // load the company details after it's created and added to the list
    App.Wireframe.Events.bind('company_created.content', function(event, company) {
      wrapper.objectsList('load_group', 'company_id', company.id, company.permalink);
    });
    	
    {if (($active_user instanceof User) && $active_user->isLoaded())}
    	wrapper.objectsList('load_item', {$active_user->getId()}, '{$active_user->getViewUrl()}');
    {elseif (($active_company instanceof Company) && ($active_company->isLoaded()))}
      wrapper.objectsList('load_group', 'company_id', {$active_company->getId()}, {$active_company->getViewUrl()|json nofilter});
    {/if}
    
    // handle on importing vCard (upload/review)
    var upload_to_flyout_wrapper = $('div.import_vcard.upload_to_flyout');
  	var form = upload_to_flyout_wrapper.find('.import_vcard_form');
  	
    $('#import_vcard').click(function() {
    	upload_to_flyout_wrapper.slideToggle('fast');
    });
    
    form.find('button').click(function() {
    	form.ajaxSubmit({
        url : App.extendUrl(form.attr('action'), {
          'async' : 1
        }),
        type : 'post',
        data : { 'submitted' : 'submitted', 'wizard_step' : 'review' },
        success : function(response) {
        	if(response.substring(3, 8) == 'error') {
        		App.Wireframe.Flash.error(response);
        	} else {
        		
        		// hide/reset import vCard form
        	  upload_to_flyout_wrapper.slideToggle('fast');
          	form.find('input[type=file]:first').val('');
          	
            var dialog = App.widgets.FlyoutDialog.show({
			        'title' : App.lang('Review Uploaded vCard'),
			        'data' : response,
			        'min_height' : 'auto',
			        'success' : function() {
			        	
			        	/**
							   * This review form
							   *
							   * @var jQuery
							   */
			        	var review_form = $(this).find('form');
						
						  	/**
							   * Company input fields
							   *
							   * @var array
							   */
						  	var company_input_fields = ['companyName', 'companyAddress', 'companyPhone', 'companyFax', 'companyHomepage'];
						
						  	/**
							   * User input fields
							   *
							   * @var array
							   */
							  var user_input_fields = ['userEmail', 'userFirstName', 'userLastName', 'userTitle', 'userPhoneWork', 'userPhoneMobile', 'userImType', 'userIm'];
						
							  /**
							   * Switch main label appropriately
							   * 
							   * @param jQuery parent_object
							   * @param string object_class
							   * @param boolean status
							   * @return null
							   */
						    var switch_label = function(parent_object, object_class, status) {
						    	if(status) {
						    		parent_object.find('.' + object_class + ' span').show();
							      parent_object.find('.' + object_class).removeClass('will_be_imported').addClass('will_not_be_imported');
						    	} else {
						    		parent_object.find('.' + object_class + ' span').hide();
							    	parent_object.find('.' + object_class).removeClass('will_not_be_imported').addClass('will_be_imported');
						    	} // if
						    } // switch_label
						
							  /**
							   * Enable/disable input fields
							   * 
							   * @param array input_fields
							   * @param jQuery checkbox
							   * @param boolean enable
							   * @return null
							   */
							  var enable_disable_input_fields = function(input_fields, checkbox, enable) {
						    	$.each(input_fields, function() {
						    		if(enable) {
						    			checkbox.parent().find('#' + this).removeAttr('disabled');
						    		} else {
						    			checkbox.parent().find('#' + this).attr('disabled', true);
						    		} // if
							    });
						    } // enable_disable_input_fields
						    
						    /**
							   * Handler that is called when we change something to see if submit button
							   * needs to be enabled or disabled
							   * 
							   * @param void
							   * @return null
							   */
						    var enabled_disable_submit_button = function() {
						    	var submit_enabled = true;
						    	var checkboxes = $.merge(review_form.find('input.master_checkbox'), review_form.find('input.slave_checkbox'));
						
						    	if($('#select_objects :checkbox:checked').length == 0) {
						    		submit_enabled = false;
						    	} // if
						
						      if(submit_enabled) {
						        review_form.find(':button[type=submit]').removeAttr('disabled');
						      } else {
						        review_form.find(':button[type=submit]').attr('disabled', true);
						      } // if
						    } // enabled_disable_submit_button
						    
						    /**
							   * Object block full control
							   * 
							   * @param jQuery parent_object
							   * @param string object_class
							   * @param boolean status
							   * @param array input_fields
							   * @param jQuery checkbox
							   * @param boolean enable
							   * @return null
							   */
						    var controlling_object_block = function(parent_object, object_class, status, input_fields, checkbox, enable) {
						    	switch_label(parent_object, object_class, status);
						    	enable_disable_input_fields(input_fields, checkbox, enable);
						    	enabled_disable_submit_button();
						    } // controlling_object_block
						
							  /**
						     * Handle each company checkboxes behaviour
						     */
						    $('.company_data').each(function() {
							    var parent = $(this);
							    var master_checkbox = parent.find('input.master_checkbox');
							    var slave_checkboxes = parent.find('input.slave_checkbox');
						
							    // enable/disable master checkbox
							    var enable_disable_master_checkbox = function() {
							    	var enable_master_checkbox = true;
							      var count_checked_slave_checkboxes = parent.find('input.slave_checkbox' + ':checked').length;
						
							      if(count_checked_slave_checkboxes = 0 && enable_master_checkbox) {
							        enable_master_checkbox = true;
							      } else {
							      	enable_master_checkbox = true;
							      } // if
							      master_checkbox[0].checked = enable_master_checkbox;
							      
							      if(enable_master_checkbox) {
							      	switch_label(master_checkbox.parent(), 'company_name', false);
							      } else {
							      	switch_label(master_checkbox.parent(), 'company_name', true);
							      } // if
						
							      enable_disable_input_fields(company_input_fields, master_checkbox, enable_master_checkbox);
							    } // enable_disable_master_checkbox
						
							    // actions on initialisation
							    master_checkbox[0].checked = true;
							    switch_label(parent, 'company_name', false);
							    
							    slave_checkboxes.each(function() {
							    	this.checked = true;
							    	switch_label($(this).parent(), 'user_name', false);
							    });
						
							    // handle master company checkbox behaviour
							    master_checkbox.click(function() {
							      if(this.checked) {
							      	controlling_object_block(parent, 'company_name', false, company_input_fields, master_checkbox, true);
							      	$(this).parent().find('div').show('fast');
							      	
							        // apply same behaviour as on master company checkbox
							      	slave_checkboxes.each(function() {
										    this.checked = true;
										    controlling_object_block($(this).parent(), 'user_name', false, user_input_fields, $(this), true);
										  });
							      } else {
							      	controlling_object_block(parent, 'company_name', true, company_input_fields, master_checkbox, false);
							      	$(this).parent().find('div').hide('fast');
							      	
							        // apply same behaviour as on master company checkbox
							      	slave_checkboxes.each(function() {
										    this.checked = false;
										    controlling_object_block($(this).parent(), 'user_name', true, user_input_fields, $(this), false);
										  });
							      } // if
							    });
						
							    // handle company users checkbox behaviour
							    slave_checkboxes.click(function() {
							    	var slave_checkbox = $(this);
							    	
						    		if(slave_checkbox.is(':checked')) {
						    			controlling_object_block(slave_checkbox.parent(), 'user_name', false, user_input_fields, slave_checkbox, true);
						    			slave_checkbox.parent().find('div').show('fast');
						    		} else {
						    			controlling_object_block(slave_checkbox.parent(), 'user_name', true, user_input_fields, slave_checkbox, false);
						    			slave_checkbox.parent().find('div').hide('fast');
						    		} // if
							    	enable_disable_master_checkbox();
							    });
							  });
						
							  /**
						     * Handle each user checkboxes behaviour
						     */
							  $('.user_data').each(function() {
							  	var parent = $(this);
							  	var master_checkbox = parent.find('input.master_checkbox');
						
							  	// check user on initialisation
							  	master_checkbox[0].checked = true;
							  	switch_label(parent, 'user_name', false);
							  	
							  	// handle master user checkbox behaviour
							  	master_checkbox.click(function() {
							      if(this.checked) {
							      	controlling_object_block(parent, 'user_name', false, user_input_fields, master_checkbox, true);
							      	$(this).parent().find('div').show('fast');
							      } else {
							      	controlling_object_block(parent, 'user_name', true, user_input_fields, master_checkbox, false);
							      	$(this).parent().find('div').hide('fast');
							      } // if
							    });
							  });
							  
							  /**
						     * Handle sending welcome message
						     */
							  var welcome_email_wrapper = $('#send_welcome_message');
								var welcome_email_checkbox = welcome_email_wrapper.find('input#SendWelcomeEmail');
								
								welcome_email_wrapper.find('.control_holder').hide();
								
								welcome_email_checkbox.click(function() {
									if(welcome_email_wrapper.find('.control_holder').is(':hidden')) {
										welcome_email_wrapper.find('.control_holder').show('fast');
									} else {
										welcome_email_wrapper.find('.control_holder').hide('fast');
									} // if
								});
			          
			          review_form.find('button').click(function() {
					      	review_form.ajaxSubmit({
					          url : App.extendUrl(review_form.attr('action'), {
                      'async' : 1
                    }),
					          type : 'post',
					          data : { 'submitted' : 'submitted', 'wizard_step' : 'import' },
					          dataType: 'json',
					          success : function(response) {
				          	  App.widgets.FlyoutDialog.close(dialog);
					          	
					          	if(response.message.substring(3, 8) == 'error') {
					          		App.Wireframe.Flash.error(response.message);
					          	} else {
					          		if(response.companies.length) {
					          			$.each(response.companies, function(k, company) {
					          				var method = '';
					          				if(company['is_new']) {
					          					method = 'grouping_map_add_item';
					          				} else {
					          					method = 'grouping_map_update_item';
					          				} // if
					          				
					          				wrapper.objectsList(method, 'company_id', company['company'].id, {
											        'name' : App.clean(company['company'].name),
											        'permalink' : company['company'].permalink,
											        'is_archived' : company['company'].state == 2 ? 1 : 0,
											        'icon' : company['company'].avatar ? company['company'].avatar.small : ''
											      });
					          			});
					          		} // if
					          		
					          		if(response.users.length) {
					          			$.each(response.users, function(k, user) {
					          				var method = '';
					          				if(user['is_new']) {
					          					method = 'add_item';
					          				} else {
					          					method = 'update_item';
					          				} // if
					          				
					          				wrapper.objectsList(method, {
											        'id' : user['user'].id,
											        'company_id' : user['user'].company_id,
											        'display_name' : user['user'].display_name,
											        'avatar' : user['user'].avatar,
											        'email' : user['user'].email,
											        'state' : user['user'].state == 2 ? 1 : 0,
											        'permalink' : user['user'].permalink
											      });
					          			});
					          		} // if
					          	} // if

                      App.Wireframe.Flash.success(response.message);
					          },
					          error : function(response) {
					            App.Wireframe.Flash.error(App.lang('An error occurred while trying to import contacts from vCard'));
					          }
					        });
					        
					        return false;
					      });
			        }
			      });
        	} // if
        },
        error : function(response) {
          App.Wireframe.Flash.error(App.lang('An error occurred while trying to upload vCard'));
        }
      });
      
      return false;
    }); // uploading vCard form click event
  });
</script>