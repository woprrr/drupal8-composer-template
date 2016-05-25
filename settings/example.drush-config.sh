#!/usr/bin/env bash

# Admin informations.
export DRUSH_INSTALL_ACCOUNT_NAME="admin"
export DRUSH_INSTALL_ACCOUNT_PASS="admin"
export DRUSH_INSTALL_ACCOUNT_MAIL="your@mail.fr"
export DRUSH_INSTALL_LOCALE="en"

# If you have an specific profile to install your site define here.
export DRUSH_INSTALL_PROFILE="standard"

# You must have this modules into composer.json to correctly install this modules.
export DRUSH_INSTALL_MODULES="devel kint config admin_toolbar"
