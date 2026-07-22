<?php

namespace Drupal\edg_core\Service;

use Drupal\Component\Utility\DeprecationHelper;
use Drupal\Core\Utility\Error;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\RequestOptions;

/**
 * Service that handle Json operations.
 */
class JsonService {

  /**
   * Useragent.
   */
  const EDG_JSON_USERAGENT = 'Mozilla/5.0 ' .
  '(Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) ' .
  'Gecko/20080311 Firefox/2.0.0.13';

  /**
   * Get data with JSON request.
   *
   * @param string $url
   *   The url to request.
   *
   * @return string
   *   The response.
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function getJson($url) {
    $response = NULL;
    $client = new Client();
    $headers = [
      'User-Agent' => self::EDG_JSON_USERAGENT,
    ];

    try {
      $request = $client->request('GET', $url, [
        RequestOptions::HEADERS => $headers,
        RequestOptions::VERIFY => FALSE,
        RequestOptions::HTTP_ERRORS => FALSE,
        RequestOptions::ALLOW_REDIRECTS,
        // RequestOptions::DEBUG,.
      ]);

      $response = $request->getBody()->getContents();
    }
    catch (RequestException $e) {
      DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '10.1.0', fn() => Error::logException(\Drupal::logger('edg_core'), $e), fn() => watchdog_exception('edg_core', $e));
    }

    return $response;
  }

}
