<?php /* Smarty version Smarty-3.1.12, created on 2014-06-26 22:05:19
         compiled from "/home/abag/public_html/dev/activecollab/4.2.6/modules/tasks/views/default/tasks/add.tpl" */ ?>
<?php /*%%SmartyHeaderCode:212541263453ac991f162733-89810693%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '4f3eb2f8086d766ad0a7b3e94786361b40af9b55' => 
    array (
      0 => '/home/abag/public_html/dev/activecollab/4.2.6/modules/tasks/views/default/tasks/add.tpl',
      1 => 1403109852,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '212541263453ac991f162733-89810693',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'add_task_url' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_53ac991f1f81b5_15000796',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_53ac991f1f81b5_15000796')) {function content_53ac991f1f81b5_15000796($_smarty_tpl) {?><?php if (!is_callable('smarty_block_title')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.title.php';
if (!is_callable('smarty_block_add_bread_crumb')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.add_bread_crumb.php';
if (!is_callable('smarty_block_form')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.form.php';
if (!is_callable('smarty_block_wrap_buttons')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.wrap_buttons.php';
if (!is_callable('smarty_block_submit')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.submit.php';
?><?php $_smarty_tpl->smarty->_tag_stack[] = array('title', array()); $_block_repeat=true; echo smarty_block_title(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
New Task<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_title(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

<?php $_smarty_tpl->smarty->_tag_stack[] = array('add_bread_crumb', array()); $_block_repeat=true; echo smarty_block_add_bread_crumb(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
New Task<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_add_bread_crumb(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>


<div id="add_project_task">
  <?php $_smarty_tpl->smarty->_tag_stack[] = array('form', array('action'=>$_smarty_tpl->tpl_vars['add_task_url']->value,'enctype'=>"multipart/form-data",'autofocus'=>'yes','ask_on_leave'=>'yes','class'=>'big_form')); $_block_repeat=true; echo smarty_block_form(array('action'=>$_smarty_tpl->tpl_vars['add_task_url']->value,'enctype'=>"multipart/form-data",'autofocus'=>'yes','ask_on_leave'=>'yes','class'=>'big_form'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

    <?php echo $_smarty_tpl->getSubTemplate (get_view_path('_task_form','tasks',@TASKS_MODULE), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>


  <?php if (AngieApplication::behaviour()->isTrackingEnabled()){?>
    <input type="hidden" name="_intent_id" value="<?php echo clean(AngieApplication::behaviour()->recordIntent('task_created'),$_smarty_tpl);?>
">
  <?php }?>
    
    <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap_buttons', array()); $_block_repeat=true; echo smarty_block_wrap_buttons(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

      <?php $_smarty_tpl->smarty->_tag_stack[] = array('submit', array()); $_block_repeat=true; echo smarty_block_submit(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Add Task<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_submit(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

    <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap_buttons(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

  <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_form(array('action'=>$_smarty_tpl->tpl_vars['add_task_url']->value,'enctype'=>"multipart/form-data",'autofocus'=>'yes','ask_on_leave'=>'yes','class'=>'big_form'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

</div><?php }} ?>