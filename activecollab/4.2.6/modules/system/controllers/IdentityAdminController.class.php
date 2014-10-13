<?php

  // Build on top of admin controller
  AngieApplication::useController('admin', ENVIRONMENT_FRAMEWORK_INJECT_INTO);

  /**
   * Identity administration controller
   * 
   * @package activeCollab.modules.system
   * @subpackage controller
   */
  class IdentityAdminController extends AdminController {
  
    /**
     * Show identity admin settings
     */
    function index() {
      if ($this->request->isAsyncCall()) {
        $settings_data = $this->request->post('settings', ConfigOptions::getValue(array(
          'identity_name',
          'identity_client_welcome_message',
          'identity_logo_on_white',
          'rep_site_domain'
        )));

        $timestamp = '?timestamp=' . time();

        $this->response->assign(array(
          'settings_data' => $settings_data,
          'small_logo_url' => AngieApplication::getBrandImageUrl('logo.16x16.png') . $timestamp,
          'medium_logo_url' => AngieApplication::getBrandImageUrl('logo.40x40.png') . $timestamp,
          'large_logo_url' => AngieApplication::getBrandImageUrl('logo.80x80.png') . $timestamp,
          'larger_logo_url' => AngieApplication::getBrandImageUrl('logo.128x128.png') . $timestamp,
          'photo_logo_url' => AngieApplication::getBrandImageUrl('logo.256x256.png') . $timestamp,
          'login_page_logo' => AngieApplication::getBrandImageUrl('login-page-logo.png') . $timestamp,
          'favicon_url' => AngieApplication::getBrandImageUrl('favicon.ico') . $timestamp,

          'revert_logo_url'     => Router::assemble('identity_admin_revert', array('target' => 'logo')),
          'revert_login_logo_url'     => Router::assemble('identity_admin_revert', array('target' => 'login_logo')),
          'revert_favicon_url'     => Router::assemble('identity_admin_revert', array('target' => 'favicon')),
        ));

        if($this->request->isSubmitted()) {

          try {
            if(empty($settings_data['identity_name'])) {
              $settings_data['identity_name'] = null;
            } // if

            if(empty($settings_data['identity_client_welcome_message'])) {
              $settings_data['identity_client_welcome_message'] = null;
            } // if

            if(empty($settings_data['rep_site_domain'])) {
              $settings_data['rep_site_domain'] = null;
            } // if

            ConfigOptions::setValue('rep_site_domain', trim(array_var($settings_data, 'rep_site_domain')));
            ConfigOptions::setValue('identity_name', trim(array_var($settings_data, 'identity_name')));
            ConfigOptions::setValue('identity_client_welcome_message', trim(array_var($settings_data, 'identity_client_welcome_message')));
            ConfigOptions::setValue('identity_logo_on_white', (boolean) array_var($settings_data, 'identity_logo_on_white'));

            if (is_foreachable($_FILES)) {
              // check if branding folder is writable
              $branding_path = PUBLIC_PATH . '/brand';
              if (!folder_is_writable($branding_path)) {
                throw new Exception(lang('Branding folder is not writable'));
              } // if
            } // if

            // logo is uploaded
            $logo = array_var($_FILES, 'logo');
            if (is_array($logo)) {
              $uploaded_file_path = array_var($logo, 'tmp_name', null);
              $uploaded_file_name = array_var($logo, 'name', null);
              $revert_files = array();

              if (!$uploaded_file_path) {
                throw new Exception(lang('Image upload failed. Check PHP configuration'));
              } // if

              $temporary_file = move_uploaded_file_to_temp_directory($uploaded_file_path, $uploaded_file_name);
              if (!$temporary_file) {
                throw new Exception(lang('Could not move uploaded image to temporary folder'));
              } else {
                $revert_files[] = $temporary_file;
              } // if

              // open source image
              $image_resource = open_image($temporary_file);
              if (!$image_resource) {
                throw new Exception(lang('Could not read uploaded image'));
              } // if

              // check if all images are writable and create resized versions
              $sizes = array('256', '128', '80', '40', '16');
              $destination = array();
              foreach ($sizes as $size) {
                $destination[$size] = $branding_path . "/logo.{$size}x{$size}.png";
                if (!file_is_writable($destination[$size], false)) {
                  throw new Exception(lang('Branding file :file is not writable', array('file' => $destination[$size])));
                } // if

                $resize_result = scale_and_fit_image($image_resource, $destination[$size] . '.temp', $size, $size, IMAGETYPE_PNG);
                if (!$resize_result) {
                  throw new Exception(lang('Could not resize uploaded image to dimensions :size x :size', array('size' => $size)));
                } else {
                  $revert_files[] = $destination[$size] . '.temp';
                } // if
              } // foreach

              // remove old images, and set new ones
              foreach ($sizes as $size) {
                $destination[$size] = $branding_path . "/logo.{$size}x{$size}.png";
                @unlink($destination[$size]);
                rename($destination[$size] . '.temp', $destination[$size]);
              } // foreach
            } // if

            // login logo is uploaded
            $login_page_logo = array_var($_FILES, 'login_page_logo');
            if (is_array($login_page_logo)) {
              $uploaded_login_logo_path = array_var($login_page_logo, 'tmp_name', null);
              if (!$uploaded_login_logo_path) {
                throw new Exception(lang('Image upload failed. Check PHP configuration'));
              } // if

              $destination_path = $branding_path . '/login-page-logo.png';
              $destination_temp_path = $branding_path . '/temp-login-page-logo.png';
              if (!move_uploaded_file($uploaded_login_logo_path, $destination_temp_path)) {
                throw new Exception(lang('Could not move uploaded image to temporary folder'));
              } else {
                $revert_files[] = $destination_temp_path;
              } // if

              $image_resource = open_image($destination_temp_path);
              if (!$image_resource) {
                throw new Exception(lang('Could not read uploaded image'));
              } // if

              $resize_result = scale_and_fit_image($image_resource, $destination_temp_path, 256, 256, IMAGETYPE_PNG);
              if (!$resize_result) {
                throw new Exception(lang('Could not resize uploaded image to dimensions :size x :size', array('size' => 512)));
              } else {
                $revert_files[] = $destination_temp_path;
              } // if

              if (!unlink($destination_path)) {
                throw new Exception(lang('Could not remove old login logo image'));
              } // if

              rename($destination_temp_path, $destination_path);
            } // if

            // login logo is uploaded
            $favicon_image = array_var($_FILES, 'favicon');
            if (is_array($favicon_image)) {
              $uploaded_favicon_path = array_var($favicon_image, 'tmp_name', null);
              if (!$uploaded_favicon_path) {
                throw new Exception(lang('Image upload failed. Check PHP configuration'));
              } // if

              $destination_favicon_path = $branding_path . '/favicon.ico';
              $destination_favicon_temp_path = $branding_path . '/temp-favicon.ico';
              if (!move_uploaded_file($uploaded_favicon_path, $destination_favicon_temp_path)) {
                throw new Exception(lang('Could not move uploaded image to temporary folder'));
              } else {
                $revert_files[] = $destination_favicon_temp_path;
              } // if

              if (!unlink($destination_favicon_path)) {
                throw new Exception(lang('Could not remove old favicon'));
              } // if

              rename($destination_favicon_temp_path, $destination_favicon_path);
            } // if

            $this->wireframe->javascriptAssign('identity_name', ConfigOptions::getValue('identity_name'));

            $this->response->ok();
          } catch (Exception $e) {
            // remove all temporary files
            if (is_foreachable($revert_files)) {
              foreach ($revert_files as $revert_file) {
                @unlink($revert_file);
              } // foreach
            } // if

            if ($logo) {
              die(JSON::encode(array(
                'ajax_error' => true,
                'ajax_message' => $e->getMessage()
              )));
            } else {
              $this->response->exception($e);
            } // if
          } // try

        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // index

    /**
     * Revert identity element
     */
    function revert() {
      if (!($this->request->isAsyncCall() && $this->request->isSubmitted())) {
        $this->response->badRequest();
      } // if

      $target = strtolower($this->request->get('target'));
      if (!in_array($target, array('logo', 'login_logo', 'favicon'))) {
        $this->response->badRequest();
      } // if

      $branding_path = PUBLIC_PATH . '/brand';

      try {
        if ($target == 'logo') {
          $sizes = array('256', '128', '80', '40', '16');
          foreach ($sizes as $size) {
            $original_image = SYSTEM_MODULE_PATH . "/assets/default/images/application-branding/default-branding.{$size}x{$size}.png";
            $custom_image = $branding_path . "/logo.{$size}x{$size}.png";

            if (is_file($custom_image) && !@unlink($custom_image)) {
              throw new Error(lang('Could not delete :custom_image', array('custom_image' => $custom_image)));
            } // if

            if (!copy($original_image, $custom_image)) {
              throw new Error(lang('Could not revert to default image :default_image', array('default_image' => $original_image)));
            } // if
          } // if

        } else if ($target == 'login_logo') {
          $original_image = SYSTEM_MODULE_PATH . "/assets/default/images/application-branding/default-login-page-logo.png";
          $custom_image = $branding_path . "/login-page-logo.png";

          if (is_file($custom_image) && !@unlink($custom_image)) {
            throw new Error(lang('Could not delete :custom_image', array('custom_image' => $custom_image)));
          } // if

          if (!copy($original_image, $custom_image)) {
            throw new Error(lang('Could not revert to default image :default_image', array('default_image' => $original_image)));
          } // if

        } else if ($target == 'favicon') {
          $original_image = SYSTEM_MODULE_PATH . "/assets/default/images/application-branding/default-favicon.ico";
          $custom_image = $branding_path . "/favicon.ico";

          if (is_file($custom_image) && !@unlink($custom_image)) {
            throw new Error(lang('Could not delete :custom_image', array('custom_image' => $custom_image)));
          } // if

          if (!copy($original_image, $custom_image)) {
            throw new Error(lang('Could not revert to default image :default_image', array('default_image' => $original_image)));
          } // if

        } // if
      } catch (Exception $e) {
        $this->response->exception($e);
      } // try

      $this->response->ok();
    } // if
    
  }