Drupal 8 Composer Template
==========================
Provide a kickstart template for Drupal 8 projects, managing your site (Dependencies / Configuration) by [Composer].

[Composer]: https://getcomposer.org/

## Requirements

### Required
- [Composer installed]

> Note : Usage of composer globally is not required you can use composer 

[Composer installed]: https://getcomposer.org/doc/00-intro.md#installation-linux-unix-osx

## Installation
To use these template in maximum, we need to adjust the following settings/configuration by project specificities.
### Optional features
Copy & edit settings/example.composer.local.json file to add specific composer instructions for your Drupal projects.
```bash
cp settings/example.development.services.yml settings/development.services.yml
```
> **Note** : An example usefull example is to separate the "vital" packages to more optionals packages. To permit that
 you can require your principal packages into `/composer.json` and your `local` or optionals packages in `/settings/composer.local.json`
 
By default that kickstart assume only `/settings/composer.local.json` to define other packages to merge into `composer.json`. If you decide to use it customize it **before** fire `composer install` command.
 
> **Important** : You can define other configuration / files to decouple more your packages or change location on `/composer.json` file at `extra` properties @see at the bottom to customize. 
 That functionality are an implementation to another composer plugin [wikimedia/composer-merge-plugin] visite the project page to customize your template.
 
[wikimedia/composer-merge-plugin]: https://github.com/wikimedia/composer-merge-plugin

### Prepare settings {#project-settings}
> **Important** : This step is require before command `site-install` or `site-update`.

#### Drupal developement services
Copy & edit settings/example.development.services.yml file to override Drupal services parameters like cache backend, twig options
```bash
cp settings/example.development.services.yml settings/development.services.yml
```
This file is used to add aditional settings (only for development) to default service.yml. This is verry usefull to add options only for one specific user.

#### Drupal local settings.local
Copy & edit settings/example.settings.local.php file to add your custom configurations, database access , or specific variables has your Drupal instance.

```bash
cp settings/example.settings.local.php settings/settings.local.php
```
That functionality are added by this kickstart, that permit to add an custom settings which can vary from one configuration to another. That can be usefull to 
arround the problems of security problems when you define production database access in `settings.php` default file.

> **Important** : That file `settings.local.php` are already included for you in `settings.php` file. You should customize `settings.php` only for add generic configuration about our each instances.

### Drush config YAML

Copy & edit settings/example.drush.config.yml file to configure your Drupal instance informations.
```bash
cp settings/example.drush.config.yml settings/drush-config.yml
```

That file are the most important file to permit an correct management of your Drupal configuration Exporting/Importing. All informations in that file are user,
in background by few process (`drush`) and permit to that template to manage your instance for you. 

That parameters are used by `drush` process during installation/Update/Export process.

#### site.parameters
> Note : All of theses variables are used by drupal configuration management.
- **name**: That represent the name of your site by default (that can be overide by your eventuals configuration imports)
- **locale**: Important : variable, that permit to build an instance of drupal on specific language. IF ISN'T (`en`) YOU DO SPECIFY THE FOLLOWING ENTRY (`language`).
- **profile**: Important : Name of install **profile** you need to use. That is used only on `re-install` mode. That permit to syncronize your imported configuration with another active configuration by `enforcing system.site['uuid']`.
- **language**: Important If you use another language to `en` you should specify the `uuid` and `locale` of configuration you expect to import. If you export your configuration and not,
 specify `uuid` correctly your import fail because configuration manager can't delete the default language to import your configuration. You need to set the future uuid of language,
  to tell Drupal your configuration aren't an new language but the same.
- **admin.account**: Important : That is your futur administrator `username` / `password` / `mail`.

#### dev.modules
> Note : This entry are your additional modules you want to install but you don't want export her configuration. By example developpements modules `devel` are not desired on production,
 but for your developpers that are USEFULL, if we export her configuration with `composer export-conf` command you have an unused configuration for devel.

### Custom installation profile (example.config_deploy)
Copy & edit settings/example.config_deploy folder to use the re-install feature of Configuration Management. 

> **IMPORTANT** : That permit to import an specific configuration on a fresh instance of Drupal. When you use `composer site-install <configuration_name>` you drop the database and re-install a new Drupal instance. For permit you to import your old/new configuration store in `/config/*` you need to read your imported configuration and force `system.site.uuid` to permit to import your entire configuration.
```bash
cp settings/example.config_deploy web/profiles/custom/your_profile_name
mv web/profiles/custom/your_profile_name/example.config_deploy.info.yml web/profiles/custom/your_profile_name/your_profile_name.info.yml
mv web/profiles/custom/your_profile_name/example.config_deploy.install web/profiles/custom/your_profile_name/your_profile_name.install
vim web/profiles/custom/your_profile_name/your_profile_name.install #(AND EDIT NAME OF function _install (`your_profile_name_install`))#
```
Before using that new profile your need to edit two files to tell Drupal your profiles to be used.
- settings.local.php : By editing (`$settings['install_profile'] = 'your_profile_name';`)
- drush.config.yml : By editing (`profile: your_profile_name`)

