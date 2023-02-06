<?php

namespace Drupal\ngeo_migrate\Plugin\migrate\process;

use Drupal\Core\Database\Database;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;


/**
 * Map a Drupal 7 File ID (fid) to the corresponding fD9 file with the URI.
 *
 * Available configuration keys
 * - base_url: The base URL that will replace the schema (eg. public://)
 * - db_connection: (optional) The database key name. Defaults to 'migrate'.
 *
 * Examples:
 *
 * @code
 * process:
 *   pseudo_uri:
 *     plugin: d7_to_d9_fid
 *     source: url
 * @endcode
 *
 * @MigrateProcessPlugin(
 *     id="d7_to_d9_fid",
 * )
 */
class MapFileFid extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $db_connection = 'migrate';
    if (!empty($this->configuration['db_connection'])) {
      $db_connection = $this->configuration['db_connection'];
    }

    if (empty($value) && !isset($value['fid'])) {
      return '';
    }

    Database::setActiveConnection($db_connection);
    $connection_d7 = Database::getConnection();

    // Get the file URI from an fid.
    $query = $connection_d7->select('file_managed')
      ->condition('file_managed.fid', $value['fid']);
    $query->fields('file_managed', [
      'uri',
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

    $files = \Drupal::entityTypeManager()
      ->getStorage('file')
      ->loadByProperties(['uri' => $file->uri]);
    $file = reset($files) ?: NULL;

    return $file->fid_value;
  }
}
