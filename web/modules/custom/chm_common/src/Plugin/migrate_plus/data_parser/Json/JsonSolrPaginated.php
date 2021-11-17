<?php

namespace Drupal\chm_common\Plugin\migrate_plus\data_parser\Json;

use Drupal\migrate_plus\Plugin\migrate_plus\data_parser\Json;

/**
 * Obtain JSON data for migration.
 *
 * @DataParser(
 *   id = "json_solr_paginated",
 *   title = @Translation("Do paginated requests and parse Solr JSON response")
 * )
 */
class JsonSolrPaginated extends Json {

  /**
   * {@inheritdoc}
   */
  protected function getSourceData($urlPattern) {
    $items = [];
    $page_size = $this->getSourceConfigValue('pagesize_value', 10);
    $start = 0;
    do {
      $count = 0;
      $response = $this->parseResponsePage($start, $page_size);
      $start += $page_size;
      if (!empty($response['response']['docs'])) {
        $count = count($response['response']['docs']);
        $items = array_merge($items, $response['response']['docs']);
      }
    } while (intval($count) != 0);
    #drush_print('Total count: '. count($items));
    #drush_print('First item: '. json_encode($items[0]));
    return $items;
  }

  /**
   * {@inheritdoc}
   */
  public function count() {
    $response = $this->parseResponsePage(0, 0);
    if (isset($response['response']['numFound'])) {
      return intval($response['response']['numFound']);
    }
    return 0;
  }

  /**
   * Retrieve a single result page from remote service.
   *
   * @param int $start
   *   Row no. to start from.
   * @param int $pageSize
   *   Number of records to retrieve.
   *
   * @return array
   *   Parsed JSON from the remote service.
   */
  protected function parseResponsePage($start, $pageSize) {
    $ref = new \ReflectionClass($this);
    $url = $this->getUrl($start, $pageSize);
    \Drupal::logger('chm_common')->debug('@method: Loading URL: @url',
      ['@method' => $ref->getShortName(), '@url' => $url]);
    $response = $this->getDataFetcherPlugin()->getResponse($url);
    $content = json_decode($response->getBody(), TRUE);
    if (!isset($content['response'])) {
      \Drupal::logger('chm_common')->error('@class: Cannot get JSON from: @url (HTTP:@code - @reason)',
        [
          '@class' => $ref->getShortName(),
          '@url' => $url,
          '@code' => $response->getStatusCode(),
          '@reason' => $response->getReasonPhrase(),
        ]
      );
    }
    return $content;
  }

  /**
   * Build the URL for the remote request.
   *
   * @param int $start
   *   Row no. to start from.
   * @param int $pageSize
   *   Number of records to retrieve.
   *
   * @return null|string
   *   The URL to pull the data from with pagination and page size set.
   */
  protected function getUrl($start, $pageSize) {
    $url = NULL;
    if (is_array($this->configuration['urls']) && !empty($this->configuration['urls'][0])) {
      $url = $this->configuration['urls'][0];
    }
    else {
      $url = $this->configuration['urls'];
    }
    $pag_name = $this->getSourceConfigValue('paginator_parameter', 'start');
    $pagesize_name = $this->getSourceConfigValue('pagesize_parameter', 'rows');
    $url= sprintf('%s?%s=%d&%s=%d&%s', $url, $pag_name, $start, $pagesize_name, $pageSize, $this->getHTTPQueryParam());
    #drush_print('URL: '. $url);
    return $url;
  }

  /**
   * Return HTTP query (appended to the URL). Do NOT start with ampersand (&)!
   *
   * @return string
   *   URL Query fragment.
   */
  protected function getHttpQueryParam() {
    return '&dummy1=1&dummy2=2';
  }

  /**
   * Pull a value from source plugin `configuration` section in YML.
   *
   * @param string $name
   *   Name of the config parameter.
   * @param mixed $default
   *   Default value.
   *
   * @return mixed
   *   Configuration value or default.
   */
  protected function getSourceConfigValue($name, $default = NULL) {
    $ret = $default;
    if (!empty($this->configuration['configuration'][$name])) {
      $ret = $this->configuration['configuration'][$name];
    }
    return $ret;
  }

}
