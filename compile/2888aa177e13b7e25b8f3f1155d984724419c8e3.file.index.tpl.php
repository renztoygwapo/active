<?php /* Smarty version Smarty-3.1.12, created on 2014-06-18 17:11:43
         compiled from "/home/abag/public_html/dev/activecollab/4.2.6/modules/system/views/default/company_projects/index.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1697090709539bec83b25df0-16456129%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '2888aa177e13b7e25b8f3f1155d984724419c8e3' => 
    array (
      0 => '/home/abag/public_html/dev/activecollab/4.2.6/modules/system/views/default/company_projects/index.tpl',
      1 => 1403109852,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1697090709539bec83b25df0-16456129',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_539bec83c4d603_12042782',
  'variables' => 
  array (
    'projects' => 0,
    'is_archive' => 0,
    'project' => 0,
    'projects_toggle_url' => 0,
    'projects_toggle_text' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_539bec83c4d603_12042782')) {function content_539bec83c4d603_12042782($_smarty_tpl) {?><?php if (!is_callable('smarty_block_title')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.title.php';
if (!is_callable('smarty_block_add_bread_crumb')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.add_bread_crumb.php';
if (!is_callable('smarty_block_lang')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/globalization/helpers/block.lang.php';
if (!is_callable('smarty_function_project_link')) include '/home/abag/public_html/dev/activecollab/4.2.6/modules/system/helpers/function.project_link.php';
if (!is_callable('smarty_modifier_date')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/globalization/helpers/modifier.date.php';
if (!is_callable('smarty_function_object_label')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/labels/helpers/function.object_label.php';
?><?php $_smarty_tpl->smarty->_tag_stack[] = array('title', array()); $_block_repeat=true; echo smarty_block_title(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Company Projects<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_title(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

<?php $_smarty_tpl->smarty->_tag_stack[] = array('add_bread_crumb', array()); $_block_repeat=true; echo smarty_block_add_bread_crumb(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Company Projects<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_add_bread_crumb(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>


<div id="company_projects">
<?php if ($_smarty_tpl->tpl_vars['projects']->value){?>
  <table class="active_projects common" cellspacing="0">
    <tr>
      <th class="icon"></th>
      <th class="name"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Project<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</th>
      <?php if ($_smarty_tpl->tpl_vars['is_archive']->value){?>
      <th><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Completed on<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</th>
      <?php }?>
      <th class="label"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Label<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</th>
    </tr>
    <?php  $_smarty_tpl->tpl_vars['project'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['project']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['projects']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['project']->key => $_smarty_tpl->tpl_vars['project']->value){
$_smarty_tpl->tpl_vars['project']->_loop = true;
?>
      <tr <?php if ($_smarty_tpl->tpl_vars['project']->value->complete()->isCompleted()){?>class="completed"<?php }?>>
        <td class="icon"><img src="<?php echo clean($_smarty_tpl->tpl_vars['project']->value->avatar()->getUrl(IProjectAvatarImplementation::SIZE_SMALL),$_smarty_tpl);?>
" alt="" /></td>
        <td class="name quick_view_item"><?php echo smarty_function_project_link(array('project'=>$_smarty_tpl->tpl_vars['project']->value),$_smarty_tpl);?>
</td>
        <?php if ($_smarty_tpl->tpl_vars['is_archive']->value){?>
          <td class="completed_on"><?php echo clean(smarty_modifier_date($_smarty_tpl->tpl_vars['project']->value->getCompletedOn()),$_smarty_tpl);?>
</td>
        <?php }?>
        <td class="label"><?php echo smarty_function_object_label(array('object'=>$_smarty_tpl->tpl_vars['project']->value),$_smarty_tpl);?>
</td>
      </tr>
    <?php } ?>
  </table>
<?php }else{ ?>
  <p class="empty_page"><span class="inner"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
There are no projects<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</span></p>
<?php }?>
  <p class="projects_status_toggle"><a id="projects_url_toggle" href="<?php echo clean($_smarty_tpl->tpl_vars['projects_toggle_url']->value,$_smarty_tpl);?>
"><?php echo clean($_smarty_tpl->tpl_vars['projects_toggle_text']->value,$_smarty_tpl);?>
</a></p>
</div>

<script type="text/javascript">
  $('#company_projects').each(function() {
    var wrapper = $(this);
    var inline_tabs = wrapper.parents('.inline_tabs:first');

    if (inline_tabs.length) {
      var tabs_id = inline_tabs.attr('id');
      var projects_tab = inline_tabs.find('div.inline_tabs_links ul li a.selected');
      var projects_toggle_link = $('#projects_url_toggle');
      var projects_page = projects_toggle_link.attr('href');

      // toggle archive/active projects
      projects_toggle_link.click(function () {
        projects_tab.attr('href', projects_page).click();
        return false;
      });

      // refresh tabs on project update
      App.Wireframe.Events.bind('project_created.inline_tab project_updated.inline_tab project_deleted.inline_tab', function (event, invoice) {
        App.widgets.InlineTabs.refresh(tabs_id);
      });
    } // if
  });
</script><?php }} ?>