<?php /* Smarty version Smarty-3.1.12, created on 2014-10-13 07:33:26
         compiled from "C:\wamp\www\active\activecollab\4.2.6\modules\system\layouts\inline.tpl" */ ?>
<?php /*%%SmartyHeaderCode:12025543b8046848610-14450054%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '6f2c6ea042fb4f0378ef456b909d68300a0b24b6' => 
    array (
      0 => 'C:\\wamp\\www\\active\\activecollab\\4.2.6\\modules\\system\\layouts\\inline.tpl',
      1 => 1413185335,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '12025543b8046848610-14450054',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'wireframe' => 0,
    'content_for_layout' => 0,
    'request' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_543b80468b6ec8_67513366',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_543b80468b6ec8_67513366')) {function content_543b80468b6ec8_67513366($_smarty_tpl) {?><?php if (!is_callable('smarty_function_template_vars_to_js')) include 'C:\\wamp\\www\\active\\activecollab/4.2.6/angie/frameworks/environment/helpers\\function.template_vars_to_js.php';
if (!is_callable('smarty_modifier_json')) include 'C:\\wamp\\www\\active\\activecollab/4.2.6/angie/frameworks/environment/helpers\\modifier.json.php';
if (!is_callable('smarty_modifier_number')) include 'C:\\wamp\\www\\active\\activecollab/4.2.6/angie/frameworks/globalization/helpers\\modifier.number.php';
if (!is_callable('smarty_modifier_filesize')) include 'C:\\wamp\\www\\active\\activecollab/4.2.6/angie/frameworks/globalization/helpers\\modifier.filesize.php';
?><?php echo smarty_function_template_vars_to_js(array('wireframe'=>$_smarty_tpl->tpl_vars['wireframe']->value),$_smarty_tpl);?>

    
<script type="text/javascript">
  App.Wireframe.MainMenu.setCurrent(<?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['wireframe']->value->getCurrentMenuItem());?>
);
  
<?php if ($_smarty_tpl->tpl_vars['wireframe']->value->list_mode->isEnabled()){?>
  App.Wireframe.Content.enableListMode();
<?php }else{ ?>
  App.Wireframe.Content.disableListMode();
<?php }?>
  
  App.Wireframe.BreadCrumbs.batchSet(<?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['wireframe']->value->breadcrumbs);?>
);
  App.Wireframe.PageTabs.batchSet(<?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['wireframe']->value->tabs);?>
, <?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['wireframe']->value->tabs->getCurrentTab());?>
);
  
  App.Wireframe.PageTitle.set(<?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['wireframe']->value->getPageTitle());?>
);
  App.Wireframe.PageTitle.batchSetActions(<?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['wireframe']->value->actions);?>
);
  App.Wireframe.PageTitle.setPrintUrl(<?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['wireframe']->value->print->getUrl());?>
);
    
  App.Wireframe.Content.set(<?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['content_for_layout']->value);?>
, <?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['request']->value->getUrl());?>
);

<?php if (AngieApplication::isInDevelopment()||AngieApplication::isInDebugMode()){?>
  App.Wireframe.Benchmark.set({
    'execution_time' : <?php echo smarty_modifier_json(smarty_modifier_number(BenchmarkForAngie::getTimeElapsed()));?>
,
    'memory_usage' : <?php echo smarty_modifier_json(smarty_modifier_filesize(BenchmarkForAngie::getMemoryUsage()));?>
,
    'all_queries' : <?php echo smarty_modifier_json(BenchmarkForAngie::getQueries());?>
,
    'queries_count' : <?php echo smarty_modifier_json(BenchmarkForAngie::getQueriesCount());?>

  });
<?php }?>
</script><?php }} ?>