# Drupal 8 Composer Install / Update
Define an standardized architecture to install/update Drupal 8 with composer. That project is a kickstart project to install Drupal Core & Usefull modules.

## Prepare settings
WARNING: This step is require before Run any Script thanks to read all instructions about Drupal developement / Drupal local settings.

### Drupal developement services

Copy & edit settings/example.development.services.yml file to override Drupal services parameters like cache backend, twig options
```bash
cp settings/example.development.services.yml settings/development.services.yml
```

### Drupal local settings

Copy & edit settings/example.settings.local.php file to add your custom configurations, database access , or specific variables has your Drupal instance.

```bash
cp settings/example.settings.local.php settings/settings.local.php
```

### Drush site install / update parameters

Copy & edit settings/example.drush-config.sh file to configure your Drupal instance informations.
```bash
cp settings/example.drush-config.sh settings/drush-config.sh
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
```bash
composer install
```

After installation your Drupal & contributor modules are correctly download and present in web/ folder. If you have correctly configure your project @see `settings/examples.*` files.
```bash
composer site-install
```

The composer site-install run `/../scripts/site-install.sh` file and execute all commands needed to (re)install an fresh Drupal 8 instance.
WARNING : If you re-execute this script after an installation without configuration export to export your changes, any changes will be lost !

ATM this method need to duplicate your configuration files into modules/install && /../config/ folder to allow two modes coexist. It's possible to use an same method like [Config Installer]: https://www.drupal.org/project/config_installer to import all configurations since same `/../config/` folder.

### Re-Run Complete installation

/!\ WARNING /!\ After your first installation "composer site- install' or manualy, if you need to re-install your site with your configuration exported in `/../config/` folder.
YOU DO UNCOMMENT COMMENTED PART in `/../scripts/site-install.sh`. REPLACE "xxxxxx" PART BY UUID CORRESPOND TO YOUR FIRST CONFIG.
```
# Enforce system.site uuid to prevent config missmatch in re-install with config syncronization of old instance.
#$DRUSH cset system.site uuid "xxxxxxxx" -y
#$DRUSH cset shortcut.set.default uuid "xxxxxxxxx" -y

## Enforce language.entity.fr uuid
## to prevent the atempt to remove the default language configuration if you have choose FR in default language.
#$DRUSH cset language.entity.fr uuid "xxxxxxxxxxx" -y

```

ATM This script no retrive that after running `$DRUSH site-install` command. This action is needed only after your first install for prevent all missmatch about UUID into instances.

To retrive your `system.site uuid` you can use that commands :

System.site :
```
drush cget system.site uuid -y

```

Only if you use `Standard` Profile or an profile using `Shortcut` module you must get uuid too.
```
drush cget shortcut.set.default uuid -y

```

If you install your site with specific locale (language) you must retrive uuid too to prevent missmatch (only if it's an default language).
```
drush cget language.entity.fr uuid -y

```

AFTER correctly uncomment you can re-run install command `composer site-install`
Example when i uncomment this part :
```
## Enforce system.site uuid.
#$DRUSH cset system.site uuid "ea3db32f-fb7b-4b43-8818-7d4af9618034" -y
#$DRUSH cset shortcut.set.default uuid "b36fff8d-b146-446e-8bad-1f0ff779c464" -y

## Enforce language.entity.fr uuid
## to prevent the atempt to remove the default language configuration.
#$DRUSH cset language.entity.fr uuid "6f8a956a-9621-4b95-ac41-479c108e6812" -y

```

/!\ After upgrade on 8.2.x this trick is not necessary, CMI have fix this limitation when we need to syncronize your configuration. [Do Issue]: https://www.drupal.org/node/1613424 /!\

## Run update

If you have already an active Drupal 8 instance you can run update script.
```bash
composer site-update
```

During the update site-update script re-syncronize all configurations of `CONFIG_SYNC_DIRECTORY` folder (by default /../config).

### Update project modules

To process an modules update with update script you need to edit your composer.json file and modify version of existing module. After modification of composer.json file you can use the common command
```bash
composer update <package_name>
```

By example for update drupal core version with that method
```bash
composer update drupal/core
```

Now to apply your changes you can run site-update scripts to applies configurations upadates.
```bash
composer site-update
```
