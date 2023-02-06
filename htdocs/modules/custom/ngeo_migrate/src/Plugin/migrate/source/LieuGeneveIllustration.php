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
 *     id="ngeo_d7_lieu_geneve_illustration",
 *     source_module="ngeo_migrate"
 * )
 */
class LieuGeneveIllustration extends SqlBase {

  /**
   * {@inheritdoc}
   */
  public function fields() {
    return [
      'field_nomgeo_illustrations_fid' => $this->t('File ID'),
      'entity_id' =>  $this->t('The Node (Lieu de Genève) ID'),
      'uri' => $this->t('URI'),
      'field_nomgeo_illustrations_alt' => $this->t('Alternative text'),
      'field_nomgeo_illustrations_title' => $this->t('Image title'),
      'delta' => $this->t('File delta position'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    $source_url = str_replace('public://', 'https://ge.ch/noms-geographiques/sites/noms-geographiques/files/', $row->getSourceProperty('uri'));
    $row->setSourceProperty('source_url', $source_url);
    return parent::prepareRow($row);
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return [
      'field_nomgeo_illustrations_fid' => [
        'type' => 'integer',
        'alias' => 'fid',
      ],
      'entity_id' => [
        'type' => 'integer',
        'alias' => 'nid',
      ],
    ];
  }

  /**
   * {@inheritdoc}
   *
   * Fetch Drupal 7 database file illustrations.
   */
  public function query() {
    $query = $this->select('field_data_field_nomgeo_illustrations', 'illustrations')
      ->fields('illustrations', [
        'field_nomgeo_illustrations_fid',
        'field_nomgeo_illustrations_alt',
        'field_nomgeo_illustrations_title',
        'delta',
        'entity_id'
      ]);

    $query->join('file_managed', 'files', 'files.fid = illustrations.field_nomgeo_illustrations_fid');

    $query->fields('files', ['uri']);

    return $query;
  }
}
