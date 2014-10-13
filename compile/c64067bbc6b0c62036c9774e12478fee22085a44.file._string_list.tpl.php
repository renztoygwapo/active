<?php /* Smarty version Smarty-3.1.12, created on 2014-10-04 12:06:35
         compiled from "C:\wamp\www\dev\activecollab\4.2.6\modules\system\views\default\_string_list.tpl" */ ?>
<?php /*%%SmartyHeaderCode:28875542fe2cb616819-06401241%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c64067bbc6b0c62036c9774e12478fee22085a44' => 
    array (
      0 => 'C:\\wamp\\www\\dev\\activecollab\\4.2.6\\modules\\system\\views\\default\\_string_list.tpl',
      1 => 1403109852,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '28875542fe2cb616819-06401241',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    '_string_list_id' => 0,
    '_string_list_name' => 0,
    '_string_list_value' => 0,
    '_string_list_num' => 0,
    '_string_list_item' => 0,
    '_string_list_link_title' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_542fe2cb9142c3_05652111',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_542fe2cb9142c3_05652111')) {function content_542fe2cb9142c3_05652111($_smarty_tpl) {?><?php if (!is_callable('smarty_function_counter')) include 'C:\\wamp\\www\\dev\\activecollab\\4.2.6\\angie\\vendor\\smarty\\smarty\\plugins\\function.counter.php';
if (!is_callable('smarty_function_cycle')) include 'C:\\wamp\\www\\dev\\activecollab\\4.2.6\\angie\\vendor\\smarty\\smarty\\plugins\\function.cycle.php';
if (!is_callable('smarty_function_image_url')) include 'C:\\wamp\\www\\dev\\activecollab/4.2.6/angie/frameworks/environment/helpers\\function.image_url.php';
if (!is_callable('smarty_block_lang')) include 'C:\\wamp\\www\\dev\\activecollab/4.2.6/angie/frameworks/globalization/helpers\\block.lang.php';
if (!is_callable('smarty_block_link')) include 'C:\\wamp\\www\\dev\\activecollab/4.2.6/angie/frameworks/environment/helpers\\block.link.php';
?><div id="<?php echo clean($_smarty_tpl->tpl_vars['_string_list_id']->value,$_smarty_tpl);?>
" string_list_name="<?php echo clean($_smarty_tpl->tpl_vars['_string_list_name']->value,$_smarty_tpl);?>
" class="string_list">
  <table>
<?php if (is_foreachable($_smarty_tpl->tpl_vars['_string_list_value']->value)){?>
  <?php echo smarty_function_counter(array('start'=>0,'name'=>'string_list_num','assign'=>'_string_list_num'),$_smarty_tpl);?>

  
  <?php  $_smarty_tpl->tpl_vars['_string_list_item'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['_string_list_item']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['_string_list_value']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['_string_list_item']->key => $_smarty_tpl->tpl_vars['_string_list_item']->value){
$_smarty_tpl->tpl_vars['_string_list_item']->_loop = true;
?>
    <tr class="<?php echo smarty_function_cycle(array('values'=>'odd,even'),$_smarty_tpl);?>
 item">
      <td class="num">#<?php echo smarty_function_counter(array('name'=>'string_list_num'),$_smarty_tpl);?>
<?php echo clean($_smarty_tpl->tpl_vars['_string_list_num']->value,$_smarty_tpl);?>
</td>
      <td class="value">
        <span><?php echo clean($_smarty_tpl->tpl_vars['_string_list_item']->value,$_smarty_tpl);?>
</span>
        <input type="hidden" name="<?php echo clean($_smarty_tpl->tpl_vars['_string_list_name']->value,$_smarty_tpl);?>
[]" value="<?php echo clean($_smarty_tpl->tpl_vars['_string_list_item']->value,$_smarty_tpl);?>
" />
      </td>
      <td class="remove"><a href="javascript: return false;"><img src="<?php echo smarty_function_image_url(array('name'=>"icons/12x12/delete.png",'module'=>@ENVIRONMENT_FRAMEWORK),$_smarty_tpl);?>
" alt="" /></a></td>
    </tr>
  <?php } ?>
<?php }else{ ?>
    <tr class="odd empty">
      <td colspan="2"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
List is Empty<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
    </tr>
<?php }?>
  </table>
  
  <div class="add_list_item">
  	<?php $_smarty_tpl->smarty->_tag_stack[] = array('link', array('href'=>"#",'title'=>$_smarty_tpl->tpl_vars['_string_list_link_title']->value,'class'=>"button_add add_list_item_button")); $_block_repeat=true; echo smarty_block_link(array('href'=>"#",'title'=>$_smarty_tpl->tpl_vars['_string_list_link_title']->value,'class'=>"button_add add_list_item_button"), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo clean($_smarty_tpl->tpl_vars['_string_list_link_title']->value,$_smarty_tpl);?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_link(array('href'=>"#",'title'=>$_smarty_tpl->tpl_vars['_string_list_link_title']->value,'class'=>"button_add add_list_item_button"), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

  </div>
</div>

<script type="text/javascript">
  $('#<?php echo clean($_smarty_tpl->tpl_vars['_string_list_id']->value,$_smarty_tpl);?>
').stringList();
</script><?php }} ?>