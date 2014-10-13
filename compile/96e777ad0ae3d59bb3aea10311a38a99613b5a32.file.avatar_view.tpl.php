<?php /* Smarty version Smarty-3.1.12, created on 2014-09-29 03:59:49
         compiled from "/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/avatar/views/default/fw_avatar/avatar_view.tpl" */ ?>
<?php /*%%SmartyHeaderCode:18637294255428d935cf0d52-10552999%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '96e777ad0ae3d59bb3aea10311a38a99613b5a32' => 
    array (
      0 => '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/avatar/views/default/fw_avatar/avatar_view.tpl',
      1 => 1403109851,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '18637294255428d935cf0d52-10552999',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'active_object' => 0,
    'gd_library_loaded' => 0,
    'widget_id' => 0,
    'current_avatar' => 0,
    'default_avatar' => 0,
    'original_url' => 0,
    'event_name' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_5428d936067033_52689023',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5428d936067033_52689023')) {function content_5428d936067033_52689023($_smarty_tpl) {?><?php if (!is_callable('smarty_block_title')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.title.php';
if (!is_callable('smarty_block_add_bread_crumb')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.add_bread_crumb.php';
if (!is_callable('smarty_function_use_widget')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/function.use_widget.php';
if (!is_callable('smarty_block_lang')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/globalization/helpers/block.lang.php';
if (!is_callable('smarty_block_button')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.button.php';
if (!is_callable('smarty_modifier_json')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/modifier.json.php';
?><?php $_smarty_tpl->smarty->_tag_stack[] = array('title', array('title'=>$_smarty_tpl->tpl_vars['active_object']->value->avatar()->getAvatarLabelName())); $_block_repeat=true; echo smarty_block_title(array('title'=>$_smarty_tpl->tpl_vars['active_object']->value->avatar()->getAvatarLabelName()), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Update :title<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_title(array('title'=>$_smarty_tpl->tpl_vars['active_object']->value->avatar()->getAvatarLabelName()), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

<?php $_smarty_tpl->smarty->_tag_stack[] = array('add_bread_crumb', array()); $_block_repeat=true; echo smarty_block_add_bread_crumb(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
View<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_add_bread_crumb(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

<?php echo smarty_function_use_widget(array('name'=>"avatar_dialog",'module'=>"avatar"),$_smarty_tpl);?>


<?php if ($_smarty_tpl->tpl_vars['gd_library_loaded']->value){?>
  <div class="fw_avatar_container" id="<?php echo clean($_smarty_tpl->tpl_vars['widget_id']->value,$_smarty_tpl);?>
">
    <table class="fw_avatar_table">
      <tr>
        <td class="fw_current_avatar_image left_container">
          <img src="<?php echo clean($_smarty_tpl->tpl_vars['current_avatar']->value,$_smarty_tpl);?>
" alt="<?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Current Avatar<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" class="current_avatar"/>
        </td>
        <td class="fw_avatar_actions right_container">
          <ul class="action_list">
            <li>
              <a href="<?php echo clean($_smarty_tpl->tpl_vars['active_object']->value->avatar()->getUploadUrl(),$_smarty_tpl);?>
" class="fw_avatar_action_upload_new_picture"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Upload New Picture<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a>
              <form action="<?php echo clean($_smarty_tpl->tpl_vars['active_object']->value->avatar()->getUploadUrl(),$_smarty_tpl);?>
" method="post" class="hidden_upload_form" enctype="multipart/form-data">
                <input type="file" name="avatar">
              </form>
            </li>
            <?php if ($_smarty_tpl->tpl_vars['active_object']->value->avatar()->resize_mode=='crop'){?>
              <li><a href="<?php echo clean($_smarty_tpl->tpl_vars['active_object']->value->avatar()->getEditUrl(),$_smarty_tpl);?>
" class="fw_avatar_action_crop_picture"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Crop Picture<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a></li>
            <?php }?>
            <li><a href="<?php echo clean($_smarty_tpl->tpl_vars['active_object']->value->avatar()->getRemoveUrl(),$_smarty_tpl);?>
" class="fw_avatar_action_reset_to_default_picture"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Reset to Default Picture<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a></li>
          </ul>
        </td>
      </tr>
    </table>

    <div class="fw_crop_widget">
      <div class="fw_crop_widget_wrapper">
      </div>

      <p><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Select part of the image you want to use as avatar<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</p>
      <div class="fw_crop_buttons">
        <?php $_smarty_tpl->smarty->_tag_stack[] = array('button', array()); $_block_repeat=true; echo smarty_block_button(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Save<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_button(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 <?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
or<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 <a href="#"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Cancel<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a>
      </div>
    </div>

  </div>

  <script type="text/javascript">
    App.widgets.AvatarDialog.init(<?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['widget_id']->value);?>
, {
      default_avatar : <?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['default_avatar']->value);?>
,
      original_avatar : <?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['original_url']->value);?>
,
      event_name : <?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['event_name']->value);?>
,
      is_default : <?php if ($_smarty_tpl->tpl_vars['default_avatar']->value==$_smarty_tpl->tpl_vars['current_avatar']->value){?>1<?php }else{ ?>0<?php }?>
    });
  </script>
<?php }else{ ?>
  <p class="empty_page"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<a href="http://ch2.php.net/manual/en/book.image.php" target="_blank">GD library</a> that is needed for resizing images is not installed<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
.<br /><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Please contact your web server administrator<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
.</p>
<?php }?><?php }} ?>