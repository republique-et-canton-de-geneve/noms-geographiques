# NGEO - Migration

All migrations are processed using the Drupal 7 database.

## Before run the first full Migrations

Before starting a migration, you must be sure to have the Drupal 7 database reachable from your Drupal instance and
having the proper Drupal 7 database access setup.

Ensure you have a database connection setup in Drupal `settings.php`.

```php
$databases['drupal7']['default'] = array(
  'database' => 'ngeo_d7',
  'username' => 'root',
  'password' => 'password',
  'prefix' => '',
  'host' => 'mariadb',
  'port' => '3306',
  'namespace' => 'Drupal\\Core\\Database\\Driver\\mysql',
  'driver' => 'mysql',
   '_dsn_utf8_fallback' => TRUE,
);
```

Ensure the database `druapl7` has been created.

To import a dump into this database:
  ```bash
  $ drush sql-cli --database=drupal7 < my-super-dump.sql
  ```

### Install the migration modules

  ```bash
  $ drush pmu ngeo_migrate -y && drush en ngeo_migrate -y
  ```

## Migrate

### Lieu de Genève

1. Migrate all lieux with communes.

  ```bash
  $ drush mim --group=ngeo_lieu_geneve --execute-dependencies
  ```

### TODO: compléter

## Debugging

List the migrations with `drush ms`

Limit a migration with `drush mim [migration_name] --limit=2`

Rollback your change with `drush mr [migration_name]`

## Help for building migration
```bash
$ drush cr && drush mr [migration_name] && drush mim [migration_name] --limit=1
```

## Troubleshooting

### Migration stuck

Migration [migration_name] is busy with another operation: Importing [error]

You just need to reset this migration using
  ```bash
  $ drush mrs [migration_name]
  ```

## Debug value
  ```bash
process:
  debug:
    plugin: callback
    callable: var_dump
    source: format
  ```

## Migrate an entity with specific id:
  ```bash
drush mim ngeo_lieu_geneve_node --idlist=27197
  ```

## Migrate AGAIN a same entity:
  ```bash
drush mim ngeo_lieu_geneve_node --idlist=27197 --update
  ```