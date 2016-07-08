#!/usr/bin/env bash
# Prepare drupal 8 architecture.
#
# This file:
#
#  - Configure & prepare project to use.
#
# Version: 1.0.0
#
# Authors:
#
#  - Alexandre MALLET (woprrr) (https://www.drupal.org/u/woprrr)
#
# Usage:
#
#  composer project-init
#
# Licensed under MIT
# Copyright (c) 2015 Alexandre MALLET (https://www.drupal.org/u/woprrr)

DRUSH="$(pwd)/vendor/bin/drush -r $(pwd)/web"

echo -e "\n#step 1. Settings : Check if examples files are correctly set."
if [ ! -f settings/settings.local.php ]
  then
    echo -e "Missing 'settings.local.php' creation in progress"
    cp settings/example.settings.local.php settings/settings.local.php
  else
    echo -e "File 'settings.local.php' correctly configured."
fi

if [ ! -f settings/development.services.yml ]
  then
    echo -e "Missing 'development.services.yml' creation in progress"
    cp settings/example.development.services.yml settings/development.services.yml
  else
    echo -e "File 'development.services.yml' correctly configured."
fi

if [ ! -d settings/drush-config.sh ]
  then
    echo -e "Missing 'drush-config.sh' creation in progress"
    cp settings/example.drush-config.sh settings/drush-config.sh
  else
    echo -e "File 'drush-config.sh' correctly configured."
fi

if [ ! -d web/profiles/custom ]
  then
    echo -e "Missing 'web/profiles/custom' folder"
    if [ ! -d web/profiles ]
      then
        echo -e "Folder 'web/profiles' not created do not forget to process 'composer install' to initiate your project.\nFolder 'web/profile' created."
        mkdir -p -m777 web/profiles/custom
    fi
  else
    echo -e "Folder 'web/profiles/custom' correctly configured"
fi

echo -e "\n#step 2. Project profile."
if [ ! -n "$(ls -A web/profiles/custom)" ]
  then
    read -p "Please enter machine name of your install profile : " input_variable
    cp -R settings/example.config_deploy web/profiles/custom/${input_variable}
    mv web/profiles/custom/${input_variable}/example.config_deploy.info.yml web/profiles/custom/${input_variable}/${input_variable}.info.yml
    mv web/profiles/custom/${input_variable}/example.config_deploy.install web/profiles/custom/${input_variable}/${input_variable}.install
    echo -e "\nYour profile is correctly created in location 'web/profiles/custom/${input_variable}'\n Make sure to edit ${input_variable}.install with correct function name eg : '${input_variable}_install()'"
  else
    echo "You have already an profile configured in folder 'web/profiles/custom'"
fi

echo -e "\n#step 3. Composer install : Are you sure you want to launch the boot command composer.json ?"
select yn in "Yes" "No"; do
    case $yn in
        Yes ) composer install; break;;
        No ) exit;;
    esac
done

echo -e "\n#step 4. Drupal instance : Are you sure you want launched the first installation of your instance Drupal 8 ? (Important you must have properly configure this file in the folder ' settings / * ') ?"
select tf in "Yes" "No"; do
    case $tf in
        Yes ) composer site-install; break;;
        No ) exit;;
    esac
done
