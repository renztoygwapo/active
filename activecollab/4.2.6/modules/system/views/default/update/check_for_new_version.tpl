{title}New Version Details{/title}

<div id="new_version_dialog">
  <table cellspacing="0">
    <tr>
      <td class="new_version_block current">
        <div class="new_version_block_inner">
          <h3>{lang}Current Version{/lang}</h3>
          <div class="version_num">{$current_version}</div>
        </div>
      </td>
    </tr>
    <tr>
      <td class="new_version_block latest_available">
        <div class="new_version_block_inner">
          <h3>{lang}Latest Version Available for Your Support Plan{/lang}</h3>
          <div class="version_num">{$latest_available_version}</div>
          <a href="{$update_instructions_url}" class="action_link" target="_blank">{lang}Click here to Learn how to Upgrade{/lang}</a>
        </div>
      </td>
    </tr>
    <tr>
      <td class="new_version_block latest">
        <div class="new_version_block_inner">
          <h3>{lang}Latest Version{/lang}</h3>
          <div class="version_num">{$latest_version}</div>
          <a href="{$renew_support_url}" class="action_link" target="_blank"  >{lang}Please extend support and upgrades plan to get access to the latest version{/lang}</a>
          <img src="{image_url name='layout/version-info/locked.png' module='system'}" class="lock_icon">
        </div>
      </td>
    </tr>
  </table>
  <div class="latest_slip"></div>
</div>


<script type="text/javascript">
  (function ($) {
    var wrapper = $('#new_version_dialog');
    var latest_available_version_block = wrapper.find('td.new_version_block.latest_available');
    var latest_version_block = wrapper.find('td.new_version_block.latest');
    var latest_slip = wrapper.find('div.latest_slip').hide();

    var current_version = {$current_version|json nofilter}
    var latest_available_version = {$latest_available_version|json nofilter};
    var latest_version = {$latest_version|json nofilter}

    if (App.compareVersions(current_version, latest_available_version) == 0) {
      latest_available_version_block.hide();
      if (App.compareVersions(current_version, latest_version) == 0) {
        latest_version_block.hide();
      } // if
    } else {
      if (App.compareVersions(latest_available_version, latest_version) == 0) {
        latest_version_block.hide();
      } // if
    } // if

    if (App.compareVersions(current_version, latest_available_version) == -1) {
      latest_slip.show();
    } // if
  }(jQuery));
</script>