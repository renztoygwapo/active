<?php /* Smarty version Smarty-3.1.12, created on 2014-10-03 05:46:32
         compiled from "C:\wamp\www\dev\activecollab\4.2.6\modules\system\views\default\users\view.tpl" */ ?>
<?php /*%%SmartyHeaderCode:24499542e3838bcc602-25826204%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a433504bf0fe829d23af5fa7cd81169c0f63f499' => 
    array (
      0 => 'C:\\wamp\\www\\dev\\activecollab\\4.2.6\\modules\\system\\views\\default\\users\\view.tpl',
      1 => 1406740594,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '24499542e3838bcc602-25826204',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'active_user' => 0,
    'logged_user' => 0,
    'personality_type' => 0,
    'request' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_542e3838dd3f01_85225688',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_542e3838dd3f01_85225688')) {function content_542e3838dd3f01_85225688($_smarty_tpl) {?><?php if (!is_callable('smarty_block_add_bread_crumb')) include 'C:\\wamp\\www\\dev\\activecollab/4.2.6/angie/frameworks/environment/helpers\\block.add_bread_crumb.php';
if (!is_callable('smarty_block_object')) include 'C:\\wamp\\www\\dev\\activecollab/4.2.6/angie/frameworks/environment/helpers\\block.object.php';
if (!is_callable('smarty_function_inline_tabs')) include 'C:\\wamp\\www\\dev\\activecollab/4.2.6/angie/frameworks/environment/helpers\\function.inline_tabs.php';
if (!is_callable('smarty_function_select_personality_type')) include 'C:\\wamp\\www\\dev\\activecollab/4.2.6/modules/system/helpers\\function.select_personality_type.php';
?><?php $_smarty_tpl->smarty->_tag_stack[] = array('add_bread_crumb', array()); $_block_repeat=true; echo smarty_block_add_bread_crumb(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Profile<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_add_bread_crumb(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>


<?php $_smarty_tpl->smarty->_tag_stack[] = array('object', array('object'=>$_smarty_tpl->tpl_vars['active_user']->value,'user'=>$_smarty_tpl->tpl_vars['logged_user']->value)); $_block_repeat=true; echo smarty_block_object(array('object'=>$_smarty_tpl->tpl_vars['active_user']->value,'user'=>$_smarty_tpl->tpl_vars['logged_user']->value), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

  <div class="wireframe_content_wrapper">
    <?php echo smarty_function_inline_tabs(array('object'=>$_smarty_tpl->tpl_vars['active_user']->value),$_smarty_tpl);?>

  </div>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_object(array('object'=>$_smarty_tpl->tpl_vars['active_user']->value,'user'=>$_smarty_tpl->tpl_vars['logged_user']->value), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>


<?php if (!empty($_smarty_tpl->tpl_vars['personality_type']->value)){?>
  <?php echo smarty_function_select_personality_type(array('value'=>$_smarty_tpl->tpl_vars['personality_type']->value,'id'=>"personality_type_hidden",'render_type'=>"hidden"),$_smarty_tpl);?>

<?php }?>


<script type="text/javascript">
  
  $(document).ready(function(){
    var personality_type = $('#personality_type_hidden').val();
    if(typeof personality_type_hidden != 'undefined') {
      var _html =  "<div class='property'><div class='label'>Personality Type</div><div class='content'>"+personality_type +"</div></div>";
      $('.vcard_data .properties').append(_html);
    }
  });

  App.Wireframe.Events.bind('user_updated.<?php echo clean($_smarty_tpl->tpl_vars['request']->value->getEventScope(),$_smarty_tpl);?>
', function (event, user) {
    if(user['class'] == 'User' && user.id == '<?php echo clean($_smarty_tpl->tpl_vars['active_user']->value->getId(),$_smarty_tpl);?>
') {
    	// update avatar on the profile page
      $('#user_page_' + user.id + ' #select_user_icon .properties_icon').attr('src', user.avatar.photo);

      // if user is changing their own avatar, update the image at the bottom left corner
      if ('<?php echo clean($_smarty_tpl->tpl_vars['active_user']->value->getId(),$_smarty_tpl);?>
' == '<?php echo clean($_smarty_tpl->tpl_vars['logged_user']->value->getId(),$_smarty_tpl);?>
') {
        $('#menu_item_profile img').attr('src', user.avatar.large);
        $('#menu_item_profile span.label').html(user.display_name);
      } // if
    } // if
  });
</script><?php }} ?>