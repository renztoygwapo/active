<?php /* Smarty version Smarty-3.1.12, created on 2014-06-20 16:57:03
         compiled from "/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/views/default/fw_scheduled_tasks_admin/index.tpl" */ ?>
<?php /*%%SmartyHeaderCode:74694625153a467dfaa27c1-28770794%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '61a084658a5d9f9b5ff42d9018165a512ab465ea' => 
    array (
      0 => '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/views/default/fw_scheduled_tasks_admin/index.tpl',
      1 => 1403109851,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '74694625153a467dfaa27c1-28770794',
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
  'unifunc' => 'content_53a467e2121171_92144938',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_53a467e2121171_92144938')) {function content_53a467e2121171_92144938($_smarty_tpl) {?><?php if (!is_callable('smarty_block_title')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.title.php';
if (!is_callable('smarty_block_add_bread_crumb')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.add_bread_crumb.php';
if (!is_callable('smarty_block_lang')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/globalization/helpers/block.lang.php';
if (!is_callable('smarty_modifier_datetime')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/globalization/helpers/modifier.datetime.php';
if (!is_callable('smarty_function_empty_slate')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/function.empty_slate.php';
?><?php $_smarty_tpl->smarty->_tag_stack[] = array('title', array()); $_block_repeat=true; echo smarty_block_title(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Scheduled Tasks<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_title(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

<?php $_smarty_tpl->smarty->_tag_stack[] = array('add_bread_crumb', array()); $_block_repeat=true; echo smarty_block_add_bread_crumb(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Scheduled Tasks<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_add_bread_crumb(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>


<div id="scheduled_tasks_admin" class="wireframe_content_wrapper settings_panel">
  <div class="settings_panel_header">
    <table class="settings_panel_header_cell_wrapper">
      <tr>
        <td class="settings_panel_header_cell">
          <h2><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Scheduled Tasks<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h2>
			    <div class="properties">
          <?php  $_smarty_tpl->tpl_vars['scheduled_task'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['scheduled_task']->_loop = false;
 $_smarty_tpl->tpl_vars['scheduled_task_name'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['scheduled_tasks']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['scheduled_task']->key => $_smarty_tpl->tpl_vars['scheduled_task']->value){
$_smarty_tpl->tpl_vars['scheduled_task']->_loop = true;
 $_smarty_tpl->tpl_vars['scheduled_task_name']->value = $_smarty_tpl->tpl_vars['scheduled_task']->key;
?>
            <div class="property">
              <div class="label"><?php echo clean($_smarty_tpl->tpl_vars['scheduled_task']->value['text'],$_smarty_tpl);?>
</div>
              <div class="data">
                <?php if ($_smarty_tpl->tpl_vars['scheduled_task']->value['last_activity'] instanceof DateTimeValue){?>
                  <?php echo clean(smarty_modifier_datetime($_smarty_tpl->tpl_vars['scheduled_task']->value['last_activity']),$_smarty_tpl);?>

                <?php }else{ ?>
                  <?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Never executed<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

                <?php }?>
              </div>
            </div>
          <?php } ?>
			    </div>
        </td>
      </tr>
    </table>
  </div>
  
  <div class="settings_panel_body">
    <?php echo smarty_function_empty_slate(array('name'=>'scheduled_tasks','module'=>@ENVIRONMENT_FRAMEWORK,'scheduled_tasks'=>$_smarty_tpl->tpl_vars['scheduled_tasks']->value),$_smarty_tpl);?>

  </div>
</div><?php }} ?>