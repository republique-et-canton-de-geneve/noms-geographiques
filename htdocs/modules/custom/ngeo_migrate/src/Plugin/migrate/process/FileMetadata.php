<?php

namespace Drupal\ngeo_migrate\Plugin\migrate\process;

use Drupal\Core\Database\Database;
use Drupal\migrate\MigrateException;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;


/**
 * Get a Drupal 7 File metadata (filename, uri, alt & title) base on given FID.
 *
 * Available configuration keys
 * - db_connection: (optional) The database key name. Defaults to 'migrate'.
 *
 * Examples:
 *
 * @code
 * process:
 *   destination_url:
 *     plugin: d7_file_metadata
 *     source: fid
 *     field_d7: field_nomgeo_illustrations
 * @endcode
 *
 * @MigrateProcessPlugin(
 *     id="d7_file_metadata",
 * )
 */
class FileMetadata extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $db_connection = 'migrate';
    if (!empty($this->configuration['db_connection'])) {
      $db_connection = $this->configuration['db_connection'];
    }

    if (empty($value)) {
      return [];
    }

    // Validate the configuration.
    if (empty($this->configuration['field_d7'])) {
      throw new MigrateException('Plugin field_d7 configuration is missing.');
    }

    Database::setActiveConnection($db_connection);
    $connection = Database::getConnection();

    $query = $connection->select("field_data_{$this->configuration['field_d7']}", 'field_file_d7')
      ->fields('field_file_d7', [
        "{$this->configuration['field_d7']}_fid",
        "{$this->configuration['field_d7']}_alt",
        "{$this->configuration['field_d7']}_title",
        'delta',
        'entity_id'
      ]);

    $query->join('file_managed', 'files', "files.fid = field_file_d7.{$this->configuration['field_d7']}_fid");
    $query->condition('files.fid', $value);
    $query->fields('files', ['uri']);

    $query->range(0, 1);
    $files = $query->execute()->fetchAll();

    if (empty($files)) {
      return [];
    }

    $file = reset($files);

    return (array) $file;
  }

}
