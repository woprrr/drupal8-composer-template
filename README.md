# Drupal 8 Composer Install / Update
Define an standardized architecture to install/update Drupal 8 with composer. That project is a kickstart project to install Drupal Core & Usefull modules.

## Prepare settings
WARNING: This step is require before Run any Script thanks to read all instructions about Drupal developement / Drupal local settings.

### Drupal developement services

Copy & edit settings/example.development.services.yml file to override Drupal services parameters like cache backend, twig options
```
$ cp settings/example.development.services.yml settings/development.services.yml
```

### Drupal local settings

Copy & edit settings/example.settings.local.php file to add your custom configurations, database access , or specific variables has your Drupal instance.

```
$ cp settings/example.settings.local.php settings/settings.local.php
```

### Drush site install / update parameters

Copy & edit settings/example.drush-config.sh file to configure your Drupal instance informations.
```
$ cp settings/example.drush-config.sh settings/drush-config.sh
```

That variables are used by drush during installation process.

  - DRUSH_INSTALL_ACCOUNT_NAME : User name for the administrator 
  - DRUSH_INSTALL_ACCOUNT_PASS : Administrator password
  - DRUSH_INSTALL_ACCOUNT_MAIL : Administrator mail
  - DRUSH_INSTALL_LOCALE : Default language
  - DRUSH_INSTALL_PROFILE : Installation profile name
  - DRUSH_LOCAL_MODULES : Additional modules you want to install after Drupal installation


## Run installation

After cloning the file we can install all modules listed into composer.json file for install all dependencies of your project.
To tell composer install all modules needed execute that command :
```
$ composer install
```

After installation your Drupal & contributor modules are correctly download and present in web/ folder. If you have correctly configure your project @see `settings/examples.*` files.
```
$ composer site-install
```

The composer site-install run `/../scripts/site-install.sh` file and execute all commands needed to (re)install an fresh Drupal 8 instance.
WARNING : If you re-execute this script after an installation without configuration export to export your changes, any changes will be lost !

ATM this method need to duplicate your configuration files into modules/install && /../config/ folder to allow two modes coexist. It's possible to use an same method like [Config Installer]: https://www.drupal.org/project/config_installer to import all configurations since same `/../config/` folder.

## Run update

If you have already an active Drupal 8 instance you can run update script.
```
$ composer site-update
```

During the update site-update script re-syncronize all configurations of `CONFIG_SYNC_DIRECTORY` folder (by default /../config).

### Update project modules

To process an modules update with update script you need to edit your composer.json file and modify version of existing module. After modification of composer.json file you can use the common command
```
$ composer update <package_name>
```

By example for update drupal core version with that method
```
$ composer update drupal/core
```

Now to apply your changes you can run site-update scripts to applies configurations upadates.
```
$ composer site-update
```
