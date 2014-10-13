<?php /* Smarty version Smarty-3.1.12, created on 2014-10-13 07:33:26
         compiled from "C:\wamp\www\active\activecollab\4.2.6\modules\system\views\default\backend\my_tasks.tpl" */ ?>
<?php /*%%SmartyHeaderCode:15920543b804640e569-19569870%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '580a0ceabcef243ddb798c38e325e75221569e55' => 
    array (
      0 => 'C:\\wamp\\www\\active\\activecollab\\4.2.6\\modules\\system\\views\\default\\backend\\my_tasks.tpl',
      1 => 1413185335,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '15920543b804640e569-19569870',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'logged_user' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_543b804645b082_82450979',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_543b804645b082_82450979')) {function content_543b804645b082_82450979($_smarty_tpl) {?><?php if (!is_callable('smarty_block_title')) include 'C:\\wamp\\www\\active\\activecollab/4.2.6/angie/frameworks/environment/helpers\\block.title.php';
if (!is_callable('smarty_block_add_bread_crumb')) include 'C:\\wamp\\www\\active\\activecollab/4.2.6/angie/frameworks/environment/helpers\\block.add_bread_crumb.php';
if (!is_callable('smarty_function_my_tasks')) include 'C:\\wamp\\www\\active\\activecollab/4.2.6/modules/tasks/helpers\\function.my_tasks.php';
?><?php $_smarty_tpl->smarty->_tag_stack[] = array('title', array()); $_block_repeat=true; echo smarty_block_title(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
My Tasks<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_title(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

<?php $_smarty_tpl->smarty->_tag_stack[] = array('add_bread_crumb', array()); $_block_repeat=true; echo smarty_block_add_bread_crumb(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
My Tasks<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_add_bread_crumb(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>


<?php echo smarty_function_my_tasks(array('user'=>$_smarty_tpl->tpl_vars['logged_user']->value,'include_late_assignments'=>true,'id'=>'my_tasks'),$_smarty_tpl);?>
<?php }} ?>