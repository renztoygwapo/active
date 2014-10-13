<div class="milestone_data_range">
  <div id="milestone_date_range_{$_milestone->getId()}" class="milestone_date_range milestone_range">
  	<div class="milestone_date_range_date milestone_date_range_start_date">
  		<span class="milestone_date_range_date_month">{get_month_name month=$_milestone->getStartOn()->getMonth() short_name=true}</span>
  		<span class="milestone_date_range_date_day">{$_milestone->getStartOn()->getDay()}</span>
  		<span class="milestone_date_range_date_year">{$_milestone->getStartOn()->getYear()}</span>
  	</div>
  	<div class="milestone_date_range_date milestone_date_range_end_date">
  		<span class="milestone_date_range_date_month">{get_month_name month=$_milestone->getDueOn()->getMonth() short_name=true}</span>
  		<span class="milestone_date_range_date_day">{$_milestone->getDueOn()->getDay()}</span>
  		<span class="milestone_date_range_date_year">{$_milestone->getDueOn()->getYear()}</span>
  	</div>
  </div>
</div>