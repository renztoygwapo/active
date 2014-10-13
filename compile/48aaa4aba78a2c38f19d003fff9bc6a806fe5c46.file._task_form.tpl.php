<?php /* Smarty version Smarty-3.1.12, created on 2014-06-26 22:05:19
         compiled from "/home/abag/public_html/dev/activecollab/4.2.6/modules/tasks/views/default/tasks/_task_form.tpl" */ ?>
<?php /*%%SmartyHeaderCode:152685539753ac991f200543-17702148%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '48aaa4aba78a2c38f19d003fff9bc6a806fe5c46' => 
    array (
      0 => '/home/abag/public_html/dev/activecollab/4.2.6/modules/tasks/views/default/tasks/_task_form.tpl',
      1 => 1403109852,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '152685539753ac991f200543-17702148',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'task_data' => 0,
    'active_task' => 0,
    'active_project' => 0,
    'logged_user' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_53ac991f800d58_54530322',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_53ac991f800d58_54530322')) {function content_53ac991f800d58_54530322($_smarty_tpl) {?><?php if (!is_callable('smarty_block_wrap')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.wrap.php';
if (!is_callable('smarty_function_text_field')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/function.text_field.php';
if (!is_callable('smarty_block_wrap_editor')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.wrap_editor.php';
if (!is_callable('smarty_block_label')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.label.php';
if (!is_callable('smarty_block_editor_field')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/visual_editor/helpers/block.editor_field.php';
if (!is_callable('smarty_function_select_task_category')) include '/home/abag/public_html/dev/activecollab/4.2.6/modules/tasks/helpers/function.select_task_category.php';
if (!is_callable('smarty_function_select_milestone')) include '/home/abag/public_html/dev/activecollab/4.2.6/modules/system/helpers/function.select_milestone.php';
if (!is_callable('smarty_function_select_priority')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/complete/helpers/function.select_priority.php';
if (!is_callable('smarty_function_select_visibility')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/function.select_visibility.php';
if (!is_callable('smarty_function_select_due_on')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/function.select_due_on.php';
if (!is_callable('smarty_function_select_estimate')) include '/home/abag/public_html/dev/activecollab/4.2.6/modules/system/helpers/function.select_estimate.php';
if (!is_callable('smarty_block_lang')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/globalization/helpers/block.lang.php';
if (!is_callable('smarty_function_select_job_type')) include '/home/abag/public_html/dev/activecollab/4.2.6/modules/system/helpers/function.select_job_type.php';
if (!is_callable('smarty_function_select_label')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/labels/helpers/function.select_label.php';
if (!is_callable('smarty_function_select_attachments')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/attachments/helpers/function.select_attachments.php';
if (!is_callable('smarty_function_custom_fields')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/custom_fields/helpers/function.custom_fields.php';
if (!is_callable('smarty_function_select_assignees')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/assignees/helpers/function.select_assignees.php';
?><script type="text/javascript">
  App.widgets.FlyoutDialog.front().setAutoSize(false);
</script>

<div class="big_form_wrapper two_form_sidebars">
  <div class="main_form_column">
    <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap', array('field'=>'name')); $_block_repeat=true; echo smarty_block_wrap(array('field'=>'name'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

      <?php echo smarty_function_text_field(array('name'=>"task[name]",'value'=>$_smarty_tpl->tpl_vars['task_data']->value['name'],'id'=>'taskSummary','class'=>'title required validate_minlength 3','label'=>"Title",'required'=>true,'maxlength'=>"150"),$_smarty_tpl);?>

    <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap(array('field'=>'name'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>


    <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap_editor', array('field'=>'body')); $_block_repeat=true; echo smarty_block_wrap_editor(array('field'=>'body'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

      <?php $_smarty_tpl->smarty->_tag_stack[] = array('label', array()); $_block_repeat=true; echo smarty_block_label(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Description<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_label(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

      <?php $_smarty_tpl->smarty->_tag_stack[] = array('editor_field', array('name'=>"task[body]",'id'=>'taskBody','inline_attachments'=>$_smarty_tpl->tpl_vars['task_data']->value['inline_attachments'],'object'=>$_smarty_tpl->tpl_vars['active_task']->value)); $_block_repeat=true; echo smarty_block_editor_field(array('name'=>"task[body]",'id'=>'taskBody','inline_attachments'=>$_smarty_tpl->tpl_vars['task_data']->value['inline_attachments'],'object'=>$_smarty_tpl->tpl_vars['active_task']->value), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo $_smarty_tpl->tpl_vars['task_data']->value['body'];?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_editor_field(array('name'=>"task[body]",'id'=>'taskBody','inline_attachments'=>$_smarty_tpl->tpl_vars['task_data']->value['inline_attachments'],'object'=>$_smarty_tpl->tpl_vars['active_task']->value), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

    <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap_editor(array('field'=>'body'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

  </div>
  
  <div class="form_sidebar form_first_sidebar">
    <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap', array('field'=>'category_id')); $_block_repeat=true; echo smarty_block_wrap(array('field'=>'category_id'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

      <?php echo smarty_function_select_task_category(array('name'=>"task[category_id]",'value'=>$_smarty_tpl->tpl_vars['task_data']->value['category_id'],'parent'=>$_smarty_tpl->tpl_vars['active_project']->value,'user'=>$_smarty_tpl->tpl_vars['logged_user']->value,'label'=>'Category','success_event'=>"category_created"),$_smarty_tpl);?>

    <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap(array('field'=>'category_id'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>


    <?php if (Milestones::canAccess($_smarty_tpl->tpl_vars['logged_user']->value,$_smarty_tpl->tpl_vars['active_project']->value)){?>
      <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap', array('field'=>'milestone_id')); $_block_repeat=true; echo smarty_block_wrap(array('field'=>'milestone_id'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

        <?php echo smarty_function_select_milestone(array('name'=>"task[milestone_id]",'value'=>$_smarty_tpl->tpl_vars['task_data']->value['milestone_id'],'project'=>$_smarty_tpl->tpl_vars['active_project']->value,'user'=>$_smarty_tpl->tpl_vars['logged_user']->value,'label'=>'Milestone'),$_smarty_tpl);?>

      <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap(array('field'=>'milestone_id'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

    <?php }?>
    
    <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap', array('field'=>'priority')); $_block_repeat=true; echo smarty_block_wrap(array('field'=>'priority'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

      <?php echo smarty_function_select_priority(array('name'=>"task[priority]",'value'=>$_smarty_tpl->tpl_vars['task_data']->value['priority'],'label'=>'Priority'),$_smarty_tpl);?>

    <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap(array('field'=>'priority'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

    
    <?php if ($_smarty_tpl->tpl_vars['logged_user']->value->canSeePrivate()){?>
      <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap', array('field'=>'visibility')); $_block_repeat=true; echo smarty_block_wrap(array('field'=>'visibility'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

        <?php echo smarty_function_select_visibility(array('name'=>"task[visibility]",'value'=>$_smarty_tpl->tpl_vars['task_data']->value['visibility'],'short_description'=>true,'label'=>'Visibility','object'=>$_smarty_tpl->tpl_vars['active_task']->value),$_smarty_tpl);?>

      <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap(array('field'=>'visibility'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

    <?php }else{ ?>
      <input type="hidden" name="task[visibility]" value="1" />
    <?php }?>
    
    <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap', array('field'=>'due_on')); $_block_repeat=true; echo smarty_block_wrap(array('field'=>'due_on'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

      <?php echo smarty_function_select_due_on(array('name'=>"task[due_on]",'value'=>$_smarty_tpl->tpl_vars['task_data']->value['due_on'],'id'=>'taskDueOn','label'=>'Due On'),$_smarty_tpl);?>

    <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap(array('field'=>'due_on'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

    
  <?php if (AngieApplication::isModuleLoaded('tracking')&&TrackingObjects::canAdd($_smarty_tpl->tpl_vars['logged_user']->value,$_smarty_tpl->tpl_vars['active_project']->value)){?>
    <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap', array('field'=>'estimate')); $_block_repeat=true; echo smarty_block_wrap(array('field'=>'estimate'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

      <?php $_smarty_tpl->smarty->_tag_stack[] = array('label', array()); $_block_repeat=true; echo smarty_block_label(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Estimate<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_label(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

      <?php echo smarty_function_select_estimate(array('name'=>'task[estimate_value]','value'=>$_smarty_tpl->tpl_vars['task_data']->value['estimate'],'short'=>true),$_smarty_tpl);?>
 <?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
of<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 <?php echo smarty_function_select_job_type(array('name'=>'task[estimate_job_type_id]','value'=>$_smarty_tpl->tpl_vars['task_data']->value['estimate_job_type_id'],'user'=>$_smarty_tpl->tpl_vars['logged_user']->value,'short'=>true),$_smarty_tpl);?>

    <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap(array('field'=>'estimate'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

    
    <?php if ($_smarty_tpl->tpl_vars['active_task']->value->isLoaded()){?>
      <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap', array('field'=>'estimate_comment')); $_block_repeat=true; echo smarty_block_wrap(array('field'=>'estimate_comment'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

      	<?php echo smarty_function_text_field(array('name'=>'task[estimate_comment]','label'=>'Estimate Update Comment'),$_smarty_tpl);?>

      <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap(array('field'=>'estimate_comment'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

    <?php }?>
  <?php }?>
    
    <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap', array('field'=>'label')); $_block_repeat=true; echo smarty_block_wrap(array('field'=>'label'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

      <?php echo smarty_function_select_label(array('name'=>"task[label_id]",'value'=>$_smarty_tpl->tpl_vars['task_data']->value['label_id'],'id'=>"taskLabel",'type'=>'AssignmentLabel','user'=>$_smarty_tpl->tpl_vars['logged_user']->value,'label'=>'Label'),$_smarty_tpl);?>

    <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap(array('field'=>'label'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>


    <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap', array('field'=>'attachments')); $_block_repeat=true; echo smarty_block_wrap(array('field'=>'attachments'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

      <?php echo smarty_function_select_attachments(array('name'=>"task[attachments]",'object'=>$_smarty_tpl->tpl_vars['active_task']->value,'user'=>$_smarty_tpl->tpl_vars['logged_user']->value,'label'=>'Attachments'),$_smarty_tpl);?>

    <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap(array('field'=>'attachments'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>


    <?php echo smarty_function_custom_fields(array('name'=>'task','object'=>$_smarty_tpl->tpl_vars['active_task']->value,'object_data'=>$_smarty_tpl->tpl_vars['task_data']->value),$_smarty_tpl);?>

  </div>
  
  <div class="form_sidebar form_second_sidebar">
    <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap', array('field'=>'assignees')); $_block_repeat=true; echo smarty_block_wrap(array('field'=>'assignees'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

      <?php $_smarty_tpl->smarty->_tag_stack[] = array('label', array('for'=>'taskAssignees')); $_block_repeat=true; echo smarty_block_label(array('for'=>'taskAssignees'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Assignees<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_label(array('for'=>'taskAssignees'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

      <?php echo smarty_function_select_assignees(array('name'=>"task",'value'=>$_smarty_tpl->tpl_vars['task_data']->value['assignee_id'],'exclude'=>$_smarty_tpl->tpl_vars['task_data']->value['exclude_ids'],'other_assignees'=>$_smarty_tpl->tpl_vars['task_data']->value['other_assignees'],'object'=>$_smarty_tpl->tpl_vars['active_task']->value,'user'=>$_smarty_tpl->tpl_vars['logged_user']->value,'choose_responsible'=>true,'choose_subscribers'=>$_smarty_tpl->tpl_vars['active_task']->value->isNew()),$_smarty_tpl);?>

    <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap(array('field'=>'assignees'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

  </div>
</div><?php }} ?>