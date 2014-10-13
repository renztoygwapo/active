<?php /* Smarty version Smarty-3.1.12, created on 2014-06-26 22:05:58
         compiled from "/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/subtasks/views/default/_object_subtask_form_row.tpl" */ ?>
<?php /*%%SmartyHeaderCode:9566599953ac9946f22214-78542107%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'de5e464c356120b52490833053f84e976e5bc25f' => 
    array (
      0 => '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/subtasks/views/default/_object_subtask_form_row.tpl',
      1 => 1403109851,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '9566599953ac9946f22214-78542107',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'subtask' => 0,
    'subtask_parent' => 0,
    'subtask_data' => 0,
    'subtasks_id' => 0,
    'user' => 0,
    'logged_user' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_53ac9947156fc5_64145458',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_53ac9947156fc5_64145458')) {function content_53ac9947156fc5_64145458($_smarty_tpl) {?><?php if (!is_callable('smarty_function_text_field')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/function.text_field.php';
if (!is_callable('smarty_block_label')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.label.php';
if (!is_callable('smarty_function_select_assignee')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/assignees/helpers/function.select_assignee.php';
if (!is_callable('smarty_function_select_priority')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/complete/helpers/function.select_priority.php';
if (!is_callable('smarty_function_select_label')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/labels/helpers/function.select_label.php';
if (!is_callable('smarty_function_select_due_on')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/function.select_due_on.php';
if (!is_callable('smarty_block_submit')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.submit.php';
if (!is_callable('smarty_block_lang')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/globalization/helpers/block.lang.php';
?><?php if ($_smarty_tpl->tpl_vars['subtask']->value instanceof Subtask&&$_smarty_tpl->tpl_vars['subtask']->value->isLoaded()){?>
<tr class="edit_subtask">
<?php }else{ ?>
<tr class="new_subtask" style="display: none">
<?php }?>
  <td class="task_reorder"></td>
  <td class="task_meta"></td>
  <td colspan="2" class="task_content">
  <?php if ($_smarty_tpl->tpl_vars['subtask']->value instanceof Subtask&&$_smarty_tpl->tpl_vars['subtask']->value->isLoaded()){?>
    <form action="<?php echo clean($_smarty_tpl->tpl_vars['subtask']->value->getEditUrl(),$_smarty_tpl);?>
" method="post" class="subtask_form">
  <?php }else{ ?>
    <form action="<?php echo clean($_smarty_tpl->tpl_vars['subtask_parent']->value->subtasks()->getAddUrl(),$_smarty_tpl);?>
" method="post" class="subtask_form">
  <?php }?>
      <div class="subtask_summary">
        <?php echo smarty_function_text_field(array('name'=>'subtask[body]','value'=>$_smarty_tpl->tpl_vars['subtask_data']->value['body'],'class'=>'long','id'=>((string)$_smarty_tpl->tpl_vars['subtasks_id']->value)."_summary_field"),$_smarty_tpl);?>

      </div>
      
      <div class="subtask_attributes">
        <div class="subtask_attribute subtask_assignee">
          <?php $_smarty_tpl->smarty->_tag_stack[] = array('label', array('for'=>"(".((string)$_smarty_tpl->tpl_vars['subtasks_id']->value).")_select_assignee")); $_block_repeat=true; echo smarty_block_label(array('for'=>"(".((string)$_smarty_tpl->tpl_vars['subtasks_id']->value).")_select_assignee"), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Assignee<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_label(array('for'=>"(".((string)$_smarty_tpl->tpl_vars['subtasks_id']->value).")_select_assignee"), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 <?php echo smarty_function_select_assignee(array('name'=>'subtask[assignee_id]','value'=>$_smarty_tpl->tpl_vars['subtask_data']->value['assignee_id'],'parent'=>$_smarty_tpl->tpl_vars['subtask']->value,'user'=>$_smarty_tpl->tpl_vars['user']->value,'id'=>((string)$_smarty_tpl->tpl_vars['subtasks_id']->value)."_select_assignee"),$_smarty_tpl);?>

        </div>
        
      <?php if ($_smarty_tpl->tpl_vars['subtask']->value->usePriority()){?>
        <div class="subtask_attribute subtask_priority">
          <?php $_smarty_tpl->smarty->_tag_stack[] = array('label', array('for'=>"(".((string)$_smarty_tpl->tpl_vars['subtasks_id']->value).")_task_priority")); $_block_repeat=true; echo smarty_block_label(array('for'=>"(".((string)$_smarty_tpl->tpl_vars['subtasks_id']->value).")_task_priority"), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Priority<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_label(array('for'=>"(".((string)$_smarty_tpl->tpl_vars['subtasks_id']->value).")_task_priority"), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 <?php echo smarty_function_select_priority(array('name'=>'subtask[priority]','value'=>$_smarty_tpl->tpl_vars['subtask_data']->value['priority'],'id'=>((string)$_smarty_tpl->tpl_vars['subtasks_id']->value)."_task_priority"),$_smarty_tpl);?>

        </div>
      <?php }?>
        
      <?php if ($_smarty_tpl->tpl_vars['subtask']->value->useLabels()){?>
        <div class="subtask_attribute subtask_label">
          <?php $_smarty_tpl->smarty->_tag_stack[] = array('label', array('for'=>"(".((string)$_smarty_tpl->tpl_vars['subtasks_id']->value).")_label")); $_block_repeat=true; echo smarty_block_label(array('for'=>"(".((string)$_smarty_tpl->tpl_vars['subtasks_id']->value).")_label"), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Label<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_label(array('for'=>"(".((string)$_smarty_tpl->tpl_vars['subtasks_id']->value).")_label"), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 <?php echo smarty_function_select_label(array('name'=>'subtask[label_id]','value'=>$_smarty_tpl->tpl_vars['subtask_data']->value['label_id'],'type'=>get_class($_smarty_tpl->tpl_vars['subtask']->value->label()->newLabel()),'id'=>((string)$_smarty_tpl->tpl_vars['subtasks_id']->value)."_label",'user'=>$_smarty_tpl->tpl_vars['logged_user']->value,'can_create_new'=>false),$_smarty_tpl);?>

        </div>
      <?php }?>
        
        <div class="subtask_attribute subtask_due_on">
          <?php $_smarty_tpl->smarty->_tag_stack[] = array('label', array('for'=>"(".((string)$_smarty_tpl->tpl_vars['subtasks_id']->value).")_due_on")); $_block_repeat=true; echo smarty_block_label(array('for'=>"(".((string)$_smarty_tpl->tpl_vars['subtasks_id']->value).")_due_on"), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Due On<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_label(array('for'=>"(".((string)$_smarty_tpl->tpl_vars['subtasks_id']->value).")_due_on"), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 <?php echo smarty_function_select_due_on(array('name'=>'subtask[due_on]','value'=>$_smarty_tpl->tpl_vars['subtask_data']->value['due_on'],'id'=>((string)$_smarty_tpl->tpl_vars['subtasks_id']->value)."_due_on"),$_smarty_tpl);?>

        </div>
      </div>
      
      <input type="hidden" name="submitted" value="submitted" />
      
      <div class="subtask_buttons_wrapper">
      <?php if ($_smarty_tpl->tpl_vars['subtask']->value instanceof Subtask&&$_smarty_tpl->tpl_vars['subtask']->value->isLoaded()){?>
        <?php $_smarty_tpl->smarty->_tag_stack[] = array('submit', array()); $_block_repeat=true; echo smarty_block_submit(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Save Changes<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_submit(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 <?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
or<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 <a href="#" class="subtask_cancel"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Close<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a>
      <?php }else{ ?>
        <?php $_smarty_tpl->smarty->_tag_stack[] = array('submit', array()); $_block_repeat=true; echo smarty_block_submit(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Add Subtask<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_submit(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 <?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
or<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 <a href="#" class="subtask_cancel"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Close<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a>
      <?php }?>
      </div>
    </form>
  </td>
</tr><?php }} ?>