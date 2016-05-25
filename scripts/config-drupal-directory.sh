#!/usr/bin/env bash
# Prepare all files into web/sites/default/*.
#
# This file:
#
#  - Verify and configure all files into /default folder to install / update Drupal 8.
#
# Version: 1.0.0
#
# Authors:
#
#  - Alexandre MALLET (woprrr) (https://www.drupal.org/u/woprrr)
#
# Licensed under MIT
# Copyright (c) 2015 Alexandre MALLET (https://www.drupal.org/u/woprrr)

# Prepare the files directory if not exist.
if [ ! -d web/sites/default/files ]
  then
    mkdir -m777 web/sites/default/files
fi

# Prepare the settings files with settings.php present into `/../settings/`.
if [ -f web/sites/default/settings.php ]
  then
    chmod +w "$(pwd)/web/sites/default"
    chmod +w "$(pwd)/web/sites/default/settings.php"
    rm "$(pwd)/web/sites/default/settings.php"
fi
cp "$(pwd)/settings/settings.php" "$(pwd)/web/sites/default/settings.php"

# Prepare the settings files with service.yml present into `/../settings/`.
if [ -f web/sites/default/services.yml ]
  then
    chmod +w "$(pwd)/web/sites/default"
    chmod +w "$(pwd)/web/sites/default/services.yml"
    rm "$(pwd)/web/sites/default/services.yml"
fi
cp "$(pwd)/settings/services.yml" "$(pwd)/web/sites/default/services.yml"

# Prepare the settings.local.php file with symbolic link to include in settings.php file.
if [ -f web/sites/default/settings.local.php ]
  then
    rm "$(pwd)/web/sites/default/settings.local.php"
fi
ln -s "$(pwd)/settings/settings.local.php" "$(pwd)/web/sites/default/settings.local.php"

# Prepare the development services file
if [ -f settings/development.services.yml ]
  then
    rm -f "$(pwd)/web/sites/development.services.yml"
fi
ln -s "$(pwd)/settings/development.services.yml" "$(pwd)/web/sites/development.services.yml"
