<?php /* Smarty version Smarty-3.1.12, created on 2014-06-26 22:05:59
         compiled from "/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/history/views/default/_object_history.tpl" */ ?>
<?php /*%%SmartyHeaderCode:135739569353ac99475ed791-39455307%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '2ce5c9f369852d745e6910d5e8ee426bf07b4e7a' => 
    array (
      0 => '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/history/views/default/_object_history.tpl',
      1 => 1403109851,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '135739569353ac99475ed791-39455307',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    '_history_object' => 0,
    '_history_modifications' => 0,
    '_history_modification' => 0,
    '_history_modification_modification' => 0,
    'request' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_53ac99476bec84_79959612',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_53ac99476bec84_79959612')) {function content_53ac99476bec84_79959612($_smarty_tpl) {?><?php if (!is_callable('smarty_function_use_widget')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/function.use_widget.php';
if (!is_callable('smarty_block_lang')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/globalization/helpers/block.lang.php';
if (!is_callable('smarty_function_assemble')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/function.assemble.php';
if (!is_callable('smarty_modifier_class')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/modifier.class.php';
?><?php echo smarty_function_use_widget(array('name'=>"text_compare_dialog",'module'=>"text_compare"),$_smarty_tpl);?>


<div class="resource object_history object_section" id="object_history_<?php echo clean($_smarty_tpl->tpl_vars['_history_object']->value->getId(),$_smarty_tpl);?>
">
  <div class="content_section_title"><h2><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
History<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h2></div>
  
  <div class="object_history_logs object_section_content common_object_section_content">
  <?php if (is_foreachable($_smarty_tpl->tpl_vars['_history_modifications']->value)){?>
    <?php  $_smarty_tpl->tpl_vars['_history_modification'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['_history_modification']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['_history_modifications']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['_history_modification']->key => $_smarty_tpl->tpl_vars['_history_modification']->value){
$_smarty_tpl->tpl_vars['_history_modification']->_loop = true;
?>
    <div class="object_history_log">
      <div class="object_history_modification_head"><?php echo $_smarty_tpl->tpl_vars['_history_modification']->value['head'];?>
</div>
      <ul>
      <?php  $_smarty_tpl->tpl_vars['_history_modification_modification'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['_history_modification_modification']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['_history_modification']->value['modifications']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['_history_modification_modification']->key => $_smarty_tpl->tpl_vars['_history_modification_modification']->value){
$_smarty_tpl->tpl_vars['_history_modification_modification']->_loop = true;
?>
        <li><?php echo $_smarty_tpl->tpl_vars['_history_modification_modification']->value;?>
</li>
      <?php } ?>
      </ul>
    </div>
    <?php } ?>
  <?php }else{ ?>
    <p class="empty_page"><span class="inner"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
History is empty<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</span></p>
  <?php }?>
  </div>
</div>

<script type="text/javascript">
  var wrapper = $('#object_history_<?php echo clean($_smarty_tpl->tpl_vars['_history_object']->value->getId(),$_smarty_tpl);?>
');
  var refresh_history_url = '<?php echo smarty_function_assemble(array('route'=>'object_history','object_id'=>$_smarty_tpl->tpl_vars['_history_object']->value->getId(),'object_class'=>get_class($_smarty_tpl->tpl_vars['_history_object']->value),'async'=>1),$_smarty_tpl);?>
';

  var modifications_wrapper = wrapper.find('div.object_history_logs');
   
  App.Wireframe.Events.bind('<?php echo clean($_smarty_tpl->tpl_vars['_history_object']->value->getUpdatedEventName(),$_smarty_tpl);?>
.<?php echo clean($_smarty_tpl->tpl_vars['request']->value->getEventScope(),$_smarty_tpl);?>
 <?php echo clean($_smarty_tpl->tpl_vars['_history_object']->value->getDeletedEventName(),$_smarty_tpl);?>
.<?php echo clean($_smarty_tpl->tpl_vars['request']->value->getEventScope(),$_smarty_tpl);?>
', function (event, object) {
    if (object['id'] != '<?php echo clean($_smarty_tpl->tpl_vars['_history_object']->value->getId(),$_smarty_tpl);?>
' || object['class'] != '<?php echo clean(smarty_modifier_class($_smarty_tpl->tpl_vars['_history_object']->value),$_smarty_tpl);?>
') {
      return false;
    } // if

    $.ajax({
       'url'      : refresh_history_url,
       'success'  : function (response) {
         response = $.trim(response);
         modifications_wrapper.empty();
         if (response) {
           modifications_wrapper.append(response);
	         wrapper.find('a.text_diffs').bind('click', function() {
		         doCompare.apply(this);
		         return false;
	         });
         } else {
           modifications_wrapper.append('<p class="empty_page"><span class="inner">' + App.lang('History is empty') + '</span></p>');
         } // if

       }
    });
  });

  var doCompare = function() {
	  var versions_to_compare = {
		  final_version : App.lang('selected'),
		  //final_name : App.lang('Selected'),
		  final_body : $(this).parent().find('pre.new').html(),
		  compare_with_version : App.lang('previous'),
		  //compare_with_name : App.lang('Previous'),
		  compare_with_body : $(this).parent().find('pre.old').html()
	  };
	  App.widgets.TextCompareDialog.compareText(this, versions_to_compare);
  };

  wrapper.find('a.text_diffs').click(function() {
	  doCompare.apply(this);
	  return false;
  });
</script><?php }} ?>