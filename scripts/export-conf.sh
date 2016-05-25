#!/usr/bin/env bash
# Drupal 8 Export-configuration files Script.
#
# This file:
#
#  - This script can be used to update all configurations yml in `/../config/` folder with you current configuration changes.
#
# Version: 1.0.0
#
# Authors:
#
#  - Alexandre MALLET (woprrr) (https://www.drupal.org/u/woprrr)
#
# Usage:
#
#  composer export-conf
#
# Licensed under MIT
# Copyright (c) 2015 Alexandre MALLET (https://www.drupal.org/u/woprrr)
DRUSH="$(pwd)/vendor/bin/drush -r $(pwd)/web"

# Include settings script
. "$(pwd)/settings/drush-config.sh"

if [ ! -z "$DRUSH_LOCAL_MODULES" ]
  then
    $DRUSH pmu $DRUSH_LOCAL_MODULES -y
fi

# Export configuration files.
$DRUSH cex -y

# Enable additional modules
if [ ! -z "$DRUSH_LOCAL_MODULES" ]
  then
    $DRUSH en $DRUSH_LOCAL_MODULES -y
fi
