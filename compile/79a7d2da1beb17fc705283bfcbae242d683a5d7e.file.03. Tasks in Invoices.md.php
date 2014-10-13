<?php /* Smarty version Smarty-3.1.12, created on 2014-10-04 12:02:13
         compiled from "C:\wamp\www\dev\activecollab\4.2.6\modules\system\help\whats_new\4.1.0\03. Tasks in Invoices.md" */ ?>
<?php /*%%SmartyHeaderCode:1036542fe1c52e61b5-65465690%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '79a7d2da1beb17fc705283bfcbae242d683a5d7e' => 
    array (
      0 => 'C:\\wamp\\www\\dev\\activecollab\\4.2.6\\modules\\system\\help\\whats_new\\4.1.0\\03. Tasks in Invoices.md',
      1 => 1403109852,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1036542fe1c52e61b5-65465690',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_542fe1c5357652_93007477',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_542fe1c5357652_93007477')) {function content_542fe1c5357652_93007477($_smarty_tpl) {?>*Title: Tasks in Invoices
*Slug: tasks-in-invoices

================================================================

In the last few weeks, the most popular feature request was to be able **display task names in the invoices generated from reports**. Being good listeners and open to customer suggestions on how to improve activeCollab, we have made this possible. 

<?php $_smarty_tpl->smarty->_tag_stack[] = array('sub', array()); $_block_repeat=true; echo HelpElementHelpers::block_sub(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
New Options for Displaying Invoice Items<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo HelpElementHelpers::block_sub(array(), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>


When creating an <?php $_smarty_tpl->smarty->_tag_stack[] = array('page', array('name'=>"creating-invoice-from-time-report",'book'=>"invoicing")); $_block_repeat=true; echo HelpElementHelpers::block_page(array('name'=>"creating-invoice-from-time-report",'book'=>"invoicing"), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Invoice from a Time Report<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo HelpElementHelpers::block_page(array('name'=>"creating-invoice-from-time-report",'book'=>"invoicing"), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
, you now can choose how the time records and logged expenses will be grouped on the <?php $_smarty_tpl->smarty->_tag_stack[] = array('term', array()); $_block_repeat=true; echo HelpElementHelpers::block_term(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Invoice<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo HelpElementHelpers::block_term(array(), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
. The following time record layout options are available:

- **Separate Invoice Items** -  displays every logged time entry in the invoice.
- **Group by Task** - displays the task name and the amount of logged time per task.
- **Group by Project** - displays a sum of all time entries under the project name.
- **Group by Job Type** - displays the number of hours logged for a job type.
- **Sum All Time Records as a Single Invoice Item**.

While you choosing how to display invoice items, you will be able to preview the layout:

<?php echo HelpElementHelpers::function_image(array('name'=>"task_on_invoice.png"),$_smarty_tpl);?>


After making a selection, you can create and issue your new invoice:

<?php echo HelpElementHelpers::function_image(array('name'=>"invoice.png"),$_smarty_tpl);?>


We sincerely hope that you will enjoy using this new feature!

<?php $_smarty_tpl->smarty->_tag_stack[] = array('sub', array()); $_block_repeat=true; echo HelpElementHelpers::block_sub(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Share your Feedback<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo HelpElementHelpers::block_sub(array(), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>


Our team is always open to your suggestions and ideas on how to improve activeCollab. Community feedback has shaped activeCollab into what it is today. 

If you have any similar ideas on how to make activeCollab the best project management tool out there, feel free to get in touch and share your insights.<?php }} ?>