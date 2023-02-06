<?php

namespace Drupal\ngeo_migrate\Plugin\migrate\process;

use Drupal\migrate\Plugin\migrate\process\Get;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;
use Drupal\Core\Database\Database;

/**
 * Migrate moderation states from Workbench Moderation.
 *
 * Examples:
 *
 * @code
 * process:
 *   moderation_state:
 *    - plugin: get_workbench_moderation
 *      source: nid
 * @endcode
 *
 * @MigrateProcessPlugin(
 *     id="get_workbench_moderation",
 * )
 */
class MigrationWorkbenchModeration extends Get {

  /**
   * Flag indicating whether there are multiple values.
   *
   * @var bool
   */
  protected $multiple;

  /**
   * The moderation information service.
   *
   * @var \Drupal\content_moderation\ModerationInformationInterface
   */
  protected $moderationInfo;

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {

    // Switch to default database.
    Database::setActiveConnection('drupal7');

    // Get a connection going.
    $db = Database::getConnection();

    if ($row->getSourceProperty('status') == 1) {
      // Le noeud source est publié, donc l'état du workflow sera publié.
      return 'published';
    }

    // Pas de révision publiée du noeud, on prend l'état courant.
    $query = $db->select('workbench_moderation_node_history', 'wstate')
      ->fields('wstate', ['state'])
      ->condition('wstate.nid', $row->getSourceProperty('nid'))
      ->condition('wstate.current', '1')
      ->range(0, 1);

    $rows = $query->execute()->fetchAll();

    $row = reset($rows);

    return $row->state ?? 'draft';
  }

  /**
   * {@inheritdoc}
   */
  public function multiple() {
    return $this->multiple;
  }

}
