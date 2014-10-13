<?php 

	class TestModule extends AngieModule {

		/**
		* Short module name (should be the same as module folder name)
		*
		* @var string
		*/
		protected $name = 'test';

		/**
		* Module version
		*
		* @var string
		*/  
		protected $version = '1.0';

		/**
		* Return module name (displayed in activeCollab administration panel)
		*
		* @return string
		*/
		function getDisplayName() {
		return lang('My Test Module');
		}

		/**
		* Return module description (displayed in activeCollab administration panel)
		*
		* @return string
		*/
		function getDescription() {
		return lang('An example of my test module');
		}

		/**
		* List events that this module listens to and define event handlers
		*/
		function defineHandlers() {
  			EventsManager::listen('on_homescreen_widget_types', 'on_homescreen_widget_types');
		}

		/**
		* List routes defined and used by this module
		*/
		function defineRoutes() {
		// Place where you can define your routes
		}
	  
	}