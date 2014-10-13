/**
 * Milestone date range widget
 */
App.widgets.MilestoneDateRange = function () {

  // public interface 
  return {
     set : function (widget_id, dates) {
       var widget = $('#'+widget_id);
       widget.empty().removeClass('milestone_single_day milestone_range milestone_tbd');
       
       // normal
       if (dates.start_date && dates.end_date) {
         var start_date = new Date(dates.start_date.timestamp * 1000);
         var end_date = new Date(dates.end_date.timestamp * 1000);

         if (start_date.getTime() == end_date.getTime()) {
           // single day milestone
           var single_date_dom = $('<div class="milestone_date_range_date milestone_date_range_single_date"><span class="milestone_date_range_date_month">' + App.Config.get('short_month_names')[(start_date.getUTCMonth() + 1)]+ '</span><span class="milestone_date_range_date_day">' + start_date.getUTCDate() + '</span><span class="milestone_date_range_date_year">' + start_date.getUTCFullYear() + '</span></div>');
           widget.addClass('milestone_single_day').append(single_date_dom);
         } else {
           // milestone range
           var start_date_dom = $('<div class="milestone_date_range_date milestone_date_range_start_date"><span class="milestone_date_range_date_month">' + App.Config.get('short_month_names')[(start_date.getUTCMonth() + 1)]+ '</span><span class="milestone_date_range_date_day">' + start_date.getUTCDate() + '</span><span class="milestone_date_range_date_year">' + start_date.getUTCFullYear() + '</span></div>');
           var end_date_dom = $('<div class="milestone_date_range_date milestone_date_range_end_date"><span class="milestone_date_range_date_month">' + App.Config.get('short_month_names')[(end_date.getUTCMonth() + 1)]+ '</span><span class="milestone_date_range_date_day">' + end_date.getUTCDate() + '</span><span class="milestone_date_range_date_year">' + end_date.getUTCFullYear() + '</span></div>');
           widget.addClass('milestone_range').append(start_date_dom).append(end_date_dom);
         }
       } else {
       // tbd
         var tbd_date_dom = $('<div class="milestone_date_range_tbd_date">' + App.lang('To be determined') + '</div>');
         widget.addClass('milestone_tbd').append(tbd_date_dom);
       }
     }
  };

}();