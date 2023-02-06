<?php

$databases['default']['default'] = array(
  'database' => 'drupal',
  'username' => 'drupal',
  'password' => 'drupal',
  'prefix' => '',
  'host' => 'mariadb',
  'port' => '3306',
  'namespace' => 'Drupal\\mysql\\Driver\\Database\\mysql',
  'driver' => 'mysql',
);

$databases['default']['toMigrate'] = array(
  'database' => 'drupal_migration',
  'username' => 'drupal',
  'password' => 'drupal',
  'prefix' => '',
  'host' => 'mariadb',
  'port' => '3306',
  'namespace' => 'Drupal\\mysql\\Driver\\Database\\mysql',
  'driver' => 'mysql',
);


$config['environment_indicator.indicator']['bg_color'] = '#eb7a34'; // local
$config['environment_indicator.indicator']['fg_color'] = '#ffffff';
$config['environment_indicator.indicator']['name'] = 'local';
//$config['gin.settings']['preset_accent_color'] = 'gin';
//$config['environment_indicator.indicator']['current_release'] = 'k-0.1.2'; // KO

$settings['config_sync_directory'] = dirname ( __FILE__,1) . '/sync';

/*** Cache disable for development. ***/
$settings['container_yamls'][] = dirname ( __FILE__,1) . '/development.services.yml';
$settings['cache']['bins']['render'] = 'cache.backend.null';
$settings['cache']['bins']['dynamic_page_cache'] = 'cache.backend.null';

$config['system.performance']['css']['preprocess'] = FALSE;
$config['system.performance']['js']['preprocess'] = FALSE;

$settings['memcache']['servers'] = ['127.0.0.1:11211' => 'default'];
$settings['memcache']['bins'] = ['default' => 'default'];
$settings['memcache']['key_prefix'] = 'ngeo_cache';
$settings['trusted_host_patterns'] = [
  '^noms-geographiques.local.ge.ch$',
  '^cmsadmin-noms-geographiques.local.ge.ch$',
  '^127\.0.\0.\1$',
  '^localhost$',
];
//$settings['cache']['default'] = 'cache.backend.memcache';
//$settings['cache']['bins']['render'] = 'cache.backend.memcache';

$settings['extension_discovery_scan_tests'] = FALSE;

$config['reroute_email.settings']['enable'] = TRUE;
$config['reroute_email.settings']['address'] = "didier.beux@etat.ge.ch";

$config['matomo']['site_id'] = '12';
$config['matomo']['url'] = "";

