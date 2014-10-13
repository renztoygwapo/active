<?php /* Smarty version Smarty-3.1.12, created on 2014-10-04 12:02:12
         compiled from "C:\wamp\www\dev\activecollab\4.2.6\modules\system\help\whats_new\4.1.0\02. Receive Payments via String or Braintree.md" */ ?>
<?php /*%%SmartyHeaderCode:17561542fe1c4d52012-73164439%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '5ea9022b1ad38669b4b1b7239148ecedabe0fc56' => 
    array (
      0 => 'C:\\wamp\\www\\dev\\activecollab\\4.2.6\\modules\\system\\help\\whats_new\\4.1.0\\02. Receive Payments via String or Braintree.md',
      1 => 1403109852,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '17561542fe1c4d52012-73164439',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_542fe1c4dcf037_60312611',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_542fe1c4dcf037_60312611')) {function content_542fe1c4dcf037_60312611($_smarty_tpl) {?>*Title: Receive Payments via Stripe and Braintree
*Slug: stripe-and-braintree

================================================================

After having integrated Paypal and Authorize.net, activeCollab is now introducing support for two popular payment gateways - <a href="https://stripe.com/">Stripe</a> and <a href="https://www.braintreepayments.com/">Braintree</a>.

Being the fastest growing on-line payment services, Stripe and Braintree are used by a great number of our customers and their clients. That is why integration with activeCollab <?php $_smarty_tpl->smarty->_tag_stack[] = array('term', array()); $_block_repeat=true; echo HelpElementHelpers::block_term(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Payment Gateways<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo HelpElementHelpers::block_term(array(), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 was the next logical step. Our developers have been working hard to make this happen:

<?php echo HelpElementHelpers::function_image(array('name'=>"logos.png"),$_smarty_tpl);?>


<?php $_smarty_tpl->smarty->_tag_stack[] = array('sub', array()); $_block_repeat=true; echo HelpElementHelpers::block_sub(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Setting up Stripe and Braintree Payment Gateway in activeCollab<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo HelpElementHelpers::block_sub(array(), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>


To enable new payment gateways, visit the <?php $_smarty_tpl->smarty->_tag_stack[] = array('option', array()); $_block_repeat=true; echo HelpElementHelpers::block_option(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Administration > Payment Settings<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo HelpElementHelpers::block_option(array(), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 and click the <?php $_smarty_tpl->smarty->_tag_stack[] = array('option', array()); $_block_repeat=true; echo HelpElementHelpers::block_option(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
New Payment Gateway<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo HelpElementHelpers::block_option(array(), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 button. Select the gateway that you wish to set and fill out the required fields. You will find all the necessary information on your Stripe/Braintree account.

Do not forget to enable the new gateway after setting it up:

<?php echo HelpElementHelpers::function_image(array('name'=>"payment_gateways_zoom.png"),$_smarty_tpl);?>


<?php $_smarty_tpl->smarty->_tag_stack[] = array('sub', array()); $_block_repeat=true; echo HelpElementHelpers::block_sub(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Receive Payments from your Clients<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo HelpElementHelpers::block_sub(array(), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>


Your clients will now be able to submit payments by using Stripe or Braintree without having to leave activeCollab.

<?php echo HelpElementHelpers::function_related_video(array('name'=>"charging-clients"),$_smarty_tpl);?>
<?php }} ?>