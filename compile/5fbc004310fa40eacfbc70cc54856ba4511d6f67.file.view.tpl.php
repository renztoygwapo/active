<?php /* Smarty version Smarty-3.1.12, created on 2014-09-12 16:33:19
         compiled from "/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/authentication/views/default/fw_api_client_subscriptions/view.tpl" */ ?>
<?php /*%%SmartyHeaderCode:6360815875413204fdadaf5-66150347%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '5fbc004310fa40eacfbc70cc54856ba4511d6f67' => 
    array (
      0 => '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/authentication/views/default/fw_api_client_subscriptions/view.tpl',
      1 => 1403109851,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '6360815875413204fdadaf5-66150347',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'active_api_client_subscription' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_5413204ff23bd3_61077012',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5413204ff23bd3_61077012')) {function content_5413204ff23bd3_61077012($_smarty_tpl) {?><?php if (!is_callable('smarty_block_title')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.title.php';
if (!is_callable('smarty_block_add_bread_crumb')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.add_bread_crumb.php';
if (!is_callable('smarty_function_use_widget')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/function.use_widget.php';
if (!is_callable('smarty_block_lang')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/globalization/helpers/block.lang.php';
if (!is_callable('smarty_modifier_datetime')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/globalization/helpers/modifier.datetime.php';
?><?php $_smarty_tpl->smarty->_tag_stack[] = array('title', array('lang'=>false)); $_block_repeat=true; echo smarty_block_title(array('lang'=>false), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
API Subscription Details<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_title(array('lang'=>false), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

<?php $_smarty_tpl->smarty->_tag_stack[] = array('add_bread_crumb', array()); $_block_repeat=true; echo smarty_block_add_bread_crumb(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
API Subscription Details<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_add_bread_crumb(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>


<?php echo smarty_function_use_widget(array('name'=>'properties_list','module'=>@ENVIRONMENT_FRAMEWORK),$_smarty_tpl);?>


<div id="api_client_subscription_details" class="object_inspector">
  <dl class="properties_list">
    <dt><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Client<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</dt>
    <dd><?php echo clean($_smarty_tpl->tpl_vars['active_api_client_subscription']->value->getClientName(),$_smarty_tpl);?>
</dd>

    <dt><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Client Vendor<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</dt>
    <dd>
    <?php if ($_smarty_tpl->tpl_vars['active_api_client_subscription']->value->getClientVendor()){?>
      <?php echo clean($_smarty_tpl->tpl_vars['active_api_client_subscription']->value->getClientVendor(),$_smarty_tpl);?>

    <?php }else{ ?>
      <span class="details"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Unknown<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</span>
    <?php }?>
    </dd>

    <dt><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Enabled<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</dt>
    <dd>
    <?php if ($_smarty_tpl->tpl_vars['active_api_client_subscription']->value->getIsEnabled()){?>
      <?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Yes<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

    <?php }else{ ?>
      <?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
No<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

    <?php }?>
    </dd>

  <?php if ($_smarty_tpl->tpl_vars['active_api_client_subscription']->value->getIsEnabled()){?>
    <dt><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Access Level<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</dt>
    <dd>
    <?php if ($_smarty_tpl->tpl_vars['active_api_client_subscription']->value->getIsReadOnly()){?>
      <?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Read Only<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

    <?php }else{ ?>
      <?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Read and Write<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

    <?php }?>
    </dd>
  <?php }?>

    <dt><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Created On<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</dt>
    <dd><?php echo clean(smarty_modifier_datetime($_smarty_tpl->tpl_vars['active_api_client_subscription']->value->getCreatedOn()),$_smarty_tpl);?>
</dd>

    <dt><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Last Used On<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</dt>
    <dd>
    <?php if ($_smarty_tpl->tpl_vars['active_api_client_subscription']->value->getLastUsedOn()){?>
      <?php echo clean(smarty_modifier_datetime($_smarty_tpl->tpl_vars['active_api_client_subscription']->value->getLastUsedOn()),$_smarty_tpl);?>

    <?php }else{ ?>
      <span class="details"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Never Used<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</span>
    <?php }?>
    </dd>
  </dl>
  
  <div class="body">
    <p><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array('url'=>$_smarty_tpl->tpl_vars['active_api_client_subscription']->value->getApiUrl())); $_block_repeat=true; echo smarty_block_lang(array('url'=>$_smarty_tpl->tpl_vars['active_api_client_subscription']->value->getApiUrl()), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
API URL: <span class="token">:url</span><?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array('url'=>$_smarty_tpl->tpl_vars['active_api_client_subscription']->value->getApiUrl()), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</p>
    <p><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array('token'=>$_smarty_tpl->tpl_vars['active_api_client_subscription']->value->getFormattedToken())); $_block_repeat=true; echo smarty_block_lang(array('token'=>$_smarty_tpl->tpl_vars['active_api_client_subscription']->value->getFormattedToken()), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Token: <span class="token">:token</span><?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array('token'=>$_smarty_tpl->tpl_vars['active_api_client_subscription']->value->getFormattedToken()), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</p>
  </div>
</div><?php }} ?>