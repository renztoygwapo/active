{title}Update Discussion{/title}
{add_bread_crumb}Edit{/add_bread_crumb}

{form action=$active_discussion->getEditUrl() method=post id=editDiscussionForm class='big_form' enctype="multipart/form-data"}
{include file=get_view_path('_discussion_form', 'discussions', 'discussions')}
{wrap_buttons}
  {submit}Save Changes{/submit}
{/wrap_buttons}
{/form}