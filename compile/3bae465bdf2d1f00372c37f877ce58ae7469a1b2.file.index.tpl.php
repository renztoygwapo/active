<?php /* Smarty version Smarty-3.1.12, created on 2014-06-25 16:25:14
         compiled from "/home/abag/public_html/dev/activecollab/4.2.6/modules/system/views/default/projects/index.tpl" */ ?>
<?php /*%%SmartyHeaderCode:109412134253aaf7ea08b499-31630160%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '3bae465bdf2d1f00372c37f877ce58ae7469a1b2' => 
    array (
      0 => '/home/abag/public_html/dev/activecollab/4.2.6/modules/system/views/default/projects/index.tpl',
      1 => 1403109852,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '109412134253aaf7ea08b499-31630160',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'can_add_project' => 0,
    'manage_categories_url' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_53aaf7ea18c8c9_15260447',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_53aaf7ea18c8c9_15260447')) {function content_53aaf7ea18c8c9_15260447($_smarty_tpl) {?><?php if (!is_callable('smarty_block_title')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.title.php';
if (!is_callable('smarty_block_add_bread_crumb')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.add_bread_crumb.php';
if (!is_callable('smarty_block_lang')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/globalization/helpers/block.lang.php';
if (!is_callable('smarty_function_image_url')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/function.image_url.php';
if (!is_callable('smarty_function_assemble')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/function.assemble.php';
?><?php $_smarty_tpl->smarty->_tag_stack[] = array('title', array()); $_block_repeat=true; echo smarty_block_title(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Projects<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_title(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

<?php $_smarty_tpl->smarty->_tag_stack[] = array('add_bread_crumb', array()); $_block_repeat=true; echo smarty_block_add_bread_crumb(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
All<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_add_bread_crumb(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>


<div id="projects">
  <div class="empty_content">
      <div class="objects_list_title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Projects<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</div>
      <div class="objects_list_icon"><img src="<?php echo smarty_function_image_url(array('name'=>'icons/48x48/projects.png','module'=>@SYSTEM_MODULE),$_smarty_tpl);?>
" alt=""/></div>

      <div class="objects_list_details_actions">
        <?php if ($_smarty_tpl->tpl_vars['can_add_project']->value||$_smarty_tpl->tpl_vars['manage_categories_url']->value){?>
          <ul>
            <?php if ($_smarty_tpl->tpl_vars['can_add_project']->value){?><li><a href="<?php echo smarty_function_assemble(array('route'=>'projects_add'),$_smarty_tpl);?>
" id="new_project"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
New Project<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a></li><?php }?>
            <?php if ($_smarty_tpl->tpl_vars['manage_categories_url']->value){?><li><a href="<?php echo clean($_smarty_tpl->tpl_vars['manage_categories_url']->value,$_smarty_tpl);?>
" class="manage_objects_list_categories" title="<?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Manage Project Categories<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Manage Categories<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a></li><?php }?>
          </ul>
        <?php }?>
      </div>

      <?php if ($_smarty_tpl->tpl_vars['can_add_project']->value){?>
        <div class="object_list_details_additional_actions">
          <a href="<?php echo smarty_function_assemble(array('route'=>'projects_archive'),$_smarty_tpl);?>
" id="view_archive"><span><img src="<?php echo smarty_function_image_url(array('name'=>"icons/12x12/archive.png",'module'=>"environment"),$_smarty_tpl);?>
"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Browse Archive<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</span></a>
        </div>
      <?php }?>

      <div class="object_lists_details_tips">
        <h3><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Tips<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</h3>
        <ul>
          <li><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
To select a project and load its details, please click on it in the list on the left<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</li>
          <li><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
It is possible to select multiple projects at the same time. Just hold Ctrl key on your keyboard and click on all the projects that you want to select<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</li>
        </ul>
      </div>
  </div>
</div>

<?php echo $_smarty_tpl->getSubTemplate (get_view_path('_initialize_objects_list','projects',@SYSTEM_MODULE), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
<?php }} ?>