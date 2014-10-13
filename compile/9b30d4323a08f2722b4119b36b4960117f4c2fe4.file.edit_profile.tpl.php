<?php /* Smarty version Smarty-3.1.12, created on 2014-07-31 15:24:02
         compiled from "/home/abag/public_html/dev/activecollab/4.2.6/modules/system/views/default/users/edit_profile.tpl" */ ?>
<?php /*%%SmartyHeaderCode:989713677539bed138275b7-14886360%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '9b30d4323a08f2722b4119b36b4960117f4c2fe4' => 
    array (
      0 => '/home/abag/public_html/dev/activecollab/4.2.6/modules/system/views/default/users/edit_profile.tpl',
      1 => 1403889856,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '989713677539bed138275b7-14886360',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_539bed13a27ed7_82989396',
  'variables' => 
  array (
    'active_user' => 0,
    'user_data' => 0,
    'logged_user' => 0,
    'additional_email_addresses' => 0,
    'additional_email_address' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_539bed13a27ed7_82989396')) {function content_539bed13a27ed7_82989396($_smarty_tpl) {?><?php if (!is_callable('smarty_block_title')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.title.php';
if (!is_callable('smarty_block_add_bread_crumb')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.add_bread_crumb.php';
if (!is_callable('smarty_block_form')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.form.php';
if (!is_callable('smarty_block_lang')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/globalization/helpers/block.lang.php';
if (!is_callable('smarty_block_wrap')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.wrap.php';
if (!is_callable('smarty_function_text_field')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/function.text_field.php';
if (!is_callable('smarty_function_email_field')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/function.email_field.php';
if (!is_callable('smarty_block_label')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.label.php';
if (!is_callable('smarty_function_image_url')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/function.image_url.php';
if (!is_callable('smarty_function_select_im_type')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/authentication/helpers/function.select_im_type.php';
if (!is_callable('smarty_block_wrap_buttons')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.wrap_buttons.php';
if (!is_callable('smarty_block_submit')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.submit.php';
?><?php $_smarty_tpl->smarty->_tag_stack[] = array('title', array()); $_block_repeat=true; echo smarty_block_title(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Update Profile<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_title(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

<?php $_smarty_tpl->smarty->_tag_stack[] = array('add_bread_crumb', array()); $_block_repeat=true; echo smarty_block_add_bread_crumb(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Update Profile<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_add_bread_crumb(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>


<div id="edit_user_profile">
  <?php $_smarty_tpl->smarty->_tag_stack[] = array('form', array('action'=>$_smarty_tpl->tpl_vars['active_user']->value->getEditProfileUrl(),'csfr_protect'=>true)); $_block_repeat=true; echo smarty_block_form(array('action'=>$_smarty_tpl->tpl_vars['active_user']->value->getEditProfileUrl(),'csfr_protect'=>true), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

    <div class="content_stack_wrapper">
      <div class="content_stack_element odd">
        <div class="content_stack_element_info">
          <h3><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Basic Information<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h3>
        </div>
        <div class="content_stack_element_body">
          <div class="col">
            <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap', array('field'=>'first_name')); $_block_repeat=true; echo smarty_block_wrap(array('field'=>'first_name'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

              <?php echo smarty_function_text_field(array('name'=>'user[first_name]','value'=>$_smarty_tpl->tpl_vars['user_data']->value['first_name'],'id'=>'userFirstName','label'=>'First Name','required'=>true),$_smarty_tpl);?>

            <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap(array('field'=>'first_name'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

          </div>

          <div class="col">
            <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap', array('field'=>'last_name')); $_block_repeat=true; echo smarty_block_wrap(array('field'=>'last_name'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

              <?php echo smarty_function_text_field(array('name'=>'user[last_name]','value'=>$_smarty_tpl->tpl_vars['user_data']->value['last_name'],'id'=>'userLastName','label'=>'Last Name','required'=>true),$_smarty_tpl);?>

            <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap(array('field'=>'last_name'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

          </div>

          <div class="col">
            <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap', array('field'=>'title')); $_block_repeat=true; echo smarty_block_wrap(array('field'=>'title'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

              <?php echo smarty_function_text_field(array('name'=>'user[title]','value'=>$_smarty_tpl->tpl_vars['user_data']->value['title'],'id'=>'userTitle','label'=>'Title'),$_smarty_tpl);?>

            <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap(array('field'=>'title'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

          </div>
        </div>
      </div>

      <div class="content_stack_element even">
        <div class="content_stack_element_info">
          <h3><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Email Addresses<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h3>
        </div>
        <div class="content_stack_element_body">
          <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap', array('field'=>'email')); $_block_repeat=true; echo smarty_block_wrap(array('field'=>'email'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

            <?php echo smarty_function_email_field(array('name'=>'user[email]','value'=>$_smarty_tpl->tpl_vars['user_data']->value['email'],'id'=>'userEmail','disabled'=>!$_smarty_tpl->tpl_vars['active_user']->value->canChangePassword($_smarty_tpl->tpl_vars['logged_user']->value),'label'=>'Primary Email Address','required'=>true),$_smarty_tpl);?>

            <p class="aid"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Email notifications will be sent to this address<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
.</p>
          <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap(array('field'=>'email'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>


          <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap', array('field'=>'alternative_email','id'=>'alternative_user_addresses')); $_block_repeat=true; echo smarty_block_wrap(array('field'=>'alternative_email','id'=>'alternative_user_addresses'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

          <?php $_smarty_tpl->smarty->_tag_stack[] = array('label', array()); $_block_repeat=true; echo smarty_block_label(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Alternative Email Addresses<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_label(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>


          <?php if ($_smarty_tpl->tpl_vars['additional_email_addresses']->value){?>
            <?php  $_smarty_tpl->tpl_vars['additional_email_address'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['additional_email_address']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['additional_email_addresses']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['additional_email_address']->key => $_smarty_tpl->tpl_vars['additional_email_address']->value){
$_smarty_tpl->tpl_vars['additional_email_address']->_loop = true;
?>
              <div class="alternative_user_address_wrapper">
                <input name="user[additional_email_addresses][]" value="<?php echo clean($_smarty_tpl->tpl_vars['additional_email_address']->value,$_smarty_tpl);?>
" type="email"> <img src="<?php echo smarty_function_image_url(array('name'=>'icons/12x12/delete.png','module'=>@ENVIRONMENT_FRAMEWORK),$_smarty_tpl);?>
">
              </div>
            <?php } ?>
          <?php }?>

            <a href="#" id="add_alternative_user_address" class="button_add"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Add<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a>
            <p class="aid"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Alternative email addresses can be used for login, as well as for mailing in tasks, discussions and comments<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
.</p>
          <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap(array('field'=>'alternative_email','id'=>'alternative_user_addresses'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

        </div>
      </div>

      <div class="content_stack_element even">
        <div class="content_stack_element_info">
          <h3><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Contact Information<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h3>
        </div>
        <div class="content_stack_element_body">
		      <div class="col">
		      <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap', array('field'=>'phone_work')); $_block_repeat=true; echo smarty_block_wrap(array('field'=>'phone_work'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

		        <?php echo smarty_function_text_field(array('name'=>'user[phone_work]','value'=>$_smarty_tpl->tpl_vars['user_data']->value['phone_work'],'id'=>'userPhoneWork','label'=>'Office Phone Number'),$_smarty_tpl);?>

		      <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap(array('field'=>'phone_work'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

		      </div>
		      
		      <div class="col">
		      <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap', array('field'=>'phone_mobile')); $_block_repeat=true; echo smarty_block_wrap(array('field'=>'phone_mobile'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

		        <?php echo smarty_function_text_field(array('name'=>'user[phone_mobile]','value'=>$_smarty_tpl->tpl_vars['user_data']->value['phone_mobile'],'id'=>'userPhoneMobile','label'=>'Mobile Phone Number'),$_smarty_tpl);?>

		      <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap(array('field'=>'phone_mobile'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

		      </div>
          
          <div class="clear"></div>
		      
		      <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap', array('field'=>'im')); $_block_repeat=true; echo smarty_block_wrap(array('field'=>'im'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

		        <?php $_smarty_tpl->smarty->_tag_stack[] = array('label', array('for'=>'userIm')); $_block_repeat=true; echo smarty_block_label(array('for'=>'userIm'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Instant Messenger<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_label(array('for'=>'userIm'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

		        <?php echo smarty_function_select_im_type(array('name'=>'user[im_type]','value'=>$_smarty_tpl->tpl_vars['user_data']->value['im_type'],'class'=>'auto'),$_smarty_tpl);?>
 <?php echo smarty_function_text_field(array('name'=>'user[im_value]','value'=>$_smarty_tpl->tpl_vars['user_data']->value['im_value'],'id'=>'userIm'),$_smarty_tpl);?>

		      <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap(array('field'=>'im'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

        </div>
      </div>
    </div>
    
    <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap_buttons', array()); $_block_repeat=true; echo smarty_block_wrap_buttons(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

    	<?php $_smarty_tpl->smarty->_tag_stack[] = array('submit', array()); $_block_repeat=true; echo smarty_block_submit(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Save Changes<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_submit(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

    <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap_buttons(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

  <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_form(array('action'=>$_smarty_tpl->tpl_vars['active_user']->value->getEditProfileUrl(),'csfr_protect'=>true), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

</div>

<script type="text/javascript">
  $('#edit_user_profile').each(function() {
    var wrapper = $(this);

    var alternative_addresses_wrapper = wrapper.find('#alternative_user_addresses');

    alternative_addresses_wrapper.on('click', 'a#add_alternative_user_address', function() {
      var last_input = alternative_addresses_wrapper.find('div.alternative_user_address_wrapper:last');

      var to_append = '<div class="alternative_user_address_wrapper">' +
        '<input name="user[additional_email_addresses][]" type="email"> <img src="' + App.Wireframe.Utils.imageUrl('icons/12x12/delete.png', 'environment') + '">' +
      '</div>';

      if(last_input.length > 0) {
        last_input.after(to_append);
      } else {
        alternative_addresses_wrapper.find('label').after(to_append);
      } // if

      alternative_addresses_wrapper.find('div.alternative_user_address_wrapper:last input').focus();

      if(alternative_addresses_wrapper.find('div.alternative_user_address_wrapper').length >= 5) {
        alternative_addresses_wrapper.find('a#add_alternative_user_address').hide();
      } // if

      return false;
    });

    alternative_addresses_wrapper.on('click', 'div.alternative_user_address_wrapper img', function() {
      $(this).parent().remove();

      if(alternative_addresses_wrapper.find('div.alternative_user_address_wrapper').length < 5) {
        alternative_addresses_wrapper.find('a#add_alternative_user_address').show();
      } // if
    });
  });
</script><?php }} ?>