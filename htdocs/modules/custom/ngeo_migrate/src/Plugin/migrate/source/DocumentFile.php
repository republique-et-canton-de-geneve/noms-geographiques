<?php

namespace Drupal\ngeo_migrate\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;

/**
 * {@inheritdoc}
 *
 * Extend the basic sql migration to fetch only file (illustration) attached to
 * Lieu de Genève.
 *
 * @MigrateSource(
 *     id="ngeo_d7_document_file",
 *     source_module="ngeo_migrate"
 * )
 */
class DocumentFile extends SqlBase {

  /**
   * {@inheritdoc}
   */
  public function fields() {
    return [
      'nid' => $this->t('D7 Document NID'),
      'entity_id' => $this->t('ID du lieu de Genève'),
      'fid' => $this->t('Id du fichier'),
      'delta' => $this->t('Delta')
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return [
      'nid' => [
        'type' => 'integer',
      ],
      'entity_id' => [
        'type' => 'integer',
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function query() {
    $query = $this->select('	field_data_field_nomgeo_documents', 'document_ref')
      ->fields('document_ref', [
        'field_nomgeo_documents_nid',
        'entity_id',
        'delta'
      ]);

    $query->join('node', 'nodes', 'nodes.nid = document_ref.field_nomgeo_documents_nid');
    $query->fields('nodes', ['title', 'nid']);

    $query->join('	field_data_field_fichier_fichierjoint', 'document_fichier_ref', 'nodes.nid = document_fichier_ref.entity_id');
    $query->join('file_managed', 'files', "files.fid = document_fichier_ref.field_fichier_fichierjoint_fid");

    $query->fields('files', ['fid']);

    return $query;
  }
}
