<?php /* Smarty version Smarty-3.1.12, created on 2014-08-11 12:20:39
         compiled from "/home/abag/public_html/dev/activecollab/4.2.6/modules/tasks/views/default/my_tasks/unassigned.tpl" */ ?>
<?php /*%%SmartyHeaderCode:60043674253e8b517d78bf5-09415096%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '7eb83a3a5d1568799efe05432bc5aee67950e9a6' => 
    array (
      0 => '/home/abag/public_html/dev/activecollab/4.2.6/modules/tasks/views/default/my_tasks/unassigned.tpl',
      1 => 1403109852,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '60043674253e8b517d78bf5-09415096',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'assignments' => 0,
    'assignments_group' => 0,
    'assignment' => 0,
    'label_id' => 0,
    'labels' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_53e8b517ebc368_61050334',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_53e8b517ebc368_61050334')) {function content_53e8b517ebc368_61050334($_smarty_tpl) {?><?php if (!is_callable('smarty_block_title')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.title.php';
if (!is_callable('smarty_block_add_bread_crumb')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.add_bread_crumb.php';
if (!is_callable('smarty_block_lang')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/globalization/helpers/block.lang.php';
if (!is_callable('smarty_function_render_label')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/labels/helpers/function.render_label.php';
if (!is_callable('smarty_function_due_on')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/complete/helpers/function.due_on.php';
?><?php $_smarty_tpl->smarty->_tag_stack[] = array('title', array()); $_block_repeat=true; echo smarty_block_title(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Unassigned Tasks<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_title(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

<?php $_smarty_tpl->smarty->_tag_stack[] = array('add_bread_crumb', array()); $_block_repeat=true; echo smarty_block_add_bread_crumb(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Discover Work<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_add_bread_crumb(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>


<div id="unassigned_tasks">
  <?php if ($_smarty_tpl->tpl_vars['assignments']->value){?>
    <div id="unassigned_tasks_inner_wrapper">
    <?php  $_smarty_tpl->tpl_vars['assignments_group'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['assignments_group']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['assignments']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['assignments_group']->key => $_smarty_tpl->tpl_vars['assignments_group']->value){
$_smarty_tpl->tpl_vars['assignments_group']->_loop = true;
?>
      <?php if (is_foreachable($_smarty_tpl->tpl_vars['assignments_group']->value['assignments'])){?>
        <table class="common" cellspacing="0">
          <thead>
            <tr>
              <th colspan="2"><?php echo clean($_smarty_tpl->tpl_vars['assignments_group']->value['label'],$_smarty_tpl);?>
</th>
              <th class="due_on"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Due Date<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</th>
              <th class="age"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Age<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</th>
            </tr>
          </thead>
          <tbody>
          <?php  $_smarty_tpl->tpl_vars['assignment'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['assignment']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['assignments_group']->value['assignments']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['assignment']->key => $_smarty_tpl->tpl_vars['assignment']->value){
$_smarty_tpl->tpl_vars['assignment']->_loop = true;
?>
            <?php $_smarty_tpl->tpl_vars['label_id'] = new Smarty_variable($_smarty_tpl->tpl_vars['assignment']->value['label_id'], null, 0);?>
            <tr class="assignment task">
              <td class="label right">
                <?php if ($_smarty_tpl->tpl_vars['label_id']->value&&$_smarty_tpl->tpl_vars['labels']->value[$_smarty_tpl->tpl_vars['label_id']->value]){?>
                  <?php echo smarty_function_render_label(array('label'=>$_smarty_tpl->tpl_vars['labels']->value[$_smarty_tpl->tpl_vars['label_id']->value]),$_smarty_tpl);?>

                <?php }?>
              </td>
              <td class="name">
                <span class="object_type object_type_task"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array('task_id'=>$_smarty_tpl->tpl_vars['assignment']->value['task_id'])); $_block_repeat=true; echo smarty_block_lang(array('task_id'=>$_smarty_tpl->tpl_vars['assignment']->value['task_id']), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Task #:task_id<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array('task_id'=>$_smarty_tpl->tpl_vars['assignment']->value['task_id']), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</span> <a href="<?php echo clean($_smarty_tpl->tpl_vars['assignment']->value['permalink'],$_smarty_tpl);?>
" class="quick_view_item"><?php echo clean($_smarty_tpl->tpl_vars['assignment']->value['name'],$_smarty_tpl);?>
</a>
              </td>
              <td class="due_on"><?php if ($_smarty_tpl->tpl_vars['assignment']->value['due_on']){?><?php echo smarty_function_due_on(array('date'=>$_smarty_tpl->tpl_vars['assignment']->value['due_on']),$_smarty_tpl);?>
<?php }?></td>
              <td class="age">
                <?php if ($_smarty_tpl->tpl_vars['assignment']->value['age']==1){?>
                  <?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
One Day<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

                <?php }else{ ?>
                  <?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array('num'=>$_smarty_tpl->tpl_vars['assignment']->value['age'])); $_block_repeat=true; echo smarty_block_lang(array('num'=>$_smarty_tpl->tpl_vars['assignment']->value['age']), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
:num Days<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array('num'=>$_smarty_tpl->tpl_vars['assignment']->value['age']), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

                <?php }?>
              </td>
            </tr>
          <?php } ?>
          </tbody>
        </table>
      <?php }?>
    <?php } ?>
    </div>
  <?php }else{ ?>
    <p class="empty_page"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
There are no unassigned tasks<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</p>
  <?php }?>
</div><?php }} ?>