<?php /* Smarty version Smarty-3.1.12, created on 2014-06-18 17:11:42
         compiled from "/home/abag/public_html/dev/activecollab/4.2.6/modules/system/layouts/single.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1914757634539bec8278f7a9-70856273%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'df145cea6194430cbed2dff6afbd9c788150db9e' => 
    array (
      0 => '/home/abag/public_html/dev/activecollab/4.2.6/modules/system/layouts/single.tpl',
      1 => 1403109852,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1914757634539bec8278f7a9-70856273',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_539bec82841da8_96887383',
  'variables' => 
  array (
    'request' => 0,
    'wireframe' => 0,
    'content_for_layout' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_539bec82841da8_96887383')) {function content_539bec82841da8_96887383($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_json')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/modifier.json.php';
if (!is_callable('smarty_modifier_number')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/globalization/helpers/modifier.number.php';
if (!is_callable('smarty_modifier_filesize')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/globalization/helpers/modifier.filesize.php';
?><?php if (!$_smarty_tpl->tpl_vars['request']->value->isAsyncCall()){?>
  <script type="text/javascript">
    App.Wireframe.PageTitle.set(<?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['wireframe']->value->getPageTitle());?>
);
    App.Wireframe.PageTitle.batchSetActions(<?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['wireframe']->value->actions);?>
);

    App.Wireframe.PageTitle.setPrintUrl('<?php echo clean($_smarty_tpl->tpl_vars['wireframe']->value->print->getUrl(),$_smarty_tpl);?>
');
    
    App.Wireframe.BreadCrumbs.batchSet(<?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['wireframe']->value->breadcrumbs);?>
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
  </script>
<?php }?>

<?php echo $_smarty_tpl->tpl_vars['content_for_layout']->value;?>
<?php }} ?>