<?php /* Smarty version Smarty-3.1.12, created on 2014-10-13 07:33:43
         compiled from "C:\wamp\www\active\activecollab\4.2.6\modules\system\layouts\single.tpl" */ ?>
<?php /*%%SmartyHeaderCode:376543b80572141f8-95341653%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '44db75d66609840a92c1f0d1bcd389d441bb3fb6' => 
    array (
      0 => 'C:\\wamp\\www\\active\\activecollab\\4.2.6\\modules\\system\\layouts\\single.tpl',
      1 => 1413185335,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '376543b80572141f8-95341653',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'request' => 0,
    'wireframe' => 0,
    'content_for_layout' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_543b8057266032_09143537',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_543b8057266032_09143537')) {function content_543b8057266032_09143537($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_json')) include 'C:\\wamp\\www\\active\\activecollab/4.2.6/angie/frameworks/environment/helpers\\modifier.json.php';
if (!is_callable('smarty_modifier_number')) include 'C:\\wamp\\www\\active\\activecollab/4.2.6/angie/frameworks/globalization/helpers\\modifier.number.php';
if (!is_callable('smarty_modifier_filesize')) include 'C:\\wamp\\www\\active\\activecollab/4.2.6/angie/frameworks/globalization/helpers\\modifier.filesize.php';
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