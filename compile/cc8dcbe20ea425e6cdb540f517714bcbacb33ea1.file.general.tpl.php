<?php /* Smarty version Smarty-3.1.12, created on 2014-07-24 16:43:27
         compiled from "/home/abag/public_html/dev/activecollab/4.2.6/modules/system/views/default/settings/general.tpl" */ ?>
<?php /*%%SmartyHeaderCode:21508349053d137af866cd2-18094692%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'cc8dcbe20ea425e6cdb540f517714bcbacb33ea1' => 
    array (
      0 => '/home/abag/public_html/dev/activecollab/4.2.6/modules/system/views/default/settings/general.tpl',
      1 => 1403109852,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '21508349053d137af866cd2-18094692',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'general_data' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_53d137b06fc2f7_73724196',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_53d137b06fc2f7_73724196')) {function content_53d137b06fc2f7_73724196($_smarty_tpl) {?><?php if (!is_callable('smarty_block_title')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.title.php';
if (!is_callable('smarty_block_add_bread_crumb')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.add_bread_crumb.php';
if (!is_callable('smarty_block_form')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.form.php';
if (!is_callable('smarty_block_lang')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/globalization/helpers/block.lang.php';
if (!is_callable('smarty_block_wrap')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.wrap.php';
if (!is_callable('smarty_function_yes_no')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/function.yes_no.php';
if (!is_callable('smarty_block_label')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.label.php';
if (!is_callable('smarty_function_radio_field')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/function.radio_field.php';
if (!is_callable('smarty_function_text_field')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/function.text_field.php';
if (!is_callable('smarty_block_wrap_buttons')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.wrap_buttons.php';
if (!is_callable('smarty_block_submit')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.submit.php';
?><?php $_smarty_tpl->smarty->_tag_stack[] = array('title', array()); $_block_repeat=true; echo smarty_block_title(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
General Settings<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_title(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

<?php $_smarty_tpl->smarty->_tag_stack[] = array('add_bread_crumb', array()); $_block_repeat=true; echo smarty_block_add_bread_crumb(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
General Settings<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_add_bread_crumb(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>


<div id="general_settings">
  <?php $_smarty_tpl->smarty->_tag_stack[] = array('form', array('action'=>Router::assemble('admin_settings_general'),'method'=>'post')); $_block_repeat=true; echo smarty_block_form(array('action'=>Router::assemble('admin_settings_general'),'method'=>'post'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

    <div class="content_stack_wrapper">

      <?php if (!AngieApplication::isOnDemand()){?>
        <div class="content_stack_element">
          <div class="content_stack_element_info">
            <h3><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Version Checking<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h3>
          </div>
          <div class="content_stack_element_body">
            <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap', array('field'=>'help_improve_application')); $_block_repeat=true; echo smarty_block_wrap(array('field'=>'help_improve_application'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

              <?php echo smarty_function_yes_no(array('name'=>'help_improve_application','value'=>$_smarty_tpl->tpl_vars['general_data']->value['help_improve_application'],'label'=>'Help Us Improve activeCollab'),$_smarty_tpl);?>

              <p class="aid"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Select "Yes" to help us improve activeCollab by <u>sending anonymous, non-identifying usage information</u> when activeCollab checks for a new version. This information is used by our development team to make better decisions while working on future releases.<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</p>
            <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap(array('field'=>'help_improve_application'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

          </div>
        </div>
      <?php }?>

      <?php if (AngieApplication::isModuleLoaded('tracking')){?>
        <div class="content_stack_element">
          <div class="content_stack_element_info">
            <h3><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Time and Expenses<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h3>
          </div>
          <div class="content_stack_element_body">
            <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap', array('field'=>'default_billable_status')); $_block_repeat=true; echo smarty_block_wrap(array('field'=>'default_billable_status'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

              <?php $_smarty_tpl->smarty->_tag_stack[] = array('label', array()); $_block_repeat=true; echo smarty_block_label(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Default Billable Status for New Entries<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_label(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

              <div><?php echo smarty_function_radio_field(array('name'=>'default_billable_status','value'=>0,'pre_selected_value'=>$_smarty_tpl->tpl_vars['general_data']->value['default_billable_status'],'label'=>'Non-Billable'),$_smarty_tpl);?>
</div>
              <div><?php echo smarty_function_radio_field(array('name'=>'default_billable_status','value'=>1,'pre_selected_value'=>$_smarty_tpl->tpl_vars['general_data']->value['default_billable_status'],'label'=>'Billable'),$_smarty_tpl);?>
</div>
            <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap(array('field'=>'default_billable_status'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

          </div>
        </div>
      <?php }?>

      <?php if (!AngieApplication::isOnDemand()){?>
        <div class="content_stack_element last">
          <div class="content_stack_element_info">
            <h3><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Miscellaneous<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h3>
            <p class="aid"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Various settings and features<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</p>
          </div>
          <div class="content_stack_element_body">
            <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap', array('field'=>'on_logout_url')); $_block_repeat=true; echo smarty_block_wrap(array('field'=>'on_logout_url'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

              <?php $_smarty_tpl->smarty->_tag_stack[] = array('label', array()); $_block_repeat=true; echo smarty_block_label(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
When Users Log Out<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_label(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

              <div><input type="radio" name="use_on_logout_url" class="auto input_radio" value="0" id="generalUseLogoutUrlNo" <?php if (!$_smarty_tpl->tpl_vars['general_data']->value['use_on_logout_url']){?>checked="checked"<?php }?> /> <?php $_smarty_tpl->smarty->_tag_stack[] = array('label', array('for'=>'generalUseLogoutUrlNo','class'=>'inline','main_label'=>false,'after_text'=>'')); $_block_repeat=true; echo smarty_block_label(array('for'=>'generalUseLogoutUrlNo','class'=>'inline','main_label'=>false,'after_text'=>''), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Redirect them back to login page<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_label(array('for'=>'generalUseLogoutUrlNo','class'=>'inline','main_label'=>false,'after_text'=>''), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</div>
              <div><input type="radio" name="use_on_logout_url" class="auto input_radio" value="1" id="generalUseLogoutUrlYes" <?php if ($_smarty_tpl->tpl_vars['general_data']->value['use_on_logout_url']){?>checked="checked"<?php }?> /> <?php $_smarty_tpl->smarty->_tag_stack[] = array('label', array('for'=>'generalUseLogoutUrlYes','class'=>'inline','main_label'=>false,'after_text'=>'')); $_block_repeat=true; echo smarty_block_label(array('for'=>'generalUseLogoutUrlYes','class'=>'inline','main_label'=>false,'after_text'=>''), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Redirect them to a custom URL<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_label(array('for'=>'generalUseLogoutUrlYes','class'=>'inline','main_label'=>false,'after_text'=>''), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</div>
              <div id="on_logout_url_container">
                <?php echo smarty_function_text_field(array('name'=>"general[on_logout_url]",'value'=>$_smarty_tpl->tpl_vars['general_data']->value['on_logout_url'],'id'=>'on_logout_url'),$_smarty_tpl);?>

                <p class="details block"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Specify URL users will be redirected to when they log out<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</p>
              </div>
            <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap(array('field'=>'on_logout_url'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

          </div>
        </div>
      <?php }?>

    </div>
    
    <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap_buttons', array()); $_block_repeat=true; echo smarty_block_wrap_buttons(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

  	  <?php $_smarty_tpl->smarty->_tag_stack[] = array('submit', array()); $_block_repeat=true; echo smarty_block_submit(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Save Changes<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_submit(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

    <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap_buttons(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

  <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_form(array('action'=>Router::assemble('admin_settings_general'),'method'=>'post'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

</div>

<script type="text/javascript">
  $('#general_settings').each(function() {
    var wrapper = $(this);
    var on_logout_url = wrapper.find('#on_logout_url_container');

    if(wrapper.find('#generalUseLogoutUrlNo').prop('checked')) {
      on_logout_url.hide();
    } // if

    wrapper.find('#generalUseLogoutUrlNo').click(function() {
      on_logout_url.slideUp('fast');
    });

    wrapper.find('#generalUseLogoutUrlYes').click(function() {
      on_logout_url.slideDown('fast', function() {
        $(this).find('#on_logout_url').focus();
      });
    });
  });
</script><?php }} ?>