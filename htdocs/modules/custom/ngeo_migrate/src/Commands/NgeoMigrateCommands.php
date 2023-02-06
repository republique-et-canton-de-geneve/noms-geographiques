<?php

namespace Drupal\ngeo_migrate\Commands;

use Drush\Commands\DrushCommands;
use Drupal\Core\Database\Database;

/**
 * A custom Drush commandfile to complete migrations.
 *
 * In addition to this file, you need a drush.services.yml
 * in root of your module, and a composer.json file that provides the name
 * of the services file to use.
 *
 * See these files for an example of injecting Drupal services:
 *   - http://cgit.drupalcode.org/devel/tree/src/Commands/DevelCommands.php
 *   - http://cgit.drupalcode.org/devel/tree/drush.services.yml
 */
class NgeoMigrateCommands extends DrushCommands {

  /**
   * This command will update canonical links on node.
   *
   * @command ngeo_migrate:links-update
   */
  public function fieldsLinksUpdate() {
    $this->logger()->notice('Links update in progress...');
    $total = 0;
    $condition = '%\"/noms-geographiques/node/%';
    $regex = '/href="\/noms-geographiques\/node\/(\d+)"/m';
    $replacement = 'href="/node/';

    // body.
    $total += $this->_linkUpdateWithNewNid(
      'node__body',
      'body_value',
      $condition,
      $regex,
      $replacement,
    );

    // body revision.
    $total += $this->_linkUpdateWithNewNid(
      'node_revision__body',
      'body_value',
      $condition,
      $regex,
      $replacement,
    );

    // field_definition_arrete_ce.
    $total += $this->_linkUpdateWithNewNid(
      'node__field_definition_arrete_ce',
      'field_definition_arrete_ce_value',
      $condition,
      $regex,
      $replacement,
    );

    // field_definition_arrete_ce revision.
    $total += $this->_linkUpdateWithNewNid(
      'node_revision__field_definition_arrete_ce',
      'field_definition_arrete_ce_value',
      $condition,
      $regex,
      $replacement,
    );

    // 	field_anecdotes.
    $total += $this->_linkUpdateWithNewNid(
      'node__field_anecdotes',
      'field_anecdotes_value',
      $condition,
      $regex,
      $replacement,
    );

    // 	field_anecdotes revision.
    $total += $this->_linkUpdateWithNewNid(
      'node_revision__field_anecdotes',
      'field_anecdotes_value',
      $condition,
      $regex,
      $replacement,
    );

    $this->logger()->success($total . ' links update done!');
  }

  /**
   * Replace old nid with new one in canonical links.
   *
   * @param string $table
   * @param string $field
   * @param string $condition
   * @param string $regexp
   * @param string $replacement
   *
   * @return int
   *   the number of updates done.
   */
  protected function _linkUpdateWithNewNid(string $table, string $field, string $condition, string $regexp, string $replacement) {
    $subtotal = 0;
    // First, get the nodes with old links.
    $db = Database::getConnection();
    $nodesToUpdateQuery = $db->select($table, 'res');
    $nodesToUpdateQuery->fields('res');
    $nodesToUpdateQuery->condition($field, '%' . $condition . '%', 'LIKE');
    $nodesToUpdateRows = $nodesToUpdateQuery->execute()->fetchAll();

    foreach ($nodesToUpdateRows as $row) {
      // For each node, get the node ids from links to update.
      $matches = NULL;
      preg_match($regexp, $row->$field, $matches);

      // $matches has 2 entries:
      // - [0] -> the matched partial string.
      // - [1] -> the single nid.
      $oldNid = $matches[1];

      // Then, get the corresponding "new" nid.
      $mapIdsQuery = $db->select('migrate_map_ngeo_lieu_geneve_node', 'mig');
      $mapIdsQuery->fields('mig');
      $mapIdsQuery->condition('sourceid1', $oldNid);
      $mapIdsRows = $mapIdsQuery->execute()->fetchAll();

      // There should be only one matching result.
      if (isset($mapIdsRows[0])) {
        $newNid = $mapIdsRows[0]->destid1;

        // Finally, make the replacement.
        $count = 0;
        $fullReplacement = $replacement . $newNid . '"';
        $result = preg_replace($regexp, $fullReplacement, $row->$field, -1, $count);

        if ($count > 0) {
          // Update the node in database.
          $updateNodeQuery = $db->update($table);
          $updateNodeQuery->fields([$field => $result]);
          $updateNodeQuery->condition('entity_id', $row->entity_id);
          $updateNodeQuery->condition('revision_id', $row->revision_id);
          $updateNodeQuery->execute();
          $subtotal += 1;
          $this->logger()->notice('Entity-id ' . $row->entity_id . ', revision-id ' . $row->revision_id . ' - ' . $field . ': Link with old nid "' . $replacement . $oldNid . '" replaced with "' . $fullReplacement . '".');
        }
      }
    }
    return $subtotal;
  }

}
