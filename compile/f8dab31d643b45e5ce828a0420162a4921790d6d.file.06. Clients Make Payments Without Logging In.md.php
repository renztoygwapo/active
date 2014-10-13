<?php /* Smarty version Smarty-3.1.12, created on 2014-10-04 12:02:13
         compiled from "C:\wamp\www\dev\activecollab\4.2.6\modules\system\help\whats_new\4.1.0\06. Clients Make Payments Without Logging In.md" */ ?>
<?php /*%%SmartyHeaderCode:28113542fe1c59f2e68-05847176%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'f8dab31d643b45e5ce828a0420162a4921790d6d' => 
    array (
      0 => 'C:\\wamp\\www\\dev\\activecollab\\4.2.6\\modules\\system\\help\\whats_new\\4.1.0\\06. Clients Make Payments Without Logging In.md',
      1 => 1403109852,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '28113542fe1c59f2e68-05847176',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_542fe1c5a6fe88_42856715',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_542fe1c5a6fe88_42856715')) {function content_542fe1c5a6fe88_42856715($_smarty_tpl) {?>*Title: Clients can Pay Invoices without Accessing activeCollab
*Slug: invoice-payment-without-login

================================================================

Have you ever had the following problems: 

- *You issued an <?php $_smarty_tpl->smarty->_tag_stack[] = array('term', array()); $_block_repeat=true; echo HelpElementHelpers::block_term(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Invoice<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo HelpElementHelpers::block_term(array(), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
, but your Client was not able to log in to your activeCollab and make the payment (because they forgot the password, were not able to login at that time, do not know how to use activeCollab, etc.)?* 
- *The Client did not need to have an account and you wanted to simplify the payment process for them?*

We have a solution - your Clients can now make payments, without having to log in to activeCollab. The process is  simple and straightforward, for everyone.

<?php $_smarty_tpl->smarty->_tag_stack[] = array('sub', array()); $_block_repeat=true; echo HelpElementHelpers::block_sub(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
What should I do to enable such payments?<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo HelpElementHelpers::block_sub(array(), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>


First of all, <?php $_smarty_tpl->smarty->_tag_stack[] = array('page', array('name'=>"creating-invoice",'book'=>"invoicing")); $_block_repeat=true; echo HelpElementHelpers::block_page(array('name'=>"creating-invoice",'book'=>"invoicing"), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
prepare the Invoice<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo HelpElementHelpers::block_page(array('name'=>"creating-invoice",'book'=>"invoicing"), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 that you wish to issue. After doing so, activeCollab will generate a page to which only your Clients will have access, without having to log in. It is a secure page with a unique URL, so you can rest assured that it will not be publicly available.	

Everything required for making the payment, including the <?php $_smarty_tpl->smarty->_tag_stack[] = array('term', array()); $_block_repeat=true; echo HelpElementHelpers::block_term(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
payment Gateways<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo HelpElementHelpers::block_term(array(), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
, will be available:

<?php echo HelpElementHelpers::function_image(array('name'=>"making_payment.png"),$_smarty_tpl);?>


<?php $_smarty_tpl->smarty->_tag_stack[] = array('sub', array()); $_block_repeat=true; echo HelpElementHelpers::block_sub(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
What does my Client have to do?<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo HelpElementHelpers::block_sub(array(), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>


The Client only has to visit the newly generated page that and submit a payment. Since there is no login, making the payment is quick and easy.

A "Thank you" message with the Invoice PDF download link will appear after the payment has been completed.

<?php echo HelpElementHelpers::function_image(array('name'=>"download_invoice.png"),$_smarty_tpl);?>
<?php }} ?>