<?php

namespace Drupal\ngeo_migrate\Plugin\migrate\process;

use Drupal\Core\Database\Database;
use Drupal\migrate\MigrateException;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;


/**
 * Convert a Drupal 7 File ID (fid) to an absolute downloadable URL.
 *
 * Available configuration keys
 * - base_url: The base URL that will replace the schema (eg. public://)
 * - db_connection: (optional) The database key name. Defaults to 'migrate'.
 *
 * Examples:
 *
 * @code
 * process:
 *   destination_url:
 *     plugin: d7_file_to_url
 *     base_url: 'https://example.org'
 *     source: fid
 * @endcode
 *
 * @MigrateProcessPlugin(
 *     id="d7_file_to_url",
 * )
 */
class FileToUrl extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $db_connection = 'migrate';
    if (!empty($this->configuration['db_connection'])) {
      $db_connection = $this->configuration['db_connection'];
    }

    if (empty($value)) {
      return '';
    }

    if (!isset($value['fid'])) {
      // fid is fill directly.
      $fid = $value;
    }
    else {
      // fid is in the value array.
      $fid = $value['fid'];
    }

    // Validate the configuration.
    if (empty($this->configuration['base_url'])) {
      throw new MigrateException('Plugin base_url configuration is missing.');
    }

    Database::setActiveConnection($db_connection);
    $connection = Database::getConnection();

    // Get the file URI from an fid.
    $query = $connection->select('file_managed')
      ->condition('file_managed.fid', $fid);
    $query->fields('file_managed', [
      'uri',
      'filename',
    ]);
    $query->range(0, 1);
    $files = $query->execute()->fetchAll();

    if (empty($files)) {
      return '';
    }

    $file = reset($files);

    if (empty($file->uri)) {
      return '';
    }

    $file_uri = $file->uri;
//    dump($file_uri);
//    dump(str_replace('public://', $this->configuration['base_url'], $file_uri));
    return str_replace('public://', $this->configuration['base_url'], $file_uri);
  }

}
