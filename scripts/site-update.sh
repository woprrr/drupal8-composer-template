#!/usr/bin/env bash
# Drupal 8 Update Script.
#
# This file:
#
#  - Is an update script to drupal 8 with drush.
#
# Version: 1.0.0
#
# Authors:
#
#  - Alexandre MALLET (woprrr) (https://www.drupal.org/u/woprrr)
#
# Usage:
#
#  composer site-update
#
# Licensed under MIT
# Copyright (c) 2015 Alexandre MALLET (https://www.drupal.org/u/woprrr)
DRUSH="$(pwd)/vendor/bin/drush -r $(pwd)/web"

# Include settings script
. "$(pwd)/settings/drush-config.sh"

# Execute an script to prepare Drupal files to standard method.
sh "$(pwd)/scripts/config-drupal-directory.sh"

# Desable all developement modules to exclude eventual config changes.
if [ ! -z "$DRUSH_LOCAL_MODULES" ]
  then
    $DRUSH pmu $DRUSH_LOCAL_MODULES -y
fi

# Maintenance ON (site not accessible).
$DRUSH state-set system.maintenance_mode 1

# Run all module updates.
$DRUSH updb -y

# Import configuration files since sync folder define in `CONFIG_SYNC_DIRECTORY` variable.
$DRUSH cim sync -y

# Update entity configuration IMPORTANT to prevent missmatch of entities.
$DRUSH entup -y

# After import, Re-export configuration files to permit re-sync of configurations.
$DRUSH cex sync -y

# Maintenance OFF
$DRUSH state-set system.maintenance_mode 0

# Cache rebuild.
$DRUSH cr

# Re-enable all developement modules.
if [ ! -z "$DRUSH_LOCAL_MODULES" ]
  then
    $DRUSH en $DRUSH_LOCAL_MODULES -y
fi

chmod +w "$(pwd)/web/sites/default/settings.local.php"
