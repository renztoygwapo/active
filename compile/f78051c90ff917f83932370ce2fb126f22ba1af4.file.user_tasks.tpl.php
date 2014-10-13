<?php /* Smarty version Smarty-3.1.12, created on 2014-06-26 22:04:53
         compiled from "/home/abag/public_html/dev/activecollab/4.2.6/modules/system/views/default/project/user_tasks.tpl" */ ?>
<?php /*%%SmartyHeaderCode:153764886853ac99051cca75-02329262%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'f78051c90ff917f83932370ce2fb126f22ba1af4' => 
    array (
      0 => '/home/abag/public_html/dev/activecollab/4.2.6/modules/system/views/default/project/user_tasks.tpl',
      1 => 1403109852,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '153764886853ac99051cca75-02329262',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'assignments' => 0,
    'labels' => 0,
    'project_slugs' => 0,
    'task_url' => 0,
    'task_subtask_url' => 0,
    'todo_url' => 0,
    'todo_subtask_url' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_53ac99052c4099_95086101',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_53ac99052c4099_95086101')) {function content_53ac99052c4099_95086101($_smarty_tpl) {?><?php if (!is_callable('smarty_block_title')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.title.php';
if (!is_callable('smarty_block_add_bread_crumb')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.add_bread_crumb.php';
if (!is_callable('smarty_function_use_widget')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/function.use_widget.php';
if (!is_callable('smarty_modifier_map')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/modifier.map.php';
if (!is_callable('smarty_modifier_json')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/modifier.json.php';
?><?php $_smarty_tpl->smarty->_tag_stack[] = array('title', array()); $_block_repeat=true; echo smarty_block_title(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
My Assignments on this Project<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_title(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

<?php $_smarty_tpl->smarty->_tag_stack[] = array('add_bread_crumb', array()); $_block_repeat=true; echo smarty_block_add_bread_crumb(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
My Assignments on this Project<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_add_bread_crumb(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

<?php echo smarty_function_use_widget(array('name'=>"assignments_list",'module'=>"system"),$_smarty_tpl);?>


<div id="user_assignments"></div>

<script type="text/javascript">
  $('#user_assignments').assignmentsList({
    'assignments' : <?php echo smarty_modifier_map($_smarty_tpl->tpl_vars['assignments']->value);?>
,
    'labels' : <?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['labels']->value);?>
,
    'project_slugs' : <?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['project_slugs']->value);?>
,
    'task_url' : <?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['task_url']->value);?>
,
    'task_subtask_url' : <?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['task_subtask_url']->value);?>
,
    'todo_url' : <?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['todo_url']->value);?>
,
    'todo_subtask_url' : <?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['todo_subtask_url']->value);?>
,
    'show_assignment_type' : true, 
    'additional_column_1' : <?php echo smarty_modifier_json(@AssignmentFilter::ADDITIONAL_COLUMN_CATEGORY);?>
,
    'additional_column_2' : <?php echo smarty_modifier_json(@AssignmentFilter::ADDITIONAL_COLUMN_MILESTONE);?>
,
    'show_no_assignments_message' : true, 
    'no_assignments_message' : App.lang("You don't have any assignments in this project")
  });
</script><?php }} ?>