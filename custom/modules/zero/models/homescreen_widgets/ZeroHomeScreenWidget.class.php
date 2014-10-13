<?php

  /**
   * My reports home screen widget
   */
  class ZeroHomeScreenWidget extends HomescreenWidget {
  
	
      /**
       * Return home screen widget name (also widget label on home screen)
       *
       * @return string
       */
      function getName() {
          return lang('zero Widget');
      }

      /**
       * Return name of the widget group
       *
       * This value will determine where your widget will be displayed in Add a Widget dialog. 
       * It can be one of the existing groups, or a new group
       *
       * @return string
       */  
      function getGroupName() {
          return lang('zero test');
      }
  
      /**
       * Return widget description (displayed when you select this widget in Add a Widget dialog)
       *
       * @return string
       */
      function getDescription() {
          return lang('test making a homescreen widget');
      }
	  
	
	
      /**
       * Render widget title
       *
       * @return string
       */
      function renderTitle(IUser $user, $widget_id, $column_wrapper_class = NULL) {
          return parent::renderTitle($user, $widget_id, $column_wrapper_class); // Inherit default title renderer (uses widget name)
      }

      /**
       * Render widget body
       *
       * @return string
       */ 
      function renderBody(IUser $user, $widget_id, $column_wrapper_class = NULL) {
          return 'Welcome to my test reports widget';
      }
	  function renderFooter() {
		return '<footer><div>test footer</div></footer>';
	  }

  }