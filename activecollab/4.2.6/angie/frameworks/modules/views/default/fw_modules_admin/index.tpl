{title}Modules{/title}
{add_bread_crumb}All Modules{/add_bread_crumb}

<div id="modules_admin">
{if is_foreachable($modules.native_modules)}
  <table class="common modules_list" cellspacing="0">
    <tr>
      <th class="is_enabled"></th>
      <th class="name" colspan="2">{lang}Native Modules{/lang}</th>
      <th class="options"></th>
    </tr>
  {foreach $modules.native_modules as $module}
    {if AngieApplication::isCompatibleModule($module) && !AngieApplication::isBlockedForAutoloadError($module)}
    <tr class="{cycle values='even, odd'} {$module->getName()}">
      <td class="is_enabled">
        {if $module->isInstalled()}
        {checkbox_field on_url=$module->getEnableUrl() off_url=$module->getDisableUrl() class="enabling_disabling_chx" checked=$module->isEnabled() name="enabling" disabled=!$module->canDisable($logged_user)}
      {/if}
      </td>
      <td class="icon"><img src="{$module->getIconUrl()}"></td>
      <td class="name">
        {$module->getDisplayName()}, <span class="details">v{$module->getVersion()}</span>
        {if $module->getDescription()}
          <span class="details block">{$module->getDescription()|clickable nofilter}</span>
        {/if}
      </td>
      <td class="options">
        {if $module->isInstalled()}
          {if $module->canUninstall($logged_user)}
            {button href=$module->getUninstallUrl() class="uninstall_module_btn" confirm=$module->getUninstallMessage() success_event="module_deleted"}Uninstall{/button}
          {/if}
          {else}
          {if $module->canInstall($logged_user)}
            {button href=$module->getInstallUrl() class="install_module_btn" title="Install Module" mode="flyout_form"}Install{/button}
          {/if}
        {/if}
      </td>
    </tr>
    {else}
    <tr class="{cycle values='even, odd'} {$module->getName()} not_compatible">
      <td colspan="4">
        <p class="not_compatible_warning">{lang module=$module->getDisplayName() application=$application_name}<b>:module module</b> has been disabled because it is <u>not compatible</u> with the current version of :application{/lang}.</p>
        <p class="not_compatible_resolution">{lang}To enable it, please click on a link that best describes how you got this module{/lang}:</p>

        <ol>
          <li>{lang compatibility_url=AngieApplication::getCompatibilityLink($module)}<a href=":compatibility_url">I Got this Module</a> from a Third Party Developer (Purchased it or Got a Free Download){/lang}</li>
          <li><a href="{AngieApplication::getCompatibilityLink($module, true)}">{lang}I Developed this Module{/lang}</a></li>
        </ol>
      </td>
    </tr>
    {/if}
  {/foreach}
  </table>
  {*Custom modules*}
  {if is_foreachable($modules.custom_modules)}
    <table class="common modules_list" cellspacing="0">
      <tr>
        <th class="is_enabled"></th>
        <th class="name" colspan="2">{lang}Custom Modules{/lang}</th>
        <th class="options"></th>
      </tr>
      {foreach from=$modules.custom_modules item=module}
        <tr class="{cycle values='even, odd'} {$module->getName()}">
          <td class="is_enabled">
            {if $module->isInstalled()}
              {checkbox_field on_url=$module->getEnableUrl() off_url=$module->getDisableUrl() class="enabling_disabling_chx" checked=$module->isEnabled() name="enabling" disabled=!$module->canDisable($logged_user)}
            {/if}
          </td>
          <td class="icon"><img src="{$module->getIconUrl()}"></td>
          <td class="name">
            {$module->getDisplayName()}, <span class="details">v{$module->getVersion()}</span>
            {if $module->getDescription()}
              <span class="details block">{$module->getDescription()|clickable nofilter}</span>
            {/if}
          </td>
          <td class="options">
            {if $module->isInstalled()}
              {if $module->canUninstall($logged_user)}
                {button href=$module->getUninstallUrl() class="uninstall_module_btn" confirm=$module->getUninstallMessage() success_event="module_deleted"}Uninstall{/button}
              {/if}
              {else}
              {if $module->canInstall($logged_user)}
                {button href=$module->getInstallUrl() class="install_module_btn" title="Install Module" mode="flyout_form"}Install{/button}
              {/if}
            {/if}
          </td>
        </tr>
      {/foreach}
        <tr>
          <td class="is_enabled"></td>
          <td colspan="2" class="disable_all_custom_modules">
            {button href=$disable_custom_modules_url class="disable_custom_modules_btn" confirm='Are you sure that you want to disable all custom modules?' title="Disable all custom modules" success_event="modules_disabled"}Disable all custom modules{/button}
          </td>
          <td class="options"></td>
        </tr>
    </table>
  {/if}
{else}
  <p>{lang}There are no modules{/lang}</p>
{/if}
</div>
<script type="text/javascript">
  (function() {
    $('.enabling_disabling_chx').asyncCheckbox({
      'success' : function() {
        if(this.checked) {
          App.Wireframe.Flash.success(App.lang('Module has been enabled') + '. ' + App.lang('Please wait until activeCollab refreshes the page'));
        } else {
          App.Wireframe.Flash.success(App.lang('Module has been disabled') + '. ' + App.lang('Please wait until activeCollab refreshes the page'));
        } // if

        location.reload();
      }
    });

    App.Wireframe.Events.bind('modules_disabled.content', function(event, module) {
      App.Wireframe.Flash.success(App.lang('Custom modules have been disabled') + '. ' + App.lang('Please wait until activeCollab refreshes the page'));

      location.reload();
    });

    App.Wireframe.Events.bind('module_created.content', function(event, module) {
      App.Wireframe.Flash.success(App.lang('Module has been installed') + '. ' + App.lang('Please wait until activeCollab refreshes the page'));

      location.reload();
    });

    App.Wireframe.Events.bind('module_deleted.content', function(event, module) {
      App.Wireframe.Flash.success(App.lang('Module has been uninstalled') + '. ' + App.lang('Please wait until activeCollab refreshes the page'));

      location.reload();
    });

  })();
</script>