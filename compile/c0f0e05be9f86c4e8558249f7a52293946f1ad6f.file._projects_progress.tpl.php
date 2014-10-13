<?php /* Smarty version Smarty-3.1.12, created on 2014-06-26 21:52:00
         compiled from "/home/abag/public_html/dev/activecollab/4.2.6/modules/system/views/default/project/_projects_progress.tpl" */ ?>
<?php /*%%SmartyHeaderCode:191492149453ac9600800c95-76421222%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c0f0e05be9f86c4e8558249f7a52293946f1ad6f' => 
    array (
      0 => '/home/abag/public_html/dev/activecollab/4.2.6/modules/system/views/default/project/_projects_progress.tpl',
      1 => 1403109852,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '191492149453ac9600800c95-76421222',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    '_project_progress' => 0,
    '_project_progress_info' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_53ac9600898579_20944686',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_53ac9600898579_20944686')) {function content_53ac9600898579_20944686($_smarty_tpl) {?><?php if (!is_callable('smarty_block_lang')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/globalization/helpers/block.lang.php';
?><?php if ($_smarty_tpl->tpl_vars['_project_progress']->value->getTotalTasksCount()){?>
  <div class="project_progress">
    <div class="progress_wrapper" <?php if (!$_smarty_tpl->tpl_vars['_project_progress_info']->value){?>title="<?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array('completed'=>$_smarty_tpl->tpl_vars['_project_progress']->value->getCompletedTaskCount(),'total'=>$_smarty_tpl->tpl_vars['_project_progress']->value->getTotalTasksCount(),'percent'=>$_smarty_tpl->tpl_vars['_project_progress']->value->getPercentsDone())); $_block_repeat=true; echo smarty_block_lang(array('completed'=>$_smarty_tpl->tpl_vars['_project_progress']->value->getCompletedTaskCount(),'total'=>$_smarty_tpl->tpl_vars['_project_progress']->value->getTotalTasksCount(),'percent'=>$_smarty_tpl->tpl_vars['_project_progress']->value->getPercentsDone()), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
:completed of :total tasks completed (:percent%)<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array('completed'=>$_smarty_tpl->tpl_vars['_project_progress']->value->getCompletedTaskCount(),'total'=>$_smarty_tpl->tpl_vars['_project_progress']->value->getTotalTasksCount(),'percent'=>$_smarty_tpl->tpl_vars['_project_progress']->value->getPercentsDone()), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
"<?php }?>>
      <div class="progress" style="width: <?php echo clean($_smarty_tpl->tpl_vars['_project_progress']->value->getPercentsDone(),$_smarty_tpl);?>
%"><span><?php echo clean($_smarty_tpl->tpl_vars['_project_progress']->value->getPercentsDone(),$_smarty_tpl);?>
%</span></div>
      <div class="progress_label"><?php echo clean($_smarty_tpl->tpl_vars['_project_progress']->value->getPercentsDone(),$_smarty_tpl);?>
%</div>
    </div>
    <?php if ($_smarty_tpl->tpl_vars['_project_progress_info']->value){?>
      <p><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array('completed'=>$_smarty_tpl->tpl_vars['_project_progress']->value->getCompletedTaskCount(),'total'=>$_smarty_tpl->tpl_vars['_project_progress']->value->getTotalTasksCount())); $_block_repeat=true; echo smarty_block_lang(array('completed'=>$_smarty_tpl->tpl_vars['_project_progress']->value->getCompletedTaskCount(),'total'=>$_smarty_tpl->tpl_vars['_project_progress']->value->getTotalTasksCount()), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<strong>:completed</strong> of <strong>:total</strong> tasks completed<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array('completed'=>$_smarty_tpl->tpl_vars['_project_progress']->value->getCompletedTaskCount(),'total'=>$_smarty_tpl->tpl_vars['_project_progress']->value->getTotalTasksCount()), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</p>
    <?php }?>
  </div>
<?php }?><?php }} ?>