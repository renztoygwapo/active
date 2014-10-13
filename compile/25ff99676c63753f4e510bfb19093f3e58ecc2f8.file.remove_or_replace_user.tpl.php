<?php /* Smarty version Smarty-3.1.12, created on 2014-06-27 17:28:54
         compiled from "/home/abag/public_html/dev/activecollab/4.2.6/modules/system/views/default/project_people/remove_or_replace_user.tpl" */ ?>
<?php /*%%SmartyHeaderCode:50168033753ada9d6299473-77076286%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '25ff99676c63753f4e510bfb19093f3e58ecc2f8' => 
    array (
      0 => '/home/abag/public_html/dev/activecollab/4.2.6/modules/system/views/default/project_people/remove_or_replace_user.tpl',
      1 => 1403109852,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '50168033753ada9d6299473-77076286',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'initial_form_action' => 0,
    'user_can_be_removed' => 0,
    'remove_or_replace_data' => 0,
    'active_user' => 0,
    'replace_data' => 0,
    'logged_user' => 0,
    'open_responsibilities' => 0,
    'active_project' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_53ada9d6493e89_98375780',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_53ada9d6493e89_98375780')) {function content_53ada9d6493e89_98375780($_smarty_tpl) {?><?php if (!is_callable('smarty_block_title')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.title.php';
if (!is_callable('smarty_block_form')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.form.php';
if (!is_callable('smarty_block_wrap_fields')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.wrap_fields.php';
if (!is_callable('smarty_block_wrap')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.wrap.php';
if (!is_callable('smarty_function_radio_field')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/function.radio_field.php';
if (!is_callable('smarty_block_lang')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/globalization/helpers/block.lang.php';
if (!is_callable('smarty_function_select_user')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/authentication/helpers/function.select_user.php';
if (!is_callable('smarty_function_checkbox_field')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/function.checkbox_field.php';
if (!is_callable('smarty_block_wrap_buttons')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.wrap_buttons.php';
if (!is_callable('smarty_block_submit')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.submit.php';
if (!is_callable('smarty_modifier_json')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/modifier.json.php';
?><?php $_smarty_tpl->smarty->_tag_stack[] = array('title', array()); $_block_repeat=true; echo smarty_block_title(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Remove or Replace User<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_title(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>


<div id="remove_or_replace_user">
  <?php $_smarty_tpl->smarty->_tag_stack[] = array('form', array('action'=>$_smarty_tpl->tpl_vars['initial_form_action']->value)); $_block_repeat=true; echo smarty_block_form(array('action'=>$_smarty_tpl->tpl_vars['initial_form_action']->value), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

    <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap_fields', array()); $_block_repeat=true; echo smarty_block_wrap_fields(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

      <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap', array('field'=>'operation')); $_block_repeat=true; echo smarty_block_wrap(array('field'=>'operation'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

        <?php if ($_smarty_tpl->tpl_vars['user_can_be_removed']->value){?>
        <div>
          <?php echo smarty_function_radio_field(array('name'=>'remove_or_replace[operation]','value'=>'remove','pre_selected_value'=>$_smarty_tpl->tpl_vars['remove_or_replace_data']->value['operation'],'label'=>'Remove User from the Project','id'=>"remove_user_operation_remove"),$_smarty_tpl);?>


          <div id="remove_user_remove_options" class="slide_down_settings" <?php if ($_smarty_tpl->tpl_vars['remove_or_replace_data']->value['operation']!='remove'){?>style="display: none;"<?php }?>>
            <p><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array('name'=>$_smarty_tpl->tpl_vars['active_user']->value->getDisplayName(true))); $_block_repeat=true; echo smarty_block_lang(array('name'=>$_smarty_tpl->tpl_vars['active_user']->value->getDisplayName(true)), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<b>Warning</b>: This operation will set all :name's assigments to unassigned!<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array('name'=>$_smarty_tpl->tpl_vars['active_user']->value->getDisplayName(true)), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</p>
          </div>
        </div>
        <?php }?>

        <div>
        <?php if ($_smarty_tpl->tpl_vars['user_can_be_removed']->value){?>
          <?php echo smarty_function_radio_field(array('name'=>'remove_or_replace[operation]','value'=>'replace','pre_selected_value'=>$_smarty_tpl->tpl_vars['remove_or_replace_data']->value['operation'],'label'=>'Replace with Another User','id'=>"remove_user_operation_replace"),$_smarty_tpl);?>

        <?php }else{ ?>
          <input name="remove_or_replace[operation]" type="hidden" value="replace">
        <?php }?>

        <?php if ($_smarty_tpl->tpl_vars['user_can_be_removed']->value){?>
          <div id="remove_user_replace_options" class="slide_down_settings" <?php if ($_smarty_tpl->tpl_vars['remove_or_replace_data']->value['operation']=='remove'){?>style="display: none;"<?php }?>>
        <?php }?>
            <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap', array('field'=>'replace_with_id')); $_block_repeat=true; echo smarty_block_wrap(array('field'=>'replace_with_id'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

              <?php echo smarty_function_select_user(array('name'=>'remove_or_replace[replace_with_id]','value'=>$_smarty_tpl->tpl_vars['replace_data']->value['replace_with_id'],'user'=>$_smarty_tpl->tpl_vars['logged_user']->value,'exclude_ids'=>$_smarty_tpl->tpl_vars['active_user']->value->getId(),'label'=>"Replace With"),$_smarty_tpl);?>

            <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap(array('field'=>'replace_with_id'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>


            <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap', array('field'=>'send_notification')); $_block_repeat=true; echo smarty_block_wrap(array('field'=>'send_notification'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

              <?php echo smarty_function_checkbox_field(array('name'=>"remove_or_replace[send_notification]",'value'=>"1",'checked'=>$_smarty_tpl->tpl_vars['replace_data']->value['send_notification'],'label'=>"Notify Users About this Change"),$_smarty_tpl);?>

            <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap(array('field'=>'send_notification'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

        <?php if ($_smarty_tpl->tpl_vars['user_can_be_removed']->value){?>
          </div>
        <?php }?>
        </div>
      <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap(array('field'=>'operation'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>


      <div class="empty_slate">
        <h3><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Open Assignments<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h3>
      <?php if ($_smarty_tpl->tpl_vars['open_responsibilities']->value==1){?>
        <p><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array('name'=>$_smarty_tpl->tpl_vars['active_user']->value->getDisplayName(true))); $_block_repeat=true; echo smarty_block_lang(array('name'=>$_smarty_tpl->tpl_vars['active_user']->value->getDisplayName(true)), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
:name is responsible for <u>one open assignment</u> in this project<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array('name'=>$_smarty_tpl->tpl_vars['active_user']->value->getDisplayName(true)), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
.</p>
        <?php }elseif($_smarty_tpl->tpl_vars['open_responsibilities']->value>1){?>
        <p><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array('name'=>$_smarty_tpl->tpl_vars['active_user']->value->getDisplayName(true),'num'=>$_smarty_tpl->tpl_vars['open_responsibilities']->value)); $_block_repeat=true; echo smarty_block_lang(array('name'=>$_smarty_tpl->tpl_vars['active_user']->value->getDisplayName(true),'num'=>$_smarty_tpl->tpl_vars['open_responsibilities']->value), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
:name is responsible for <u>:num open assignments</u> in this project<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array('name'=>$_smarty_tpl->tpl_vars['active_user']->value->getDisplayName(true),'num'=>$_smarty_tpl->tpl_vars['open_responsibilities']->value), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
.</p>
        <?php }else{ ?>
        <p><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array('name'=>$_smarty_tpl->tpl_vars['active_user']->value->getDisplayName(true))); $_block_repeat=true; echo smarty_block_lang(array('name'=>$_smarty_tpl->tpl_vars['active_user']->value->getDisplayName(true)), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
:name is not responsible for any open assignment in this project<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array('name'=>$_smarty_tpl->tpl_vars['active_user']->value->getDisplayName(true)), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
.</p>
      <?php }?>
      </div>
    <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap_fields(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>


    <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap_buttons', array()); $_block_repeat=true; echo smarty_block_wrap_buttons(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

      <?php if ($_smarty_tpl->tpl_vars['remove_or_replace_data']->value['operation']=='remove'){?>
        <?php $_smarty_tpl->smarty->_tag_stack[] = array('submit', array()); $_block_repeat=true; echo smarty_block_submit(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Remove User<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_submit(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

      <?php }else{ ?>
        <?php $_smarty_tpl->smarty->_tag_stack[] = array('submit', array()); $_block_repeat=true; echo smarty_block_submit(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Replace User<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_submit(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

      <?php }?>
    <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap_buttons(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

  <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_form(array('action'=>$_smarty_tpl->tpl_vars['initial_form_action']->value), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

</div>

<script type="text/javascript">
  $('#remove_or_replace_user').each(function() {
    var wrapper = $(this);

    var remove_user_url = <?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['active_project']->value->getRemoveUserUrl($_smarty_tpl->tpl_vars['active_user']->value));?>
;
    var replace_user_url = <?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['active_project']->value->getReplaceUserUrl($_smarty_tpl->tpl_vars['active_user']->value));?>
;

    var form = wrapper.find('form');
    var submit_button = form.find('button[type=submit]');

    var remove_options = form.find('#remove_user_remove_options');
    var replace_options = form.find('#remove_user_replace_options');

    wrapper.find('#remove_user_operation_remove').click(function() {
      replace_options.slideUp(function() {
        remove_options.slideDown();
      });

      form.attr('action', remove_user_url);
      submit_button.text(App.lang('Remove User'));
    });

    wrapper.find('#remove_user_operation_replace').click(function() {
      remove_options.slideUp(function() {
        replace_options.slideDown();
      });

      form.attr('action', replace_user_url);
      submit_button.text(App.lang('Replace User'));
    });
  });
</script><?php }} ?>