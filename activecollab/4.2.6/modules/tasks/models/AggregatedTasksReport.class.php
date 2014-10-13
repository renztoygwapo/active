<?php

  /**
   * Aggreagated tasks report
   *
   * @package activeCollab.modules.tasks
   * @subpackage models
   */
  class AggregatedTasksReport {
    
    const GROUP_BY_MILESTONE = 'milestone_id';
    const GROUP_BY_CATEGORY = 'category_id';
    const GROUP_BY_LABEL = 'label_id';
    const GROUP_BY_PRIORITY = 'priority';
    const GROUP_BY_STATUS = 'completed_on';

    /**
     * Project instance
     *
     * @var Project
     */
    private $project;

    /**
     * Group by method
     *
     * @var string
     */
    private $group_by;

    /**
     * Report data
     *
     * @var array
     */
    private $serie_array = Array();

    /**
     * Init data
     * @param Project $project
     * @param string $group_by
     */
    function __construct(Project $project, $group_by = AggregatedTasksReport::GROUP_BY_MILESTONE) {
      $this->project = $project;
      $this->group_by = $group_by;
    } // __construct

    /**
     * Renders the chart
     * @param IUser $logged_user
     * @return string
     */
    function render(IUser $logged_user) {

      $db_result = DB::execute("SELECT $this->group_by, COUNT(*) as count FROM ".TABLE_PREFIX."project_objects WHERE project_id = ? AND type='Task' AND state >= ? AND visibility >= ? GROUP BY $this->group_by", $this->project->getId(), STATE_VISIBLE, $logged_user->getMinVisibility());
      $array_result = $db_result instanceof DBResult ? $db_result->toArrayIndexedBy($this->group_by) : false;

      if (is_foreachable($array_result)) {
        $pie_chart = new PieChart('400px', '400px', 'task_report_pie_chart_placeholder');
        $this->serie_array = array();

        // Set data for active/completed

        if ($this->group_by == AggregatedTasksReport::GROUP_BY_STATUS) {
          $active = 0;
          $completed = 0;
          foreach ($array_result as $serie_data) {
            if ($serie_data['completed_on']) {
              $completed += $serie_data['count'];
            } else {
              $active += $serie_data['count'];
            } //if
          } //foreach
          $point_active = new ChartPoint('1', $active);
          $serie_active = new ChartSerie($point_active);
          $serie_active->setOption('label', lang('Active'));
          $this->serie_array[] = $serie_active;

          $point_completed = new ChartPoint('1', $completed);
          $serie_completed = new ChartSerie($point_completed);
          $serie_completed->setOption('label', lang('Completed'));
          $this->serie_array[] = $serie_completed;

        } else {

          // Set data for the rest

          foreach ($array_result as $serie_data) {
            switch ($this->group_by) {
              case AggregatedTasksReport::GROUP_BY_MILESTONE :
                $point = new ChartPoint('1', $serie_data['count']);
                $serie = new ChartSerie($point);
                if (intval($serie_data['milestone_id'])) {
                  $milestone = Milestones::findById(intval($serie_data['milestone_id']));
                  $label = PieChart::makeShortForPieChart($milestone->getName());
                } else {
                  $label = lang('No Milestone');
                } //if
                $serie->setOption('label', $label);
                break;
              case AggregatedTasksReport::GROUP_BY_CATEGORY :
                $point = new ChartPoint('1', $serie_data['count']);
                $serie = new ChartSerie($point);
                if (intval($serie_data['category_id'])) {
                  $category = Categories::findById(intval($serie_data['category_id']));
                  $label = PieChart::makeShortForPieChart($category->getName());
                } else {
                  $label = lang('Uncategorized');
                } //if
                $serie->setOption('label', $label);
                break;
              case AggregatedTasksReport::GROUP_BY_LABEL :
                $point = new ChartPoint('1', $serie_data['count']);
                $serie = new ChartSerie($point);
                if (intval($serie_data['label_id'])) {
                  $label = Labels::findById(intval($serie_data['label_id']));
                  $label = PieChart::makeShortForPieChart($label->getName());
                } else {
                  $label = lang('No Label');
                } //if
                $serie->setOption('label', $label);
                break;
              case AggregatedTasksReport::GROUP_BY_PRIORITY :
                $point = new ChartPoint('1', $serie_data['count']);
                $serie = new ChartSerie($point);
                switch ($serie_data['priority']) {
                  case -2:
                    $serie->setOption('label', lang('Very Low'));
                    break;
                  case -1:
                    $serie->setOption('label', lang('Low'));
                    break;
                  case 0:
                    $serie->setOption('label', lang('Normal'));
                    break;
                  case 1:
                    $serie->setOption('label', lang('High'));
                    break;
                  case 2:
                    $serie->setOption('label', lang('Very High'));
                    break;
                } //switch
                break;
              default:
                $serie = null;
            } //switch
            $this->serie_array[] = $serie;
          } //foreach
        } //if
        $pie_chart->addSeries($this->serie_array);
        return $pie_chart->render();
      } else {
        return '<p class="empty_slate">' . lang('There are no tasks in this project.') . '</p>';
      } //if
    } // render

    /**
     * Return data
     * 
     * @return array
     */
    function getData() {
      return $this->serie_array;
    } //getData

  }