<?php

  // Build on top of globalization admin controller
  AngieApplication::useController('globalization_admin', GLOBALIZATION_FRAMEWORK_INJECT_INTO);

  /**
   * Languages administration controller
   *
   * @package angie.frameworks.globalization
   * @subpackage controllers
   */
  abstract class FwLanguagesAdminController extends GlobalizationAdminController {
    
    /**
     * Selected language
     *
     * @var Language
     */
    protected $active_language;
    
    /**
     * Prepare controller
     */
    function __before() {
      parent::__before();
    	$this->wireframe->breadcrumbs->add('languages_admin', lang('Languages'), Router::assemble('admin_languages'));
    	
    	$language_id = $this->request->getId('language_id');
    	if($language_id) {
    	  $this->active_language = Languages::findById($language_id);
    	} // if
    	
    	if($this->active_language instanceof Language) {
    	  $this->wireframe->breadcrumbs->add('language', $this->active_language->getName(), $this->active_language->getViewUrl());
    	} else {
    	  $this->active_language = new Language();
    	} // if
    	
    	$this->smarty->assign(array(
    		'active_language' => $this->active_language,
    	  'language_url' => Router::assemble('admin_languages')
    	));
    } // __construct
    
    /**
     * Show main languages page
     */
    function index() {
      if(extension_loaded('xml') && function_exists('xml_parser_create')) {
        $import_enabled = true;
      } // if
      
      $this->wireframe->list_mode->enable();
      
      $import_url = Router::assemble('admin_language_do_import');
     
      $this->wireframe->actions->add('new_language', lang('New Language'), Router::assemble('admin_languages_add'), array(
        'onclick' => new FlyoutFormCallback(array(
        	'success_event' => 'language_created',
          'width'					=> 400
      	)), 
        'icon' => AngieApplication::getImageUrl('layout/button-add.png', ENVIRONMENT_FRAMEWORK, AngieApplication::getPreferedInterface()),        
      ));
      
      $this->wireframe->actions->add('set_as_default', lang('Set Default Language'), Router::assemble('admin_languages_set_default'), array(
        'onclick' => new FlyoutFormCallback(array(
          'width'					=> 'narrow')
     		), 
      ));
      
      $this->smarty->assign(array(
      	'languages' => Languages::find(), 
      	'default_language_id' => ConfigOptions::getValue('language'),
      	'import_url' => $import_url,
        'import_enabled' => $import_enabled
      ));
    } // index
    
    /**
     * Set specific language as default
     */
    function set_default() {
    	if (!($this->request->isAsyncCall())) {
    		$this->response->badRequest();
    	} // if
    	
    	$default_language = $this->request->post('default_language', ConfigOptions::getValue('language'));
    	$languages = Languages::findAll();
    	
    	$this->smarty->assign(array(
    		'languages' => $languages,
    		'default_language' => $default_language
    	));
    	
    	if ($this->request->isSubmitted()) {
    		try {
    			ConfigOptions::setValue('language', $default_language, true);
    			$this->response->respondWithData(Languages::findDefault());
    		} catch (Exception $e) {
    			$this->response->exception($e);
    		} // if
    	} // if
    } // set_as_default
    
    
    /**
     * View language details
     */
    function view() {
			if (!$this->active_language->isLoaded()) {
				$this->response->notFound();
      } // if
      	
			if ($this->request->isWebBrowser()) {
				if ($this->request->isSingleCall() || $this->request->isQuickViewCall()) {
					$this->wireframe->setPageObject($this->active_language, $this->logged_user);

					$inline_tabs = new NamedList();
					$alphabet = array_merge((array) Globalization::getAlphabet(), array('*'));
					$url_pattern = Router::assemble('admin_language_translate_letter', array(
						'language_id' => $this->active_language->getId(),
						'letter' => '--LETTER--'
					));
					
					foreach ($alphabet as $alphabet_letter) {
						$inline_tabs->add('letter_' . $alphabet_letter, array(
							'title' => strtoupper($alphabet_letter),
							'url' => str_replace('--LETTER--', $alphabet_letter, $url_pattern)
						));
					} // foreach
					
					$this->smarty->assign(array(
						'inline_tabs' => $inline_tabs->toArray()
					));
												
					$this->render();
				} else {
					$this->__forward('index', 'index');
				} // if     		
			} else if ($this->request->isPhone()) {
				$this->wireframe->setPageObject($this->active_language, $this->logged_user);
			} // if
    } // view
    
    /**
     * Show page for translation of phrases starting with letter
     */
    function translate_letter() {
    	$current_letter = $this->request->get('letter');
			$this->smarty->assign(array(
				'translate_data' => $this->active_language->prepareForTranslation($current_letter),	
			));
    } // view
    
    /**
     * Create a new language
     */
    function add() {
      if($this->request->isAsyncCall()) {
        $language_data = $this->request->post('language');
        $this->response->assign('language_data', $language_data);
        
        if($this->request->isSubmitted()) {
          try {
            $this->active_language->setAttributes($language_data);
            $this->active_language->save();
            
            $this->response->respondWithData($this->active_language, array('as' => 'language'));
          } catch (Error $e) {
            $this->response->exception($e);
          }//try
        } // if
      	
      } else {
        $this->response->badRequest();
      } // if
    } // add
    
    /**
     * Update language information
     */
    function edit() {
      if($this->request->isAsyncCall()) {
        if($this->active_language->isNew()) {
          $this->response->notFound();
        } // if
        $language_data = $this->request->post('language');
      	if(!is_array($language_data)) {
      	  $language_data = array(
      	    'name'                  => $this->active_language->getName(),
      	    'locale'                => $this->active_language->getLocale(),
            'decimal_separator'     => $this->active_language->getDecimalSeparator(),
            'thousands_separator'   => $this->active_language->getThousandsSeparator()
      	  );
      	} // if
      	$this->smarty->assign('language_data', $language_data);
        	
        if($this->request->isSubmitted()) {
          try {
            $this->active_language->setAttributes($language_data);
            $this->active_language->save();
            
            $this->response->respondWithData($this->active_language, array(
              'as' => 'language', 
              'detailed' => true, 
            ));
          } catch (Error $e) {
            $this->response->exception($e);
          }//try
        }//if
      } else {
        $this->response->badRequest();
      }//if
    } // edit
    
    /**
     * Remove specific language
     */
    function delete() {
    	if($this->active_language->isNew()) {
    	  $this->response->notFound();
    	} // if
    	if(!$this->active_language->canDelete($this->logged_user)) {
    	  $this->response->forbidden();
    	} // if
    	
    	try {
    	  $this->active_language->delete();
    	  
    	  $this->response->respondWithData($this->active_language, array(
          'as' => 'language', 
          'detailed' => true, 
        ));
    	} catch (Error $e) {
    	  $this->response->exception($e);
    	}//try
      	
    } // delete    
    
    /**
     * Edit translation in chosen language
     */
    function edit_translation() {
      if($this->request->isAsyncCall() && $this->request->isSubmitted()) {
        if($this->active_language->isNew()) {
      	  $this->response->notFound();
      	} // if
      	
      	try {
          $form_data = $this->request->post('form_data');
          $this->active_language->setTranslation($form_data);
          
          $this->response->respondWithData($this->active_language, array(
            'as' => 'language', 
            'detailed' => true, 
          ));
      	} catch (Error $e) {
      	  $this->response->exception($e);
      	}//try
      } else {
  	    $this->response->badRequest();  
  	  }//if
    } // edit_translation_file
  
    /**
     * Do language import
     */
    function do_import() {
    	
    	try {
    		$attachment = make_attachment($_FILES['xml']);
    		if (!($attachment instanceof Attachment)) {
					throw new Error(lang('Language definition file was not uploaded correctly'));
    		} // if
    		
	      $init_steps = new NamedList(array(
	      	'initial' => array(
		        'text' => lang('Initial'),
		        'url' => Router::assemble('execute_import_steps', array('what' => 'initial', 'language_id' => $this->active_language->getId(), 'attachment_id' => $attachment->getId())), 
		       ),
		       'review' => array(
		        'text' => lang('Review'),
		        'url' => Router::assemble('execute_import_steps', array('what' => 'review', 'language_id' => $this->active_language->getId(), 'attachment_id' => $attachment->getId())), 
		       ),
		       'finalize' => array(
		        'text' => lang('Finishing'),
		        'url' => Router::assemble('execute_import_steps', array('what' => 'finalize', 'language_id' => $this->active_language->getId(), 'attachment_id' => $attachment->getId())), 
		       )
	      ));

	      $this->smarty->assign(array(
	        'import_steps' => $init_steps,
	      ));
    	} catch (Exception $e) {
				$this->response->exception($e);    		
    	} // try
    } // do_import
    
    /**
     * Execute installation steps
     */
    function execute_import_steps() {
      try {
        $action = $this->request->get('what');
        $update = $this->request->get('update');
        $attachment_id = $this->request->get('attachment_id');
        $attachment = Attachments::findById($attachment_id);

        if (!($attachment instanceof Attachment)) {
          throw new Error(lang('There was some unknown error, please try again'));
        } // if
        switch ($action) {
          case 'initial':
            if(!extension_loaded('xml') || !function_exists('xml_parser_create')) {
              throw new Error(lang('XML extension not found'));
            } // if
            $this->response->ok();
            break;
            
          case 'review':
              
            if (!is_file(UPLOAD_PATH.'/'.$attachment->getLocation())) {
              throw new Error(lang('You need to upload XML file first'));
            } // if
            
            require_once(ANGIE_PATH.'/classes/xml/xml2array.php');
            $language = xml2array(file_get_contents(UPLOAD_PATH.'/'.$attachment->getLocation()));
            if (!$language) {
              throw new Error(lang('Language XML file is corrupted'));
            } //if
                       
            $locale = $language['language']['info']['locale']['value'];
            $name = $language['language']['info']['name']['value'];
            
            if (!$locale || !$name) {
							throw new Error(lang('Language XML file is corrupted'));
            } // if

            if (Languages::localeExists($locale) && !$update) {
              throw new Error(lang('Language with locale :locale is already installed on system', array('locale' => $locale)));
            } // if
            
            if (Languages::nameExists($name) && !$update) {
              throw new Error(lang('Language with name :name is already installed on system', array('name' => $name)));
            } // if
             
            $this->response->ok();
            break;
            
          case 'finalize':
            require_once(ANGIE_PATH.'/classes/xml/xml2array.php');
            $language_array = xml2array(file_get_contents(UPLOAD_PATH.'/'.$attachment->getLocation()));
            if (!$language_array) {
              throw new Error(lang('Uploaded file is not valid XML'));
            } // if
            
            $language_locale = $language_array['language']['info']['locale']['value'];
            $language_name = $language_array['language']['info']['name']['value'];

            if ($update) {
              if (strtolower($language_locale) !== strtolower($this->active_language->getLocale())) {
                throw new Error(lang('Locale in XML file is not matching locale on the selected language'));
              } // if
              $this->active_language->setName($language_name);
              $this->active_language->save();
              $language = $this->active_language;
            } else {
              $language = new Language();
              $language->setLocale($language_locale);
              $language->setName($language_name);
              $language->save();
            } //if

            $translation_data = array();
        	  if (is_foreachable($language_array['language']['translations']['translation'])) {
        	    $translations = $language_array['language']['translations']['translation'];
        	    foreach ($translations as $translation) {
        	      $translation_data[$translation['attr']['phrase']] = $translation['value'];
        	    }//foreach
        	  }//if

        	  $language->setTranslation($translation_data);

        	  // cleanup unused translations
        	  Languages::cleanUpUnusedTranslations();
            
            $this->response->respondWithData($language, array(
              'as' => 'language', 
              'detailed' => true, 
            ));
        	  break;
        }//switch
      } catch (Error $e) {
        $this->response->exception($e);
      }//try
    }//execute_installation_steps
    
    /**
     * Exports language in XML form
     */
    function export() {
      if($this->active_language->isNew()) {
        $this->response->notFound();
      } // if
      	
      $this->smarty->assign(array(
      	'ac_version' =>  AngieApplication::getVersion(),
      	'translations' =>  $this->active_language->getTranslation(),
      ));
      
      $xml = $this->smarty->fetch(get_view_path('export', 'fw_languages_admin', GLOBALIZATION_FRAMEWORK));
      $filename = clean($this->active_language->getName()).' ('.$this->active_language->getLocale().').xml';
      
    	$this->response->respondWithContentDownload($xml, BaseHttpResponse::XML, $filename);
    } // export

    /**
     * Update language from XML file
     */
    function update() {
      $update_enabled = (extension_loaded('xml') && function_exists('xml_parser_create')) ? true : false;

      $this->response->assign(array(
        'update_enabled' => $update_enabled,
        'update_url' => Router::assemble('admin_language_do_update',array('language_id' => $this->active_language->getId()))
      ));
    } // update

    /**
     * Do language import
     */
    function do_update() {
      try {
        $attachment = make_attachment($_FILES['xml']);
        if (!($attachment instanceof Attachment)) {
          throw new Error(lang('Language definition file was not uploaded correctly'));
        } // if

        $init_steps = new NamedList(array(
          'initial' => array(
            'text' => lang('Initial'),
            'url' => Router::assemble('execute_import_steps',
              array(
                'what' => 'initial',
                'language_id' => $this->active_language->getId(),
                'attachment_id' => $attachment->getId(),
                'update' => true,
              )),
          ),
          'review' => array(
            'text' => lang('Review'),
            'url' => Router::assemble('execute_import_steps',
              array(
                'what' => 'review',
                'language_id' => $this->active_language->getId(),
                'attachment_id' => $attachment->getId(),
                'update' => true,
              )),
          ),
          'finalize' => array(
            'text' => lang('Finishing'),
            'url' => Router::assemble('execute_import_steps',
              array(
                'what' => 'finalize',
                'language_id' => $this->active_language->getId(),
                'attachment_id' => $attachment->getId(),
                'update' => true,
              )),
          )
        ));

        $this->smarty->assign(array(
          'update_steps' => $init_steps,
        ));
      } catch (Exception $e) {
        $this->response->exception($e);
      } // try
    } // do_import
    
    /**
     * Save single translation
     */
    function save_single() {
      if($this->active_language->isLoaded()) {
        if($this->request->isSubmitted()) {
          $hash = $this->request->post('hash');
          if (!$hash || strlen($hash) != 32) {
            $this->response->badRequest('Hash is required');
          } // if

          $translation = $this->request->post('translation');

          try {
            if($translation) {
              $this->active_language->setTranslation(array(
                $hash => $translation
              ), true);
            } else {
              $this->active_language->unsetTranslation($hash, true);
            } // if

            $this->response->ok();
          } catch (Exception $e) {
            $this->response->exception($e);
          } // try
        } else {
          $this->response->badRequest();
        } // if
      } else {
        $this->response->notFound();
      } // if
    } // save_single
    
  }