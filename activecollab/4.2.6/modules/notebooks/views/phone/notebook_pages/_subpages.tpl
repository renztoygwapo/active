{if is_foreachable($_subpages)}
  <ul data-role="listview" data-inset="true" data-dividertheme="j" data-theme="j">
  	<li data-role="list-divider"><img src="{image_url name="icons/listviews/navigate-subpages.png" module=$smarty.const.NOTEBOOKS_MODULE interface=AngieApplication::INTERFACE_PHONE}" class="divider_icon" alt="">{lang}Subpages{/lang}</li>
    {notebook_pages_tree notebook_pages=$_subpages user=$logged_user}
  </ul>
{/if}