{title lang=false}{$active_module->getDisplayName()}{/title}
{add_bread_crumb}Details{/add_bread_crumb}

<div id="module" class="module_admin_details">
  {include file=get_view_path('_module_info', 'fw_modules_admin', $smarty.const.MODULES_FRAMEWORK)}
</div>