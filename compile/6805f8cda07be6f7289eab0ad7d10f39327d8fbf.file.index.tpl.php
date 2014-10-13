<?php /* Smarty version Smarty-3.1.12, created on 2014-07-31 15:20:33
         compiled from "/home/abag/public_html/dev/activecollab/4.2.6/modules/system/views/default/identity_admin/index.tpl" */ ?>
<?php /*%%SmartyHeaderCode:96613475453bf601176c746-49127699%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '6805f8cda07be6f7289eab0ad7d10f39327d8fbf' => 
    array (
      0 => '/home/abag/public_html/dev/activecollab/4.2.6/modules/system/views/default/identity_admin/index.tpl',
      1 => 1406740203,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '96613475453bf601176c746-49127699',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_53bf6011983a07_02789199',
  'variables' => 
  array (
    'settings_data' => 0,
    'large_logo_url' => 0,
    'revert_logo_url' => 0,
    'small_logo_url' => 0,
    'medium_logo_url' => 0,
    'larger_logo_url' => 0,
    'photo_logo_url' => 0,
    'login_page_logo' => 0,
    'revert_login_logo_url' => 0,
    'favicon_url' => 0,
    'revert_favicon_url' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_53bf6011983a07_02789199')) {function content_53bf6011983a07_02789199($_smarty_tpl) {?><?php if (!is_callable('smarty_block_title')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.title.php';
if (!is_callable('smarty_block_add_bread_crumb')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.add_bread_crumb.php';
if (!is_callable('smarty_function_use_widget')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/function.use_widget.php';
if (!is_callable('smarty_block_form')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.form.php';
if (!is_callable('smarty_block_lang')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/globalization/helpers/block.lang.php';
if (!is_callable('smarty_block_wrap')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.wrap.php';
if (!is_callable('smarty_function_text_field')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/function.text_field.php';
if (!is_callable('smarty_block_textarea_field')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.textarea_field.php';
if (!is_callable('smarty_function_yes_no')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/function.yes_no.php';
if (!is_callable('smarty_block_wrap_buttons')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.wrap_buttons.php';
if (!is_callable('smarty_block_submit')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.submit.php';
?><?php $_smarty_tpl->smarty->_tag_stack[] = array('title', array()); $_block_repeat=true; echo smarty_block_title(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Identity<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_title(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

<?php $_smarty_tpl->smarty->_tag_stack[] = array('add_bread_crumb', array()); $_block_repeat=true; echo smarty_block_add_bread_crumb(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Identity Settings<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_add_bread_crumb(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

<?php echo smarty_function_use_widget(array('name'=>"form",'module'=>"environment"),$_smarty_tpl);?>


<div id="identity_admin">
  <?php $_smarty_tpl->smarty->_tag_stack[] = array('form', array('action'=>Router::assemble('identity_admin'),'method'=>"post",'enctype'=>"multipart/form-data")); $_block_repeat=true; echo smarty_block_form(array('action'=>Router::assemble('identity_admin'),'method'=>"post",'enctype'=>"multipart/form-data"), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

    <div class="content_stack_wrapper">

      <div class="content_stack_element">
        <div class="content_stack_element_info">
          <h3><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
General<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h3>
        </div>
        <div class="content_stack_element_body">
          <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap', array('field'=>'identity_name')); $_block_repeat=true; echo smarty_block_wrap(array('field'=>'identity_name'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

          	<?php echo smarty_function_text_field(array('name'=>"settings[identity_name]",'value'=>$_smarty_tpl->tpl_vars['settings_data']->value['identity_name'],'label'=>'System Name'),$_smarty_tpl);?>

          	<p class="aid"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Name your project collaboration system. This name will be used as prefix for title of all pages, as well as Welcome home screen widget<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</p>
          <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap(array('field'=>'identity_name'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>


          <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap', array('field'=>'rep_site_domain')); $_block_repeat=true; echo smarty_block_wrap(array('field'=>'rep_site_domain'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

            <?php echo smarty_function_text_field(array('name'=>"settings[rep_site_domain]",'value'=>$_smarty_tpl->tpl_vars['settings_data']->value['rep_site_domain'],'label'=>'Default Rep Site Domain'),$_smarty_tpl);?>

            <p class="aid"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Repsite Domain Name eg: abuckagallon.com<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</p>
          <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap(array('field'=>'rep_site_domain'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

          
        </div>
      </div>

      <div class="content_stack_element">
        <div class="content_stack_element_info">
          <h3><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Welcome Message<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h3>
          <p class="aid"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Set up welcome message that is displayed to your clients on their Dashboard page<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</p>
        </div>
        <div class="content_stack_element_body">
          <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap', array('field'=>'identity_client_welcome_message')); $_block_repeat=true; echo smarty_block_wrap(array('field'=>'identity_client_welcome_message'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

            <?php $_smarty_tpl->smarty->_tag_stack[] = array('textarea_field', array('name'=>"settings[identity_client_welcome_message]",'id'=>'identity_admin_welcome_message','label'=>'Welcome Message')); $_block_repeat=true; echo smarty_block_textarea_field(array('name'=>"settings[identity_client_welcome_message]",'id'=>'identity_admin_welcome_message','label'=>'Welcome Message'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo $_smarty_tpl->tpl_vars['settings_data']->value['identity_client_welcome_message'];?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_textarea_field(array('name'=>"settings[identity_client_welcome_message]",'id'=>'identity_admin_welcome_message','label'=>'Welcome Message'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

            <p class="aid"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Welcome message that is displayed to your clients<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
. <?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
New lines will be preserved. HTML is not allowed<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
.</p>
          <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap(array('field'=>'identity_client_welcome_message'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>


          <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap', array('field'=>'identity_nidentity_logo_on_whiteame')); $_block_repeat=true; echo smarty_block_wrap(array('field'=>'identity_nidentity_logo_on_whiteame'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

            <?php echo smarty_function_yes_no(array('name'=>"settings[identity_logo_on_white]",'value'=>$_smarty_tpl->tpl_vars['settings_data']->value['identity_logo_on_white'],'label'=>'Put Our Logo on the White Background'),$_smarty_tpl);?>

          <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap(array('field'=>'identity_nidentity_logo_on_whiteame'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

        </div>
      </div>

      <div class="content_stack_element">
        <div class="content_stack_element_info">
          <h3><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
System Logo<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h3>
        </div>
        <div class="content_stack_element_body">
          <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap', array('field'=>'identity_logo')); $_block_repeat=true; echo smarty_block_wrap(array('field'=>'identity_logo'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

            <table cellspacing="0" class="logo_table">
              <tr>
                <td class="logo_cell">
                  <img src="<?php echo clean($_smarty_tpl->tpl_vars['large_logo_url']->value,$_smarty_tpl);?>
" alt="<?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Logo<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" original_image="<?php echo clean($_smarty_tpl->tpl_vars['large_logo_url']->value,$_smarty_tpl);?>
">
                </td>
                <td class="logo_input">
                  <input type="file" name="logo" /><br /><a href="<?php echo clean($_smarty_tpl->tpl_vars['revert_logo_url']->value,$_smarty_tpl);?>
" class="revert_link"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Revert to default image<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a>
                </td>
              </tr>
            </table>
            <p class="aid"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array('small_logo_url'=>$_smarty_tpl->tpl_vars['small_logo_url']->value,'medium_logo_url'=>$_smarty_tpl->tpl_vars['medium_logo_url']->value,'large_logo_url'=>$_smarty_tpl->tpl_vars['large_logo_url']->value,'larger_logo_url'=>$_smarty_tpl->tpl_vars['larger_logo_url']->value,'photo_logo_url'=>$_smarty_tpl->tpl_vars['photo_logo_url']->value)); $_block_repeat=true; echo smarty_block_lang(array('small_logo_url'=>$_smarty_tpl->tpl_vars['small_logo_url']->value,'medium_logo_url'=>$_smarty_tpl->tpl_vars['medium_logo_url']->value,'large_logo_url'=>$_smarty_tpl->tpl_vars['large_logo_url']->value,'larger_logo_url'=>$_smarty_tpl->tpl_vars['larger_logo_url']->value,'photo_logo_url'=>$_smarty_tpl->tpl_vars['photo_logo_url']->value), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
This logo is used in email notifications, welcome messages and more. It is saved in four sizes: <a href=":small_logo_url" target="_blank">16x16px</a>, <a href=":medium_logo_url" target="_blank">40x40px</a>, <a href=":large_logo_url" target="_blank">80x80px</a>, <a href=":larger_logo_url" target="_blank">128x128</a> and <a href=":photo_logo_url" target="_blank">256x256px</a>. System uses different sizes for different purposes, depending on the need<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array('small_logo_url'=>$_smarty_tpl->tpl_vars['small_logo_url']->value,'medium_logo_url'=>$_smarty_tpl->tpl_vars['medium_logo_url']->value,'large_logo_url'=>$_smarty_tpl->tpl_vars['large_logo_url']->value,'larger_logo_url'=>$_smarty_tpl->tpl_vars['larger_logo_url']->value,'photo_logo_url'=>$_smarty_tpl->tpl_vars['photo_logo_url']->value), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</p>
          <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap(array('field'=>'identity_logo'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

        </div>
      </div>

      <div class="content_stack_element">
        <div class="content_stack_element_info">
          <h3><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Login Page Logo<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h3>
        </div>
        <div class="content_stack_element_body">
          <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap', array('field'=>'login_page_logo')); $_block_repeat=true; echo smarty_block_wrap(array('field'=>'login_page_logo'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

            <table cellspacing="0" class="logo_table">
              <tr>
                <td class="logo_cell">
                  <img src="<?php echo clean($_smarty_tpl->tpl_vars['login_page_logo']->value,$_smarty_tpl);?>
" alt="<?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Logo<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" original_image="<?php echo clean($_smarty_tpl->tpl_vars['login_page_logo']->value,$_smarty_tpl);?>
">
                </td>
                <td class="logo_input">
                  <input type="file" name="login_page_logo" /><br /><a href="<?php echo clean($_smarty_tpl->tpl_vars['revert_login_logo_url']->value,$_smarty_tpl);?>
" class="revert_link"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Revert to default image<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a>
                </td>
              </tr>
            </table>
            <p class="aid"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
This logo will be used only on login and reset password pages. We recommend transparent PNG file with dimensions 256x256px. If image is not that size it will be constrained to those dimensions.<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</p>
          <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap(array('field'=>'login_page_logo'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

        </div>
      </div>

      <div class="content_stack_element">
        <div class="content_stack_element_info">
          <h3><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Favicon<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h3>
        </div>
        <div class="content_stack_element_body">
          <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap', array('field'=>'favicon')); $_block_repeat=true; echo smarty_block_wrap(array('field'=>'favicon'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

            <table cellspacing="0" class="logo_table">
              <tr>
                <td class="logo_cell">
                  <img src="<?php echo clean($_smarty_tpl->tpl_vars['favicon_url']->value,$_smarty_tpl);?>
" alt="<?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Logo<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" original_image="<?php echo clean($_smarty_tpl->tpl_vars['favicon_url']->value,$_smarty_tpl);?>
">
                </td>
                <td class="logo_input">
                  <input type="file" name="favicon" /><br /><a href="<?php echo clean($_smarty_tpl->tpl_vars['revert_favicon_url']->value,$_smarty_tpl);?>
" class="revert_link"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Revert to default image<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a>
                </td>
              </tr>
            </table>
            <p class="aid"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Image has to be 16x16px, and file type has to be <strong>ICO</strong>.<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</p>
          <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap(array('field'=>'favicon'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

        </div>
      </div>

    </div>
    
    <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap_buttons', array()); $_block_repeat=true; echo smarty_block_wrap_buttons(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

  	  <?php $_smarty_tpl->smarty->_tag_stack[] = array('submit', array()); $_block_repeat=true; echo smarty_block_submit(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Save Changes<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_submit(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

    <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap_buttons(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

  <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_form(array('action'=>Router::assemble('identity_admin'),'method'=>"post",'enctype'=>"multipart/form-data"), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

</div>

<script type="text/javascript">
  var wrapper = $('#identity_admin');

  wrapper.find('.revert_link').click(function () {
    var anchor = $(this);
    var image = anchor.parents('table:first').find('td:first img');

    if (anchor.is('.in_progress')) {
      return false;
    } // if

    anchor.addClass('in_progress').html(App.lang('Reverting') + ' ...');

    $.ajax({
      'url'     : App.extendUrl(anchor.attr('href')),
      'type'    : 'post',
      'data'    : { 'submitted' : 'submitted' },
      'complete'  : function () {
        anchor.html(App.lang('Revert to default image')).removeClass('in_progress');
      },
      'success' : function (success) {
        image.attr('src', App.extendUrl(image.attr('src'), { 'timestamp' :$.now() }));
      }
    });

    return false;
  });
</script><?php }} ?>