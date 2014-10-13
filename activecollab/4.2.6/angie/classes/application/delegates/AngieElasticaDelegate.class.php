<?php

  // Include Elastica
  require_once ANGIE_PATH . '/vendor/elastica/init.php';

  use \Elastica\Client as Client;

  /**
   * Elastic search delegate
   *
   * @package angie.library.application
   * @subpackage delegates
   */
  final class AngieElasticaDelegate extends AngieDelegate {

    /**
     * Elastica client instance
     *
     * @var Client
     */
    private $client = false;

    /**
     * Return Elastica client
     *
     * @return Client
     * @throws Error
     */
    function &getClient() {
      if($this->client === false) {
        $hosts = array();

        if(defined('ELASTIC_SEARCH_HOSTS')) {
          if(ELASTIC_SEARCH_HOSTS) {
            foreach(explode(",", ELASTIC_SEARCH_HOSTS) as $host) {
              $host = trim($host);

              if($host) {
                $parts = parse_url($host);

                $hosts[] = array(
                  'host' => isset($parts['host']) ? $parts['host'] : 'localhost',
                  'port' => isset($parts['port']) ? $parts['port'] : 9200,
                );
              } // if
            } // foreach
          } // if
        } else {
          throw new Error('ELASTIC_SEARCH_HOSTS not defined');
        } // if

        $client_config = array(
          'timeout' => defined('ELASTIC_SEARCH_TIMEOUT') && ELASTIC_SEARCH_TIMEOUT ? ELASTIC_SEARCH_TIMEOUT : null,
        );

        if(count($hosts)) {
          $client_config['servers'] = $hosts;
        } // if

        $this->client = new Client($client_config);
      } // if

      return $this->client;
    } // getClient

  }