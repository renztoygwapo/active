<?php /* Smarty version Smarty-3.1.12, created on 2014-10-03 05:46:21
         compiled from "C:\wamp\www\dev\activecollab\4.2.6\modules\tasks\views\default\my_tasks\_render_my_tasks.tpl" */ ?>
<?php /*%%SmartyHeaderCode:16471542e382dab0a79-49886687%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '167fb7d059ea019afad043bff0890f7c3a0a37e7' => 
    array (
      0 => 'C:\\wamp\\www\\dev\\activecollab\\4.2.6\\modules\\tasks\\views\\default\\my_tasks\\_render_my_tasks.tpl',
      1 => 1403109852,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '16471542e382dab0a79-49886687',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'id' => 0,
    'urls' => 0,
    'user_id' => 0,
    'late_assignments' => 0,
    'assignment' => 0,
    'subtask' => 0,
    'assignments' => 0,
    'assignment_group_name' => 0,
    'assignment_group' => 0,
    'label_id' => 0,
    'labels' => 0,
    'assignment_url_replacements' => 0,
    'subtask_url_replacements' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_542e382eea0bf4_86687313',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_542e382eea0bf4_86687313')) {function content_542e382eea0bf4_86687313($_smarty_tpl) {?><?php if (!is_callable('smarty_block_lang')) include 'C:\\wamp\\www\\dev\\activecollab/4.2.6/angie/frameworks/globalization/helpers\\block.lang.php';
if (!is_callable('smarty_function_due_on')) include 'C:\\wamp\\www\\dev\\activecollab/4.2.6/angie/frameworks/complete/helpers\\function.due_on.php';
if (!is_callable('smarty_block_assign_var')) include 'C:\\wamp\\www\\dev\\activecollab/4.2.6/angie/frameworks/environment/helpers\\block.assign_var.php';
if (!is_callable('smarty_function_render_priority')) include 'C:\\wamp\\www\\dev\\activecollab/4.2.6/modules/system/helpers\\function.render_priority.php';
if (!is_callable('smarty_function_render_label')) include 'C:\\wamp\\www\\dev\\activecollab/4.2.6/angie/frameworks/labels/helpers\\function.render_label.php';
if (!is_callable('smarty_function_image_url')) include 'C:\\wamp\\www\\dev\\activecollab/4.2.6/angie/frameworks/environment/helpers\\function.image_url.php';
if (!is_callable('smarty_function_replace')) include 'C:\\wamp\\www\\dev\\activecollab/4.2.6/angie/frameworks/environment/helpers\\function.replace.php';
if (!is_callable('smarty_function_assemble')) include 'C:\\wamp\\www\\dev\\activecollab/4.2.6/angie/frameworks/environment/helpers\\function.assemble.php';
?><div class="my_tasks_wrapper" id="<?php echo clean($_smarty_tpl->tpl_vars['id']->value,$_smarty_tpl);?>
" data-task-complete-url="<?php echo clean($_smarty_tpl->tpl_vars['urls']->value['task_complete_url'],$_smarty_tpl);?>
" data-task-reopen-url="<?php echo clean($_smarty_tpl->tpl_vars['urls']->value['task_reopen_url'],$_smarty_tpl);?>
" data-subtask-complete-url="<?php echo clean($_smarty_tpl->tpl_vars['urls']->value['subtask_complete_url'],$_smarty_tpl);?>
" data-subtask-reopen-url="<?php echo clean($_smarty_tpl->tpl_vars['urls']->value['subtask_complete_url'],$_smarty_tpl);?>
" data-can-use-tracking="<?php if (AngieApplication::isModuleLoaded('tracking')){?>1<?php }else{ ?>0<?php }?>" <?php if (isset($_smarty_tpl->tpl_vars['urls']->value['task_tracking_url'])){?>data-task-tracking-url="<?php echo clean($_smarty_tpl->tpl_vars['urls']->value['task_tracking_url'],$_smarty_tpl);?>
"<?php }?> data-refresh-url="<?php echo clean($_smarty_tpl->tpl_vars['urls']->value['refresh'],$_smarty_tpl);?>
" data-user-id="<?php echo clean($_smarty_tpl->tpl_vars['user_id']->value,$_smarty_tpl);?>
" data-auto-show-per-group="15" data-refresh-counter="0">

  <div class="my_late_tasks" <?php if (empty($_smarty_tpl->tpl_vars['late_assignments']->value)){?>style="display: none"<?php }?>>
    <div class="my_late_tasks_inner_wrapper">
      <h2><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Late or Due Today<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h2>
      <ul>
      <?php if ($_smarty_tpl->tpl_vars['late_assignments']->value){?>
        <?php  $_smarty_tpl->tpl_vars['assignment'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['assignment']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['late_assignments']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['assignment']->key => $_smarty_tpl->tpl_vars['assignment']->value){
$_smarty_tpl->tpl_vars['assignment']->_loop = true;
?>
          <?php if (($_smarty_tpl->tpl_vars['assignment']->value['due_on']&&$_smarty_tpl->tpl_vars['assignment']->value['due_on']<time())&&($_smarty_tpl->tpl_vars['assignment']->value['assignee_id']==$_smarty_tpl->tpl_vars['user_id']->value||(is_array($_smarty_tpl->tpl_vars['assignment']->value['other_assignees'])&&in_array($_smarty_tpl->tpl_vars['user_id']->value,$_smarty_tpl->tpl_vars['assignment']->value['other_assignees'])))){?>
            <li class="assignment task"><span class="object_type object_type_task"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array('task_id'=>$_smarty_tpl->tpl_vars['assignment']->value['task_id'])); $_block_repeat=true; echo smarty_block_lang(array('task_id'=>$_smarty_tpl->tpl_vars['assignment']->value['task_id']), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Task #:task_id<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array('task_id'=>$_smarty_tpl->tpl_vars['assignment']->value['task_id']), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</span> <a href="<?php echo clean($_smarty_tpl->tpl_vars['assignment']->value['permalink'],$_smarty_tpl);?>
" class="quick_view_item"><?php echo clean($_smarty_tpl->tpl_vars['assignment']->value['name'],$_smarty_tpl);?>
</a> &middot; <?php echo smarty_function_due_on(array('date'=>$_smarty_tpl->tpl_vars['assignment']->value['due_on']),$_smarty_tpl);?>
</li>
          <?php }?>

          <?php if ($_smarty_tpl->tpl_vars['assignment']->value['subtasks']){?>
            <?php  $_smarty_tpl->tpl_vars['subtask'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['subtask']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['assignment']->value['subtasks']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['subtask']->key => $_smarty_tpl->tpl_vars['subtask']->value){
$_smarty_tpl->tpl_vars['subtask']->_loop = true;
?>
              <li class="assignment subtask"><span class="object_type object_type_subtask"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Subtask<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</span> <a href="<?php echo clean($_smarty_tpl->tpl_vars['subtask']->value['permalink'],$_smarty_tpl);?>
" class="quick_view_item"><?php echo clean($_smarty_tpl->tpl_vars['subtask']->value['body'],$_smarty_tpl);?>
</a> <?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
in<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 <span class="object_type object_type_task"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array('task_id'=>$_smarty_tpl->tpl_vars['assignment']->value['task_id'])); $_block_repeat=true; echo smarty_block_lang(array('task_id'=>$_smarty_tpl->tpl_vars['assignment']->value['task_id']), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Task #:task_id<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array('task_id'=>$_smarty_tpl->tpl_vars['assignment']->value['task_id']), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</span> <a href="<?php echo clean($_smarty_tpl->tpl_vars['assignment']->value['permalink'],$_smarty_tpl);?>
" class="quick_view_item"><?php echo clean($_smarty_tpl->tpl_vars['assignment']->value['name'],$_smarty_tpl);?>
</a> &middot; <?php echo smarty_function_due_on(array('date'=>$_smarty_tpl->tpl_vars['subtask']->value['due_on']),$_smarty_tpl);?>
</li>
            <?php } ?>
          <?php }?>
        <?php } ?>
      <?php }?>
      </ul>
    </div>
  </div>

  <div class="my_tasks">
    <div class="my_tasks_inner_wrapper">
    <?php if ($_smarty_tpl->tpl_vars['assignments']->value){?>
      <?php  $_smarty_tpl->tpl_vars['assignment_group'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['assignment_group']->_loop = false;
 $_smarty_tpl->tpl_vars['assignment_group_name'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['assignments']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['assignment_group']->key => $_smarty_tpl->tpl_vars['assignment_group']->value){
$_smarty_tpl->tpl_vars['assignment_group']->_loop = true;
 $_smarty_tpl->tpl_vars['assignment_group_name']->value = $_smarty_tpl->tpl_vars['assignment_group']->key;
?>
        <table data-group-name="<?php echo clean($_smarty_tpl->tpl_vars['assignment_group_name']->value,$_smarty_tpl);?>
" data-showing-more="0" class="common assignment_group" cellspacing="0">
          <thead>
            <tr>
              <th class="group_name" colspan="2">
              <?php if ($_smarty_tpl->tpl_vars['assignment_group']->value['url']){?>
                <a href="<?php echo clean($_smarty_tpl->tpl_vars['assignment_group']->value['url'],$_smarty_tpl);?>
" class="quick_view_item"><?php echo clean($_smarty_tpl->tpl_vars['assignment_group']->value['label'],$_smarty_tpl);?>
</a>
              <?php }else{ ?>
                <?php echo clean($_smarty_tpl->tpl_vars['assignment_group']->value['label'],$_smarty_tpl);?>

              <?php }?>
              </th>
              <th class="right"><a href="#" class="toggle_group"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Hide<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a></th>
            </tr>
          </thead>

          <tbody>
          <?php  $_smarty_tpl->tpl_vars['assignment'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['assignment']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['assignment_group']->value['assignments']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['assignment']->key => $_smarty_tpl->tpl_vars['assignment']->value){
$_smarty_tpl->tpl_vars['assignment']->_loop = true;
?>
            <?php $_smarty_tpl->smarty->_tag_stack[] = array('assign_var', array('name'=>'assignment_url_replacements')); $_block_repeat=true; echo smarty_block_assign_var(array('name'=>'assignment_url_replacements'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo clean($_smarty_tpl->tpl_vars['assignment']->value['project_id'],$_smarty_tpl);?>
,<?php echo clean($_smarty_tpl->tpl_vars['assignment']->value['task_id'],$_smarty_tpl);?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_assign_var(array('name'=>'assignment_url_replacements'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>


            <?php if ($_smarty_tpl->tpl_vars['assignment']->value['assignee_id']==$_smarty_tpl->tpl_vars['user_id']->value||(is_array($_smarty_tpl->tpl_vars['assignment']->value['other_assignees'])&&in_array($_smarty_tpl->tpl_vars['user_id']->value,$_smarty_tpl->tpl_vars['assignment']->value['other_assignees']))){?>
              <?php $_smarty_tpl->tpl_vars['label_id'] = new Smarty_variable($_smarty_tpl->tpl_vars['assignment']->value['label_id'], null, 0);?>

              <tr class="assignment task" data-task-id="<?php echo clean($_smarty_tpl->tpl_vars['assignment']->value['id'],$_smarty_tpl);?>
">
                <td class="label right">
                  <?php if ($_smarty_tpl->tpl_vars['assignment']->value['priority']){?>
                    <?php echo smarty_function_render_priority(array('mode'=>'image','priority_id'=>$_smarty_tpl->tpl_vars['assignment']->value['priority']),$_smarty_tpl);?>

                  <?php }?>

                  <?php if ($_smarty_tpl->tpl_vars['label_id']->value&&$_smarty_tpl->tpl_vars['labels']->value[$_smarty_tpl->tpl_vars['label_id']->value]){?>
                    <?php echo smarty_function_render_label(array('label'=>$_smarty_tpl->tpl_vars['labels']->value[$_smarty_tpl->tpl_vars['label_id']->value]),$_smarty_tpl);?>

                  <?php }?>
                </td>
                <td class="name">
                  <span class="my_tasks_name_element checkbox"><img src="<?php echo smarty_function_image_url(array('name'=>'icons/12x12/checkbox-unchecked.png','module'=>@COMPLETE_FRAMEWORK),$_smarty_tpl);?>
" title="<?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Click to Complete<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" data-is-completed="0" data-complete-url="<?php echo smarty_function_replace(array('search'=>'--PROJECT-SLUG--,--TASK-ID--','replacement'=>$_smarty_tpl->tpl_vars['assignment_url_replacements']->value,'in'=>$_smarty_tpl->tpl_vars['urls']->value['task_complete_url'],'explode'=>','),$_smarty_tpl);?>
" data-reopen-url="<?php echo smarty_function_replace(array('search'=>'--PROJECT-SLUG--,--TASK-ID--','replacement'=>$_smarty_tpl->tpl_vars['assignment_url_replacements']->value,'in'=>$_smarty_tpl->tpl_vars['urls']->value['task_reopen_url'],'explode'=>','),$_smarty_tpl);?>
"></span>
                  <span class="my_tasks_name_element object_type object_type_task"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array('task_id'=>$_smarty_tpl->tpl_vars['assignment']->value['task_id'])); $_block_repeat=true; echo smarty_block_lang(array('task_id'=>$_smarty_tpl->tpl_vars['assignment']->value['task_id']), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Task #:task_id<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array('task_id'=>$_smarty_tpl->tpl_vars['assignment']->value['task_id']), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</span>
                <?php if ($_smarty_tpl->tpl_vars['assignment']->value['assignee_id']!=$_smarty_tpl->tpl_vars['user_id']->value){?>
                  <span class="my_tasks_name_element someone_else_is_responsible" title="<?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
You are assigned to this task<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
. <?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array('name'=>$_smarty_tpl->tpl_vars['assignment']->value['assignee'])); $_block_repeat=true; echo smarty_block_lang(array('name'=>$_smarty_tpl->tpl_vars['assignment']->value['assignee']), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
:name is responsible<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array('name'=>$_smarty_tpl->tpl_vars['assignment']->value['assignee']), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
"><?php echo clean($_smarty_tpl->tpl_vars['assignment']->value['assignee'],$_smarty_tpl);?>
</span>
                <?php }?>
                  <span class="my_tasks_name_element assignment_name"><a href="<?php echo clean($_smarty_tpl->tpl_vars['assignment']->value['permalink'],$_smarty_tpl);?>
" class="quick_view_item"><?php echo clean($_smarty_tpl->tpl_vars['assignment']->value['name'],$_smarty_tpl);?>
</a></span>
                </td>
                <td class="options right">
                  <?php if ($_smarty_tpl->tpl_vars['assignment']->value['due_on'] instanceof DateValue){?>
                    <?php echo smarty_function_due_on(array('date'=>$_smarty_tpl->tpl_vars['assignment']->value['due_on'],'id'=>('due_date_for_assignment_').($_smarty_tpl->tpl_vars['assignment']->value['id'])),$_smarty_tpl);?>

                  <?php }?>

                <?php if (AngieApplication::isModuleLoaded('tracking')){?>
                  <span class="object_tracking" id="<?php echo clean($_smarty_tpl->tpl_vars['id']->value,$_smarty_tpl);?>
_object_time_for_<?php echo clean($_smarty_tpl->tpl_vars['assignment']->value['id'],$_smarty_tpl);?>
" data-estimated-time="<?php echo clean($_smarty_tpl->tpl_vars['assignment']->value['estimated_time'],$_smarty_tpl);?>
" data-object-time="<?php echo clean($_smarty_tpl->tpl_vars['assignment']->value['tracked_time'],$_smarty_tpl);?>
" data-object-expenses="0" data-show-label="0"><a href="<?php echo smarty_function_replace(array('search'=>'--PROJECT-SLUG--,--TASK-ID--','replacement'=>$_smarty_tpl->tpl_vars['assignment_url_replacements']->value,'in'=>$_smarty_tpl->tpl_vars['urls']->value['task_tracking_url'],'explode'=>','),$_smarty_tpl);?>
"><img src="<?php echo smarty_function_image_url(array('name'=>'icons/12x12/object-time-inactive.png','module'=>@TRACKING_MODULE,'interface'=>@AngieApplication::INTERFACE_DEFAULT),$_smarty_tpl);?>
"></a></span>
                <?php }?>
                </td>
              </tr>
            <?php }?>

            <?php if ($_smarty_tpl->tpl_vars['assignment']->value['subtasks']){?>
              <?php  $_smarty_tpl->tpl_vars['subtask'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['subtask']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['assignment']->value['subtasks']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['subtask']->key => $_smarty_tpl->tpl_vars['subtask']->value){
$_smarty_tpl->tpl_vars['subtask']->_loop = true;
?>
                <?php $_smarty_tpl->tpl_vars['label_id'] = new Smarty_variable($_smarty_tpl->tpl_vars['subtask']->value['label_id'], null, 0);?>
                <?php $_smarty_tpl->smarty->_tag_stack[] = array('assign_var', array('name'=>'subtask_url_replacements')); $_block_repeat=true; echo smarty_block_assign_var(array('name'=>'subtask_url_replacements'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo clean($_smarty_tpl->tpl_vars['assignment']->value['project_id'],$_smarty_tpl);?>
,<?php echo clean($_smarty_tpl->tpl_vars['assignment']->value['task_id'],$_smarty_tpl);?>
,<?php echo clean($_smarty_tpl->tpl_vars['subtask']->value['id'],$_smarty_tpl);?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_assign_var(array('name'=>'subtask_url_replacements'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>


                <tr class="assignment subtask" data-task-id="<?php echo clean($_smarty_tpl->tpl_vars['subtask']->value['parent_id'],$_smarty_tpl);?>
" data-subtask-id="<?php echo clean($_smarty_tpl->tpl_vars['subtask']->value['id'],$_smarty_tpl);?>
">
                  <td class="label right">
                    <?php if ($_smarty_tpl->tpl_vars['subtask']->value['priority']){?>
                      <?php echo smarty_function_render_priority(array('mode'=>'image','priority_id'=>$_smarty_tpl->tpl_vars['subtask']->value['priority']),$_smarty_tpl);?>

                    <?php }?>

                    <?php if ($_smarty_tpl->tpl_vars['label_id']->value&&$_smarty_tpl->tpl_vars['labels']->value[$_smarty_tpl->tpl_vars['label_id']->value]){?>
                      <?php echo smarty_function_render_label(array('label'=>$_smarty_tpl->tpl_vars['labels']->value[$_smarty_tpl->tpl_vars['label_id']->value]),$_smarty_tpl);?>

                    <?php }?>
                  </td>
                  <td class="name">
                    <span class="my_tasks_name_element checkbox"><img src="<?php echo smarty_function_image_url(array('name'=>'icons/12x12/checkbox-unchecked.png','module'=>@COMPLETE_FRAMEWORK),$_smarty_tpl);?>
" title="<?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Click to Complete<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" data-is-completed="0" data-complete-url="<?php echo smarty_function_replace(array('search'=>'--PROJECT-SLUG--,--TASK-ID--,--SUBTASK-ID--','replacement'=>$_smarty_tpl->tpl_vars['subtask_url_replacements']->value,'in'=>$_smarty_tpl->tpl_vars['urls']->value['subtask_complete_url'],'explode'=>','),$_smarty_tpl);?>
" data-reopen-url="<?php echo smarty_function_replace(array('search'=>'--PROJECT-SLUG--,--TASK-ID--,--SUBTASK-ID--','replacement'=>$_smarty_tpl->tpl_vars['subtask_url_replacements']->value,'in'=>$_smarty_tpl->tpl_vars['urls']->value['subtask_reopen_url'],'explode'=>','),$_smarty_tpl);?>
"></span>
                    <span class="my_tasks_name_element object_type object_type_subtask"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Subtask<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</span>
                    <span class="my_tasks_name_element assignment_name"><a href="<?php echo clean($_smarty_tpl->tpl_vars['subtask']->value['permalink'],$_smarty_tpl);?>
" class="quick_view_item"><?php echo clean($_smarty_tpl->tpl_vars['subtask']->value['body'],$_smarty_tpl);?>
</a> <?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
in<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 <span class="object_type object_type_task"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array('task_id'=>$_smarty_tpl->tpl_vars['assignment']->value['task_id'])); $_block_repeat=true; echo smarty_block_lang(array('task_id'=>$_smarty_tpl->tpl_vars['assignment']->value['task_id']), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Task #:task_id<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array('task_id'=>$_smarty_tpl->tpl_vars['assignment']->value['task_id']), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</span> <a href="<?php echo clean($_smarty_tpl->tpl_vars['assignment']->value['permalink'],$_smarty_tpl);?>
" class="quick_view_item"><?php echo clean($_smarty_tpl->tpl_vars['assignment']->value['name'],$_smarty_tpl);?>
</a></span>
                  </td>
                  <td class="options right">
                  <?php if ($_smarty_tpl->tpl_vars['subtask']->value['due_on'] instanceof DateValue){?>
                    <?php echo smarty_function_due_on(array('date'=>$_smarty_tpl->tpl_vars['subtask']->value['due_on']),$_smarty_tpl);?>

                  <?php }?>

                  <?php if (AngieApplication::isModuleLoaded('tracking')){?>
                    <span class="object_tracking" id="<?php echo clean($_smarty_tpl->tpl_vars['id']->value,$_smarty_tpl);?>
_object_time_for_<?php echo clean($_smarty_tpl->tpl_vars['assignment']->value['id'],$_smarty_tpl);?>
_and_<?php echo clean($_smarty_tpl->tpl_vars['subtask']->value['id'],$_smarty_tpl);?>
" data-estimated-time="<?php echo clean($_smarty_tpl->tpl_vars['assignment']->value['estimated_time'],$_smarty_tpl);?>
" data-object-time="<?php echo clean($_smarty_tpl->tpl_vars['assignment']->value['tracked_time'],$_smarty_tpl);?>
" data-object-expenses="0" data-show-label="0"><a href="<?php echo smarty_function_replace(array('search'=>'--PROJECT-SLUG--,--TASK-ID--','replacement'=>$_smarty_tpl->tpl_vars['assignment_url_replacements']->value,'in'=>$_smarty_tpl->tpl_vars['urls']->value['task_tracking_url'],'explode'=>','),$_smarty_tpl);?>
"><img src="<?php echo smarty_function_image_url(array('name'=>'icons/12x12/object-time-inactive.png','module'=>@TRACKING_MODULE,'interface'=>@AngieApplication::INTERFACE_DEFAULT),$_smarty_tpl);?>
"></a></span>
                  <?php }?>
                  </td>
                </tr>
              <?php } ?>
            <?php }?>
          <?php } ?>
          </tbody>
        </table>
      <?php } ?>
    <?php }?>

      <p class="empty_page" <?php if ($_smarty_tpl->tpl_vars['assignments']->value){?>style="display: none"<?php }?>><span><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
There are no open tasks assigned to you<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
.</span><br><br><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Use the <b>Discover Work</b> option below to find what you can work on<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
.</p>
      <p class="my_tasks_more center"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
History<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
: <a href="<?php echo smarty_function_assemble(array('route'=>'my_tasks_completed'),$_smarty_tpl);?>
"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Recently Completed<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a> &middot; <?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Discover more work<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
: <a href="<?php echo smarty_function_assemble(array('route'=>'my_tasks_unassigned'),$_smarty_tpl);?>
"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Unassigned Tasks<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a></p>
    </div>
  </div>
</div>

<script type="text/javascript">
  $('#<?php echo clean($_smarty_tpl->tpl_vars['id']->value,$_smarty_tpl);?>
').myTasks();
</script><?php }} ?>