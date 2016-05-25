#!/usr/bin/env bash
# Drupal 8 Install Script.
#
# This file:
#
#  - Is an install script to drupal 8 with drush.
#
# Version: 1.0.0
#
# Authors:
#
#  - Alexandre MALLET (woprrr) (https://www.drupal.org/u/woprrr)
#
# Usage:
#
#  composer site-install
#
# Licensed under MIT
# Copyright (c) 2015 Alexandre MALLET (https://www.drupal.org/u/woprrr)

DRUSH="$(pwd)/vendor/bin/drush -r $(pwd)/web"

# Include settings script to import all local variables specific to project.
. "$(pwd)/settings/drush-config.sh"

# Execute an script to prepare Drupal files to standard method.
sh "$(pwd)/scripts/config-drupal-directory.sh"

# Install Drupal with drush command completed with drush-config developement variables.
$DRUSH site-install -y \
  --account-name=$DRUSH_INSTALL_ACCOUNT_NAME \
  --account-pass=$DRUSH_INSTALL_ACCOUNT_PASS \
  --account-mail=$DRUSH_INSTALL_ACCOUNT_MAIL \
  --locale=$DRUSH_INSTALL_LOCALE \
  $DRUSH_INSTALL_PROFILE

# Cache rebuild.
$DRUSH cr

# Import configuration files This command use the configuration of modules/profiles not `/../config/`.
$DRUSH cim --quiet -y

# Enable additional local modules listed in `/../settings/drush-config.sh` script template.
if [ ! -z "$DRUSH_LOCAL_MODULES" ]
  then
    $DRUSH en $DRUSH_LOCAL_MODULES -y
fi

# Change to correct permissions the file settings.local.php writable to `others` group.
chmod +w "$(pwd)/web/sites/default/settings.local.php"
