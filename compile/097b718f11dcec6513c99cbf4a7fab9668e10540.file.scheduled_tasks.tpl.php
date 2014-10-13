<?php /* Smarty version Smarty-3.1.12, created on 2014-06-20 16:57:06
         compiled from "/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/views/default/empty_slates/scheduled_tasks.tpl" */ ?>
<?php /*%%SmartyHeaderCode:37700860853a467e21c7993-54893622%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '097b718f11dcec6513c99cbf4a7fab9668e10540' => 
    array (
      0 => '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/views/default/empty_slates/scheduled_tasks.tpl',
      1 => 1403109851,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '37700860853a467e21c7993-54893622',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'scheduled_tasks' => 0,
    'scheduled_task' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_53a467e2397a66_02075865',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_53a467e2397a66_02075865')) {function content_53a467e2397a66_02075865($_smarty_tpl) {?><?php if (!is_callable('smarty_block_lang')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/globalization/helpers/block.lang.php';
if (!is_callable('smarty_function_image_url')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/function.image_url.php';
if (!is_callable('smarty_function_scheduled_task_url')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/function.scheduled_task_url.php';
if (!is_callable('smarty_function_scheduled_task_command')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/function.scheduled_task_command.php';
?><div id="empty_slate_system_roles" class="empty_slate">
  <h3><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
About Scheduled Tasks<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h3>
  
  <ul class="icon_list">
    <li>
      <img src="<?php echo smarty_function_image_url(array('name'=>"empty-slates/modules.png",'module'=>@ENVIRONMENT_FRAMEWORK),$_smarty_tpl);?>
" class="icon_list_icon" alt="" />
      <span class="icon_list_title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Scheduled Tasks<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</span>
      <span class="icon_list_description"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Some activeCollab modules require to be called periodically in order to do something. For instance, Invoicing module requires to be called once a day in order to process recurring profiles. Tasks that are executed in this way are usually utility tasks and do not require user interaction<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
.</span>
    </li>
    
    <li>
      <img src="<?php echo smarty_function_image_url(array('name'=>"empty-slates/scheduled-tasks.png",'module'=>@ENVIRONMENT_FRAMEWORK),$_smarty_tpl);?>
" class="icon_list_icon" alt="" />
      <span class="icon_list_title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Execution Frequency<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</span>
      <span class="icon_list_description"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
There are at least three type of scheduled events - events executed frequently (every 3 - 5 minutes), events executed once an hour and events executed once a day. These events need to be triggered from outside, by system utility used to periodically trigger and execute tasks<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
.</span>
    </li>
    
    <li>
      <img src="<?php echo smarty_function_image_url(array('name'=>"empty-slates/cli.png",'module'=>@ENVIRONMENT_FRAMEWORK),$_smarty_tpl);?>
" class="icon_list_icon" alt="" />
      <span class="icon_list_title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Executing Scheduled Tasks<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</span>
      <span class="icon_list_description">
      <?php if (!is_windows_server()){?>
        <?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Scheduled tasks can be triggered by executing following commands<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:
        <?php  $_smarty_tpl->tpl_vars['details'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['details']->_loop = false;
 $_smarty_tpl->tpl_vars['scheduled_task'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['scheduled_tasks']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['details']->key => $_smarty_tpl->tpl_vars['details']->value){
$_smarty_tpl->tpl_vars['details']->_loop = true;
 $_smarty_tpl->tpl_vars['scheduled_task']->value = $_smarty_tpl->tpl_vars['details']->key;
?>
        <pre>/usr/bin/curl -s -L <?php echo smarty_function_scheduled_task_url(array('task'=>$_smarty_tpl->tpl_vars['scheduled_task']->value),$_smarty_tpl);?>
 &gt; /dev/null</pre>
        <?php } ?>
        <?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Commands listed above are just examples<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
. <?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Please consult your system administrator or hosting provider for exact location of cURL executables and for assistance with getting these commands to execute properly on your server<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
.
      <?php }else{ ?>
        <?php  $_smarty_tpl->tpl_vars['details'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['details']->_loop = false;
 $_smarty_tpl->tpl_vars['scheduled_task'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['scheduled_tasks']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['details']->key => $_smarty_tpl->tpl_vars['details']->value){
$_smarty_tpl->tpl_vars['details']->_loop = true;
 $_smarty_tpl->tpl_vars['scheduled_task']->value = $_smarty_tpl->tpl_vars['details']->key;
?>
        <pre>&quot;C:&#92;PHP&#92;php.exe&quot; <?php echo smarty_function_scheduled_task_command(array('task'=>$_smarty_tpl->tpl_vars['scheduled_task']->value),$_smarty_tpl);?>
</pre>
        <?php } ?>
        <?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
On Windows you can also use Scheduled Tasks to trigger scheduled tasks in activeCollab. To set-up Scheduled Tasks on Windows XP, Vista and Windows 7 (as well as Windows 2003 Server or later) you can use schtasks.exe. To do so, open the command line and type in the following commands<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:
        <?php  $_smarty_tpl->tpl_vars['details'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['details']->_loop = false;
 $_smarty_tpl->tpl_vars['scheduled_task'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['scheduled_tasks']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['details']->key => $_smarty_tpl->tpl_vars['details']->value){
$_smarty_tpl->tpl_vars['details']->_loop = true;
 $_smarty_tpl->tpl_vars['scheduled_task']->value = $_smarty_tpl->tpl_vars['details']->key;
?>
        <pre>schtasks /create /ru "System" /sc minute /mo 3 /tn "activeCollab $scheduled_task job" /tr &quot;C:&#92;PHP&#92;php.exe <?php echo smarty_function_scheduled_task_command(array('task'=>$_smarty_tpl->tpl_vars['scheduled_task']->value),$_smarty_tpl);?>
 -f&quot;</pre>
        <?php } ?>
        <?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Commands listed above are just examples<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
. <?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Please consult your system administrator or hosting provider for exact location of PHP executables and for assistance with getting these commands to execute properly on your server<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
.
      <?php }?>
      </span>
    </li>
    
    <li>
      <img src="<?php echo smarty_function_image_url(array('name'=>"empty-slates/help.png",'module'=>@ENVIRONMENT_FRAMEWORK),$_smarty_tpl);?>
" class="icon_list_icon" alt="" />
      <span class="icon_list_title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
More Info<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</span>
      <span class="icon_list_description"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array('url'=>'http://www.activecollab.com/docs/manuals/admin-version-3/configuration/scheduled-tasks')); $_block_repeat=true; echo smarty_block_lang(array('url'=>'http://www.activecollab.com/docs/manuals/admin-version-3/configuration/scheduled-tasks'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
You can read more about Scheduled Tasks and how they should be configured in <a href=":url" target="_blank">Administrator's Guide</a><?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array('url'=>'http://www.activecollab.com/docs/manuals/admin-version-3/configuration/scheduled-tasks'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
.</span>
    </li>
  </ul>
</div><?php }} ?>