Enjoy you can play with `composer site-install` again and again withou loose your configurations :).

## Usage

After cloning/download the project files and adjust `/settings/*` files to your local. Add your projects into `composer.json` or/and `/settings/composer.local.json` file(s) following composer syntax.
> Example : Add `"drupal/image_widget_crop": "1.x"` line into `"require": {}` part. Note that if we have already fired `composer install` you have another way to add projects in your `composer.json` file.

When all is ready download and generate all Drupal project files with the following command :
```bash
composer install your_config_name_to_import
```
Important : These command allow to import an specific configuration during import process, eg: you need to import your `prod` configuration store in `/config/prod/*` you should use `composer site-install prod` command.
 If we not specify Drupal configuration import `CONFIG_SYNC_DIRECTORY` (sync).
 
> Note : If we need to specify other configuration export/import folder use your `setting.local.php` to define it like the following code.
```php
# Config directories
$config_directories = array(
  CONFIG_SYNC_DIRECTORY => getcwd() . '/../config',
  config1 => getcwd() . '/../config/config1',
  config2 => getcwd() . '/../config/config2',
  prod => getcwd() . '/../config/prod'
);
```
@see more at [Drupal.org documentation](https://www.drupal.org/docs/8/configuration-management/changing-the-storage-location-of-the-sync-directory)

After installation your Drupal Core, contributor modules, libraries, packages are correctly download and present in `web/*` folder. 

> Note : All Drupal packages are already move into `/web/*` eg: Drupal modules are placed in `/web/modules/contrib` etc...

If you have already correctly configure your project [settings](#project-settings) _**you can use the following commands**_.

### First installation

To install your Drupal instance, you just need to use the following command :
```bash
composer site-install
```

The `composer site-install` install a new fresh instance of Drupal 8 for you.

> WARNING : If you re-execute this command on existing instance, without exported configuration configured and files into your `/config/*` folder to export your changes, all you changes are lost ! 
Because `drush site-install` process drop all tables before (re)install a new instance.

### Exporting active configuration

That specificity of these template are that CRITICAL feature. With composer you can export/import all your Drupal configuration easily with one command. For last version you can specify too what configuration you need to export/import to an better configuration workflow usage.

After you install you site and customize in UI, you need to export for few reasons your Drupal configuration. These exported configuration can be usefull to deploy your change in your stage instance. (By example Like Feature in Drupal 7)

To understand more CMI you can read theses amazing articles 
[English](https://www.amazeelabs.com/en/blog/team-development-drupal-8-configuration-management) or 
[French](https://happyculture.coop/blog/drupal-8-gestion-configuration-cmi)

### Run update

To update your code or applie your exported configuration without complete (re)install, you should use site-update command.
```bash
composer site-update your_config_name_to_import
```

> Note : That command execute for you usuals drush commands (updb, entup, config-import). 

### Add new packages Drupal-modules
Like normal way to add packages in composer you should use the command `composer require drupal/module_machine_name`. For more explanations @see [Drupal.org documentation](https://www.drupal.org/node/2718229)

### Update project modules

To process an modules update with update script you need to edit your composer.json file and modify version of existing module. After modifications of `composer.json` or `composer.local.json file` you can use the common command
```bash
composer update drupal/module_machine_name
```

By example for update drupal core version with that method
```bash
composer update drupal/core
```

If we need to directly enable your module in your site you can edit `core.extensions.yml` file and add your module name to active modules. To tell Drupal configuration system this changes you need to use the following command.
```bash
composer site-update your_config_name_to_import
```

> Note : We can just add your package and enable your module in UI to export your changes after via `composer export-conf your_config_name_to_import` command.

### Re-Run Complete installation
To re-install your instance with new configuration or after an `composer export-conf` you do have an special profile to use this mode.
See previous part of these `Custom installation profile`?.
That method is similar to [Config Installer]: https://www.drupal.org/project/config_installer and discuss with @Alexpot.

After that profile correctly configured you can edit `settings/drush.config.yml` and change that line :

```YAML
parameters:
  site.parameters:
    profile: standard
```
to

```YAML
parameters:
  site.parameters:
    profile: your_profile_name
```
**_and_** edit your `settings.local.php` file like :
```php
$settings['install_profile'] = 'standard';
```
to

```php
$settings['install_profile'] = 'your_profile_name';
```

You are totaly free to custom your profile except delete `your_profile_install()` function if we need to use (re)installation mode with configuration import.

## FAQ
- These project are forked to [drupal-composer/drupal-project](https://github.com/drupal-composer/drupal-project) ?

No it's not, few part of these project are totally differents. These project are based on full php application, without any bash or external scripts.
These project not have same goal of drupal-project template. I really love drupal-project template, that an amazing project and I would love to participate to this with that project !!!! That project inspire me at the begining, the architecture of folders are similar.
