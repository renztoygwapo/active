<?php /* Smarty version Smarty-3.1.12, created on 2014-08-11 11:40:42
         compiled from "/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/authentication/notifications/email/welcome.tpl" */ ?>
<?php /*%%SmartyHeaderCode:746056653e8abba495bd3-83157024%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '1c54f828c38a0f93ee63ea5006dfa434abcbb683' => 
    array (
      0 => '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/authentication/notifications/email/welcome.tpl',
      1 => 1403109851,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '746056653e8abba495bd3-83157024',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'language' => 0,
    'context' => 0,
    'recipient' => 0,
    'sender' => 0,
    'style' => 0,
    'welcome_message' => 0,
    'password' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_53e8abba7137a9_75392899',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_53e8abba7137a9_75392899')) {function content_53e8abba7137a9_75392899($_smarty_tpl) {?><?php if (!is_callable('smarty_block_lang')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/globalization/helpers/block.lang.php';
if (!is_callable('smarty_block_notification_wrapper')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/email/helpers/block.notification_wrapper.php';
if (!is_callable('smarty_function_assemble')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/function.assemble.php';
if (!is_callable('smarty_block_notification_wrap_body')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/email/helpers/block.notification_wrap_body.php';
if (!is_callable('smarty_modifier_nl2br')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/modifier.nl2br.php';
?><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array('language'=>$_smarty_tpl->tpl_vars['language']->value)); $_block_repeat=true; echo smarty_block_lang(array('language'=>$_smarty_tpl->tpl_vars['language']->value), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
An Account has been Created for You<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array('language'=>$_smarty_tpl->tpl_vars['language']->value), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

================================================================================
<?php $_smarty_tpl->smarty->_tag_stack[] = array('notification_wrapper', array('title'=>'Welcome','context'=>$_smarty_tpl->tpl_vars['context']->value,'recipient'=>$_smarty_tpl->tpl_vars['recipient']->value,'sender'=>$_smarty_tpl->tpl_vars['sender']->value)); $_block_repeat=true; echo smarty_block_notification_wrapper(array('title'=>'Welcome','context'=>$_smarty_tpl->tpl_vars['context']->value,'recipient'=>$_smarty_tpl->tpl_vars['recipient']->value,'sender'=>$_smarty_tpl->tpl_vars['sender']->value), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

  <p><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array('link_style'=>$_smarty_tpl->tpl_vars['style']->value['link'],'name'=>$_smarty_tpl->tpl_vars['sender']->value->getName(),'creator_url'=>$_smarty_tpl->tpl_vars['sender']->value->getViewUrl(),'login_url'=>Router::assemble('homepage'),'language'=>$_smarty_tpl->tpl_vars['language']->value)); $_block_repeat=true; echo smarty_block_lang(array('link_style'=>$_smarty_tpl->tpl_vars['style']->value['link'],'name'=>$_smarty_tpl->tpl_vars['sender']->value->getName(),'creator_url'=>$_smarty_tpl->tpl_vars['sender']->value->getViewUrl(),'login_url'=>Router::assemble('homepage'),'language'=>$_smarty_tpl->tpl_vars['language']->value), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<a href=":creator_url" style=":link_style">:name</a> has created an account for you. You can <a href=":login_url" style=":link_style">log in</a> with the following parameters<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array('link_style'=>$_smarty_tpl->tpl_vars['style']->value['link'],'name'=>$_smarty_tpl->tpl_vars['sender']->value->getName(),'creator_url'=>$_smarty_tpl->tpl_vars['sender']->value->getViewUrl(),'login_url'=>Router::assemble('homepage'),'language'=>$_smarty_tpl->tpl_vars['language']->value), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</p>
  <table style="width: 100%; margin-top: 20px; <?php if ($_smarty_tpl->tpl_vars['welcome_message']->value){?>margin-bottom: 20px;<?php }?>">
    <tr>
      <td style="width: 80px; font-weight: bold"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array('language'=>$_smarty_tpl->tpl_vars['language']->value)); $_block_repeat=true; echo smarty_block_lang(array('language'=>$_smarty_tpl->tpl_vars['language']->value), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Login Page<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array('language'=>$_smarty_tpl->tpl_vars['language']->value), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</td>
      <td><a href="<?php echo smarty_function_assemble(array('route'=>'homepage'),$_smarty_tpl);?>
"><?php echo smarty_function_assemble(array('route'=>'homepage'),$_smarty_tpl);?>
</a></td>
    </tr>
    <tr>
      <td style="width: 80px; font-weight: bold"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array('language'=>$_smarty_tpl->tpl_vars['language']->value)); $_block_repeat=true; echo smarty_block_lang(array('language'=>$_smarty_tpl->tpl_vars['language']->value), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Email<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array('language'=>$_smarty_tpl->tpl_vars['language']->value), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</td>
      <td>&quot;<?php echo clean($_smarty_tpl->tpl_vars['recipient']->value->getEmail(),$_smarty_tpl);?>
&quot; (<?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array('language'=>$_smarty_tpl->tpl_vars['language']->value)); $_block_repeat=true; echo smarty_block_lang(array('language'=>$_smarty_tpl->tpl_vars['language']->value), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
without quotes<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array('language'=>$_smarty_tpl->tpl_vars['language']->value), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
)</td>
    </tr>
    <tr>
      <td style="width: 80px; font-weight: bold"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array('language'=>$_smarty_tpl->tpl_vars['language']->value)); $_block_repeat=true; echo smarty_block_lang(array('language'=>$_smarty_tpl->tpl_vars['language']->value), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Password<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array('language'=>$_smarty_tpl->tpl_vars['language']->value), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</td>
      <td>&quot;<?php echo clean($_smarty_tpl->tpl_vars['password']->value,$_smarty_tpl);?>
&quot; (<?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array('language'=>$_smarty_tpl->tpl_vars['language']->value)); $_block_repeat=true; echo smarty_block_lang(array('language'=>$_smarty_tpl->tpl_vars['language']->value), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
without quotes<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array('language'=>$_smarty_tpl->tpl_vars['language']->value), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
)</td>
    </tr>
  </table>
<?php if ($_smarty_tpl->tpl_vars['welcome_message']->value){?>
  <p><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array('language'=>$_smarty_tpl->tpl_vars['recipient']->value->getLanguage())); $_block_repeat=true; echo smarty_block_lang(array('language'=>$_smarty_tpl->tpl_vars['recipient']->value->getLanguage()), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Additionally, the following welcome message was provided<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array('language'=>$_smarty_tpl->tpl_vars['recipient']->value->getLanguage()), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</p>
  <?php $_smarty_tpl->smarty->_tag_stack[] = array('notification_wrap_body', array()); $_block_repeat=true; echo smarty_block_notification_wrap_body(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo smarty_modifier_nl2br($_smarty_tpl->tpl_vars['welcome_message']->value);?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_notification_wrap_body(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

<?php }?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_notification_wrapper(array('title'=>'Welcome','context'=>$_smarty_tpl->tpl_vars['context']->value,'recipient'=>$_smarty_tpl->tpl_vars['recipient']->value,'sender'=>$_smarty_tpl->tpl_vars['sender']->value), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
<?php }} ?>