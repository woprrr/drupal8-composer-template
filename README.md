##/!\ DOC Outdated /!\
This project have totally change please contact woprrr.dev@gmail.com for the moment.

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

### Re-Run Complete installation
To re-install your instance with new configuration or after an `composer export-conf` you do have an special profile to use this mode.
You have an example profile available in `settings/example.config_deploy/*` you can copy / paste this folder into `/web/profiles/custom/` folder.

After copy/paste you can rename your install profile and edit it but you NEED yo preserve that function onto `your_profile/your_profile.install`
```php
function your_profile_install() {

  $config_sync_directorie = $GLOBALS['config_directories']['sync'];

  $file_storage = new \Drupal\Core\Config\FileStorage($config_sync_directorie);

  $system_site = $file_storage->read('system.site');
  if (isset($system_site['uuid'])) {
    \Drupal::configFactory()
      ->getEditable('system.site')
      ->set('uuid', $system_site['uuid'])
      ->save();
  }

}
```

That function permit to preserve your previous system.site uuid and set it on your new instance. That method is similar to [Config Installer]: https://www.drupal.org/project/config_installer and discuss with @Alexpot.

After that profile correctly configured you can edit `settings/drush-config.sh` and change that line :

```bash
# If you have an specific profile to install your site define here.
export DRUSH_INSTALL_PROFILE="config_deploy"
```
to

```bash
# If you have an specific profile to install your site define here.
export DRUSH_INSTALL_PROFILE="your_profile"
```

You are totaly free to custom your profile except delete `your_profile_install()` function.

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

### General questions

- After install my profile all actions tabs / local tabs are missing ?
-- Yes ! particulary if you use Bartik / Seven on Default / admin theme. You must install correct blocks in regions to add it. 
For solve it go to `web/core/profile/standard/config` and copy all blocks prefixed by 'block.block.bartik_*' & 'block.block.seven_*' and paste it on `config/` sync folder. 
