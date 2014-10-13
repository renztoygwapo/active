<?php
  /**
   * Class description
   *
   * @package
   * @subpackage
   */

  /**
   * Render my projects list
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_my_projects($params, &$smarty) {
    $user = array_required_var($params, 'user', false, 'User');

    AngieApplication::useWidget('my_projects', SYSTEM_MODULE);
    AngieApplication::useHelper('favorite_object', FAVORITES_FRAMEWORK);

    $favorite_icon_url = AngieApplication::getImageUrl('heart-on.png', FAVORITES_FRAMEWORK);
    $not_favorite_icon_url = AngieApplication::getImageUrl('heart-off.png', FAVORITES_FRAMEWORK);

    $projects_table = TABLE_PREFIX . "projects";
    $project_users_table = TABLE_PREFIX . "project_users";
    $projects = DB::execute("SELECT p.* FROM {$projects_table} p, {$project_users_table} u WHERE p.state = ? AND p.completed_on IS NULL AND u.user_id = ? AND u.project_id = p.id ORDER BY p.name", STATE_VISIBLE, $user->getId());

    if ($projects) {
      $labels = Labels::getSliceByType(1000000, 'ProjectLabel');

      $project_url_template = Router::assemble('project', array('project_slug' => '--PROJECTSLUG--'));
      $favorite_projects = $other_projects = '';

      foreach($projects as $project) {
        $project_row = '<tr project_id="' . $project['id'] . '">';

        $project_row .= '<td class="icon left" width="16px"><img src="' . get_project_icon_url($project['id'], "16x16") . '"></td>';
        $project_row .= '<td class="name"><a class="quick_view_item" href="' . str_replace("--PROJECTSLUG--", $project['slug'], $project_url_template) . '">' . clean($project['name']) . '</a></td>';

        if ($project['label_id'] && isset($labels[$project['label_id']]) && $labels[$project['label_id']] instanceof Label) {
          $rendered_label = $labels[$project['label_id']]->render(true);
        } else {
          $rendered_label = '';
        } // if

        $project_row .= '<td class="project_options">' . $rendered_label . ProjectProgress::renderRoundProjectProgress($project['id']) . '</td>';

        $is_favorite = Favorites::isFavorite(array('Project', $project['id']), $user);

        $project_row .= '<td class="favorite right" width="16px">';
        $project_row .= $is_favorite ? '<img src="' . $favorite_icon_url . '">' : '<img src="' . $not_favorite_icon_url . '">';
        $project_row .= '</td>';

        $project_row .= '</tr>';

        if ($is_favorite) {
          $favorite_projects .= $project_row;
        } else {
          $other_projects .= $project_row;
        } // if
      } // foreach

      return '<div class="my_projects"><table class="common" cellspacing="0"><tbody>' . $favorite_projects . $other_projects . '</tbody></table></div>';
    } else {
      return '<p class="center" style="color: #999;">' . lang('There are no active projects') . '</p>';
    } // if
  } // smarty_function_my_projects