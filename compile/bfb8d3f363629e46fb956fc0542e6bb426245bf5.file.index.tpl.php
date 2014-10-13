<?php /* Smarty version Smarty-3.1.12, created on 2014-09-12 16:32:49
         compiled from "/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/authentication/views/default/fw_api_client_subscriptions/index.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1070652522541320314ff883-00009293%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'bfb8d3f363629e46fb956fc0542e6bb426245bf5' => 
    array (
      0 => '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/authentication/views/default/fw_api_client_subscriptions/index.tpl',
      1 => 1403109851,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1070652522541320314ff883-00009293',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'active_object' => 0,
    'subscriptions' => 0,
    'subscriptions_per_page' => 0,
    'total_subscriptions' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_54132031dec468_53997504',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_54132031dec468_53997504')) {function content_54132031dec468_53997504($_smarty_tpl) {?><?php if (!is_callable('smarty_block_title')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.title.php';
if (!is_callable('smarty_block_add_bread_crumb')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.add_bread_crumb.php';
if (!is_callable('smarty_function_use_widget')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/function.use_widget.php';
if (!is_callable('smarty_modifier_json')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/modifier.json.php';
if (!is_callable('smarty_function_image_url')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/function.image_url.php';
if (!is_callable('smarty_block_lang')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/globalization/helpers/block.lang.php';
?><?php $_smarty_tpl->smarty->_tag_stack[] = array('title', array()); $_block_repeat=true; echo smarty_block_title(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
API Subscriptions<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_title(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

<?php $_smarty_tpl->smarty->_tag_stack[] = array('add_bread_crumb', array()); $_block_repeat=true; echo smarty_block_add_bread_crumb(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
List<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_add_bread_crumb(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>


<?php echo smarty_function_use_widget(array('name'=>"paged_objects_list",'module'=>"environment"),$_smarty_tpl);?>


<?php if ($_smarty_tpl->tpl_vars['active_object']->value->isApiUser()){?>
  <div id="api_subscriptions"></div>
  
  <script type="text/javascript">
    $('#api_subscriptions').pagedObjectsList({
      'load_more_url' : <?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['active_object']->value->getApiSubscriptionsUrl());?>
,
      'items' : <?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['subscriptions']->value);?>
,
      'items_per_load' : <?php echo clean($_smarty_tpl->tpl_vars['subscriptions_per_page']->value,$_smarty_tpl);?>
, 
      'total_items' : <?php echo clean($_smarty_tpl->tpl_vars['total_subscriptions']->value,$_smarty_tpl);?>
, 
      'list_items_are' : 'tr',
      'list_item_attributes' : {
  		  'class' : 'api_client_subscription'
  		},
  		'columns' : {
    		'is_enabled' : '', 
  			'client' : App.lang('Client'), 
  			'access' : App.lang('Access Level'), 
  			'last_used_on' : App.lang('Last Used'), 
  			'options' : ''
  		},
      'empty_message' : App.lang('This user does not have any API subscriptions'), 
      'listen' : 'api_client_subscription', 
      'listen_constraint' : function(event, item) {
        return typeof(item) == 'object' && item && item['user_id'] == <?php echo clean($_smarty_tpl->tpl_vars['active_object']->value->getId(),$_smarty_tpl);?>
;
      },
      'on_add_item' : function(item) {
        var subscription = $(this);

        subscription.append(
          '<td class="is_enabled"></td>' +  
          '<td class="client"></td>' +  
          '<td class="access"></td>' +  
          '<td class="last_used_on"></td>' +  
		    	'<td class="options"></td>'
		   	);

        var checkbox = $('<input type="checkbox">').attr({ 
          'on_url' : item['urls']['enable'], 
          'off_url' : item['urls']['disable']
        }).asyncCheckbox({
          'success_event' : 'api_client_subscription_updated', 
          'success_message' : [ App.lang('Selected API subscription has been disabled'), App.lang('Selected API subscription has been enabled') ]
        }).appendTo(subscription.find('td.is_enabled'));

        checkbox[0].checked = item['is_enabled'];

        if(item['is_enabled']) {
          if(item['is_read_only']) {
  			    subscription.addClass('is_read_only');
  			    subscription.find('td.access').text(App.lang('Read Only'));
  			  } else {
  			    subscription.find('td.access').text(App.lang('Read and Write'));
  			  } // if
        } else {
          subscription.addClass('is_disabled');
          subscription.find('td.access').text(App.lang('Disabled'));
        } // if
        
        if(typeof(item['client_vendor']) == 'string' && item['client_vendor']) {
          subscription.find('td.client').html('<span class="api_client_name">' + App.clean(item['client_name']) + '</span> by <span class="api_client_vendor">' + App.clean(item['client_vendor']) + '</span>');
			  } else {
			    subscription.find('td.client').html('<span class="api_client_name">' + App.clean(item['client_name']) + '</span>');
			  } // if
			  
		   	if(typeof(item['last_used_on']) == 'object' && item['last_used_on']) {
			   	 subscription.find('td.last_used_on').text(item['last_used_on']['formatted']);
		   	} else {
			   	 subscription.find('td.last_used_on').html('<span class="details">' + App.lang('Never Used') + '</span>');
		   	} // if

        var options_cell = subscription.find('td.options');

        $('<a href="' + item['urls']['view'] + '" class="preview_subscription" title="' + App.lang('API Subscription Details') + '"><img src="<?php echo smarty_function_image_url(array('name'=>"icons/12x12/preview.png",'module'=>@ENVIRONMENT_FRAMEWORK),$_smarty_tpl);?>
"></a>').appendTo(options_cell).flyout({
          'width' : 700
        });

        if(item['permissions']['can_edit']) {
          $('<a href="' + item['urls']['edit'] + '" class="edit_api_client_subscription" title="' + App.lang('Update API Subscription') + '"><img src="<?php echo smarty_function_image_url(array('name'=>"icons/12x12/edit.png",'module'=>@ENVIRONMENT_FRAMEWORK),$_smarty_tpl);?>
"></a>').flyoutForm({
            'success_event' : 'api_client_subscription_updated'
  			  }).appendTo(options_cell);
        } // if

  	    if(item['permissions']['can_delete']) {
  		    $('<a href="' + item['urls']['delete'] + '" class="delete_api_client_subscription" title="' + App.lang('Delete') + '"><img src="<?php echo smarty_function_image_url(array('name'=>"icons/12x12/delete.png",'module'=>@ENVIRONMENT_FRAMEWORK),$_smarty_tpl);?>
"></a>').asyncLink({
  		      'confirmation' : App.lang('Are you sure that you want to delete this subscription?'), 
  		      'success_event' : 'api_client_subscription_deleted', 
  		      'success_message' : App.lang('Selected API client subscription has been deleted')
  			  }).appendTo(options_cell);
  	    } // if
      }
    });
  </script>
<?php }else{ ?>
  <p class="empty_page"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
API access is not enabled for this user account<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</p>
<?php }?><?php }} ?>