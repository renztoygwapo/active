<?php

  /**
   * Basecamp Sources - New API and Classic
   *
   * @package angie.frameworks.data_sources
   * @subpackage basecamp
   */
  abstract class BasecampSource extends DataSource {

    /**
     * Basecamp API url and version
     */
    const API_URL = 'https://basecamp.com';
    const API_VERSION = 'v1';


    /**
     * Return import settings
     *
     * @return mixed
     */
    public function getImportSettings() {
      return $this->getAdditionalProperty('import_settings');
    } //getImportSettings

    /**
     * Set import settings
     *
     * @param $value
     * @return mixed
     */
    public function setImportSettings($value) {
      return $this->setAdditionalProperty('import_settings', $value);
    } //setImportSettings

    /**
     * Return account id
     *
     * @return mixed
     */
    public function getAccountId() {
      return $this->getAdditionalProperty('account_id');
    } //getAccountId

    /**
     * Set account id
     *
     * @param $value
     * @return mixed
     */
    public function setAccountId($value) {
      return $this->setAdditionalProperty('account_id', $value);
    } //setAccountId

    /**
     * Return import URL
     *
     * @return mixed
     */
    public function getImportUrl() {
      return Router::assemble($this->getRoutingContext() . '_import', $this->getRoutingContextParams());
    } //getImportUrl

    /**
     * Return validate URL
     *
     * @return mixed
     */
    public function getValidateUrl() {
      return Router::assemble($this->getRoutingContext() . '_validate_before_import', $this->getRoutingContextParams());
    } //getValidateUrl

    /**
     * Create request
     *
     * @param $url_suffix
     * @param $additional_params
     * @param $as_json
     * @return mixed
     * @throws Error
     */
    protected function makeRequest($url_suffix, $additional_params = null, $as_json = false) {
      $url = $this->generateAPIUrl() . $url_suffix . '.json';
      return $this->requestUrl($url, $additional_params, $as_json);
    } //createRequest

    /**
     * Request Url from basecamp
     *
     * @param $url
     * @param $additional_params
     * @param bool $as_json
     * @return mixed
     * @throws Error
     */
    protected function requestUrl($url, $additional_params = null, $as_json = false) {

      //set additional params from array
      if($additional_params) {
        $nvp = http_build_query($additional_params);
        $url .= '?' .$nvp;
      } //if

      $curl = curl_init($url);
      $this->addAuthenticationHeader($curl);

      $response = curl_exec($curl);

      $info = curl_getinfo($curl);
      if ($info["http_code"] == 200) {
        return $as_json ? $response : json_decode($response);
      } else {
        $message = $response ? $response : lang('Basecamp connection error');
        throw new Error($message);
      } // if
      curl_close($curl);
    } //requestUrl

    /**
     * Download attachments
     *
     * @param $url
     * @return string
     */
    public function downloadAttachment($url) {

      $bc_tmp_attachments = WORK_PATH . '/bc_attachments';
      recursive_mkdir($bc_tmp_attachments);

      $ext = get_file_extension($url, true);
      $ext = substr($ext, 0, strpos($ext, "?"));

      $filename = $bc_tmp_attachments . '/bc_attachment_' . make_string() . $ext;

      $fp = fopen($filename, 'w');

      $curl = curl_init($url);
      $this->addAuthenticationHeader($curl);
      curl_setopt($curl, CURLOPT_FILE, $fp);
      curl_exec($curl);
      curl_close($curl);
      fclose($fp);
      return $filename;
    } //downloadAttachment

    /**
     * Generate basecamp api url
     *
     * @return string
     */
    private function generateAPIUrl() {
      return BasecampSource::API_URL . '/' . $this->getAccountId() . '/api/' . BasecampSource::API_VERSION;
    } //generateAPIUrl

    /**
     * Authenticate request
     *
     */
    private function addAuthenticationHeader(&$curl) {
      curl_setopt($curl, CURLOPT_USERAGENT, 'activeCollab ('. $this->getUsername().')');
      curl_setopt($curl, CURLOPT_USERPWD, $this->getUsername() . ":" . $this->getPassword());
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    } //authenticate

  } //BasecampSource