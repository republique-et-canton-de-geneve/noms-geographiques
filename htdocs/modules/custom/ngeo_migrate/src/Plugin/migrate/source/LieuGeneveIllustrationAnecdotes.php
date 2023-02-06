<?php

namespace Drupal\ngeo_migrate\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;

/**
 * {@inheritdoc}
 *
 * Extend the basic sql migration to fetch only file (illustration) attached to
 * anecdotes of Lieu de Genève.
 *
 * @MigrateSource(
 *     id="ngeo_d7_lieu_geneve_illustration_anecdote",
 *     source_module="ngeo_migrate"
 * )
 */
class LieuGeneveIllustrationAnecdotes extends SqlBase {

  /**
   * {@inheritdoc}
   */
  public function fields() {
    return [
      'field_illustrations_anecdotes_fid' => $this->t('File ID'),
      'entity_id' =>  $this->t('The Node (Lieu de Genève) ID'),
      'uri' => $this->t('URI'),
      'field_illustrations_anecdotes_alt' => $this->t('Alternative text'),
      'field_illustrations_anecdotes_title' => $this->t('Image title'),
      'delta' => $this->t('File delta position'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    $uri = $this::replaceWrongUriCharacters($row->getSourceProperty('uri'));
    $source_url = str_replace('public://', 'https://ge.ch/noms-geographiques/sites/noms-geographiques/files/', $uri);
    $row->setSourceProperty('source_url', $source_url);
    return parent::prepareRow($row);
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return [
      'field_illustrations_anecdotes_fid' => [
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
    $query = $this->select('field_data_field_illustrations_anecdotes', 'illustrations')
      ->fields('illustrations', [
        'field_illustrations_anecdotes_fid',
        'field_illustrations_anecdotes_alt',
        'field_illustrations_anecdotes_title',
        'delta',
        'entity_id'
      ]);

    $query->join('file_managed', 'files', 'files.fid = illustrations.field_illustrations_anecdotes_fid');

    $query->fields('files', ['uri']);

    return $query;
  }

  /**
   * The uri of this field are badly encoded. The name of the Lieu is part of it but not well encoded.
   *
   * Ex: parc de l'Ariana's anecdote illustration.
   * If we use urlencode(), the entire URI is rewritten and that is not what we want.
   *
   * @param $uri
   * @return string
   */
  static private function replaceWrongUriCharacters($uri) {
    $uri = str_replace(' ', '%20', $uri);
    $uri = str_replace('&', '%26', $uri);
    $uri = str_replace('#', '%23', $uri);
    return $uri;
  }
}
