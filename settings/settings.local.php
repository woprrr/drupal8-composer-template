<?php

# Settings.
$settings['container_yamls'][] = DRUPAL_ROOT . '/sites/development.services.yml';
$settings['hash_salt'] = 'YUPiSjYMM6dI0dc1pkewk73fwcfPegWlNxbfVQD_mrZG2WmuDCDMa9h2_lYbBZfXRp7GLnK8ew';

# Settings NOT FOR PRODUCTION ENV.
$settings['cache']['bins']['render'] = 'cache.backend.null';
$settings['extension_discovery_scan_tests'] = TRUE;
$settings['rebuild_access'] = TRUE;
$settings['file_chmod_directory'] = 0777;
$settings['file_chmod_file'] = 0777;

# Databases.
$databases['default']['default'] = array(
  'driver' => 'mysql',
  'database' => getenv('MYSQL_DATABASE'),
  'username' => getenv('MYSQL_ROOT_USER'),
  'password' => getenv('MYSQL_ROOT_PASSWORD'),
  'host' => 'mysqldb',
  'port' => 3306,
  'prefix' => '',
  'collation' => 'utf8mb4_general_ci',
  'namespace' => 'Drupal\\Core\\Database\\Driver\\mysql',
);

# Config.
$config['system.logging']['error_level'] = 'verbose';

# Config NOT FOR PRODUCTION ENV.
$config['system.performance']['css']['preprocess'] = FALSE;
$config['system.performance']['js']['preprocess'] = FALSE;

# Config directories
$config_directories = array(
  CONFIG_SYNC_DIRECTORY => getcwd() . '/../config'
);
