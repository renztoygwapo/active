{title}{$active_text_document_version->getName()} ({$active_asset->getName()} Version #{$active_text_document_version->getVersionNum()}){/title}
{add_bread_crumb}Details{/add_bread_crumb}

{object object=$active_text_document_version user=$logged_user}
  <div class="wireframe_content_wrapper">{text_document_versions document=$active_asset user=$logged_user id="document_versions_for_{$active_asset->getId()}"}</div>  
{/object}