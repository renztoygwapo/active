{title}Notebooks{/title}
{add_bread_crumb}List{/add_bread_crumb}

<div id="notebooks"><div id="notebooks_inner">
  <div class="notebooks_shelves"></div>

  <ul class="notebooks_list"></ul>

  <div class="section_button_wrapper notebooks_archive_button_wrapper">
    <a href="{assemble route=project_notebooks_archive project_slug=$active_project->getSlug()}" class="section_button"><span><img src="{image_url name='icons/16x16/go-to-project.png' module='environment'}">{lang}Notebook Archive{/lang}</span></a>
  </div>

</div></div>

{include file=get_view_path('_initialize_notebooks', 'notebooks', $smarty.const.NOTEBOOKS_MODULE)}