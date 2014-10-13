<?php /* Smarty version Smarty-3.1.12, created on 2014-10-13 07:33:37
         compiled from "C:\wamp\www\active\activecollab\4.2.6\modules\system\views\default\people\index.tpl" */ ?>
<?php /*%%SmartyHeaderCode:21780543b8051e05987-04196600%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'e3add40635cf0d02e389fae47c7f9163b7dd38d9' => 
    array (
      0 => 'C:\\wamp\\www\\active\\activecollab\\4.2.6\\modules\\system\\views\\default\\people\\index.tpl',
      1 => 1413185335,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '21780543b8051e05987-04196600',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'can_add_company' => 0,
    'can_import_vcard' => 0,
    'users' => 0,
    'companies_map' => 0,
    'mass_manager' => 0,
    'active_user' => 0,
    'active_company' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_543b8052054e59_78863120',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_543b8052054e59_78863120')) {function content_543b8052054e59_78863120($_smarty_tpl) {?><?php if (!is_callable('smarty_block_title')) include 'C:\\wamp\\www\\active\\activecollab/4.2.6/angie/frameworks/environment/helpers\\block.title.php';
if (!is_callable('smarty_block_add_bread_crumb')) include 'C:\\wamp\\www\\active\\activecollab/4.2.6/angie/frameworks/environment/helpers\\block.add_bread_crumb.php';
if (!is_callable('smarty_function_use_widget')) include 'C:\\wamp\\www\\active\\activecollab/4.2.6/angie/frameworks/environment/helpers\\function.use_widget.php';
if (!is_callable('smarty_block_lang')) include 'C:\\wamp\\www\\active\\activecollab/4.2.6/angie/frameworks/globalization/helpers\\block.lang.php';
if (!is_callable('smarty_function_image_url')) include 'C:\\wamp\\www\\active\\activecollab/4.2.6/angie/frameworks/environment/helpers\\function.image_url.php';
if (!is_callable('smarty_function_assemble')) include 'C:\\wamp\\www\\active\\activecollab/4.2.6/angie/frameworks/environment/helpers\\function.assemble.php';
if (!is_callable('smarty_block_form')) include 'C:\\wamp\\www\\active\\activecollab/4.2.6/angie/frameworks/environment/helpers\\block.form.php';
if (!is_callable('smarty_block_wrap')) include 'C:\\wamp\\www\\active\\activecollab/4.2.6/angie/frameworks/environment/helpers\\block.wrap.php';
if (!is_callable('smarty_function_file_field')) include 'C:\\wamp\\www\\active\\activecollab/4.2.6/angie/frameworks/environment/helpers\\function.file_field.php';
if (!is_callable('smarty_block_submit')) include 'C:\\wamp\\www\\active\\activecollab/4.2.6/angie/frameworks/environment/helpers\\block.submit.php';
if (!is_callable('smarty_modifier_json')) include 'C:\\wamp\\www\\active\\activecollab/4.2.6/angie/frameworks/environment/helpers\\modifier.json.php';
if (!is_callable('smarty_modifier_map')) include 'C:\\wamp\\www\\active\\activecollab/4.2.6/angie/frameworks/environment/helpers\\modifier.map.php';
?><?php $_smarty_tpl->smarty->_tag_stack[] = array('title', array()); $_block_repeat=true; echo smarty_block_title(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
People<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_title(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

<?php $_smarty_tpl->smarty->_tag_stack[] = array('add_bread_crumb', array()); $_block_repeat=true; echo smarty_block_add_bread_crumb(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Everyone<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_add_bread_crumb(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

<?php echo smarty_function_use_widget(array('name'=>"objects_list",'module'=>"environment"),$_smarty_tpl);?>

<?php echo smarty_function_use_widget(array('name'=>"form",'module'=>"environment"),$_smarty_tpl);?>


<div id="companies">
  <div class="empty_content">
      <div class="objects_list_title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
People<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</div>
      <div class="objects_list_icon"><img src="<?php echo smarty_function_image_url(array('name'=>'icons/48x48/companies.png','module'=>@SYSTEM_MODULE),$_smarty_tpl);?>
" alt=""/></div>
      <div class="objects_list_details_actions">
          <ul>
          <?php if ($_smarty_tpl->tpl_vars['can_add_company']->value){?>
            <li><a href="<?php echo smarty_function_assemble(array('route'=>'people_invite'),$_smarty_tpl);?>
" id="invite_people"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Invite People<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a></li>
            <li><a href="<?php echo smarty_function_assemble(array('route'=>'people_companies_add'),$_smarty_tpl);?>
" id="new_company"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
New Company<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a></li>
          <?php }?>
          <?php if ($_smarty_tpl->tpl_vars['can_import_vcard']->value){?><li><a href="#" id="import_vcard" title="Import vCard"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Import vCard<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a></li><?php }?>
          </ul>
      </div>
      
      <div class="upload_to_flyout import_vcard">
      	<?php $_smarty_tpl->smarty->_tag_stack[] = array('form', array('action'=>Router::assemble('people_import_vcard'),'method'=>'post','enctype'=>'multipart/form-data','class'=>"import_vcard_form")); $_block_repeat=true; echo smarty_block_form(array('action'=>Router::assemble('people_import_vcard'),'method'=>'post','enctype'=>'multipart/form-data','class'=>"import_vcard_form"), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

      	  <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap', array('field'=>'vcard')); $_block_repeat=true; echo smarty_block_wrap(array('field'=>'vcard'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

      	  	<?php echo smarty_function_file_field(array('name'=>"vcard",'label'=>"Upload vCard"),$_smarty_tpl);?>

      	  <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap(array('field'=>'vcard'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

      	  
      	  <?php $_smarty_tpl->smarty->_tag_stack[] = array('submit', array()); $_block_repeat=true; echo smarty_block_submit(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Upload vCard<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_submit(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 
  			<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_form(array('action'=>Router::assemble('people_import_vcard'),'method'=>'post','enctype'=>'multipart/form-data','class'=>"import_vcard_form"), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

			</div>
			
      <div class="object_lists_details_tips">
        <h3><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Tips<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</h3>
        <ul>
          <li><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
To select a company and load its details, please click on it in the list on the left<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</li>
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

    var print_url = '<?php echo smarty_function_assemble(array('route'=>'people','print'=>1),$_smarty_tpl);?>
';  

    var items = <?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['users']->value);?>
;
    var companies_map = <?php echo smarty_modifier_map($_smarty_tpl->tpl_vars['companies_map']->value);?>
;
    
    var mass_edit_url = '<?php echo smarty_function_assemble(array('route'=>'people_mass_edit'),$_smarty_tpl);?>
';
    
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
      'multi_actions' : <?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['mass_manager']->value);?>
,
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
    	
    <?php if ((($_smarty_tpl->tpl_vars['active_user']->value instanceof User)&&$_smarty_tpl->tpl_vars['active_user']->value->isLoaded())){?>
    	wrapper.objectsList('load_item', <?php echo clean($_smarty_tpl->tpl_vars['active_user']->value->getId(),$_smarty_tpl);?>
, '<?php echo clean($_smarty_tpl->tpl_vars['active_user']->value->getViewUrl(),$_smarty_tpl);?>
');
    <?php }elseif((($_smarty_tpl->tpl_vars['active_company']->value instanceof Company)&&($_smarty_tpl->tpl_vars['active_company']->value->isLoaded()))){?>
      wrapper.objectsList('load_group', 'company_id', <?php echo clean($_smarty_tpl->tpl_vars['active_company']->value->getId(),$_smarty_tpl);?>
, <?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['active_company']->value->getViewUrl());?>
);
    <?php }?>
    
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
</script><?php }} ?>