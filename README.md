Drupal 8 Composer Skeleton
==========================
Provide a skeleton for Drupal 8 projects, managing your site (Dependencies / Configuration) by [Composer].

[Composer]: https://getcomposer.org/

## Requirements

### Required
- [Composer installed]

> Note : Usage of composer globally is not required you can use composer 

[Composer installed]: https://getcomposer.org/doc/00-intro.md#installation-linux-unix-osx

## Installation
1. Clone repository or use quick installation command `composer create-project woprrr/drupal8-composer-template:8.2.x-dev` and follow shell instructions. (IMPORTANT during initialization of project you can use a custom install profile when shell question `site.profile (standard):` appear but if you want to re-install from existing configuration you will change that manually and using `config_installer` instead **show step 4**).
2. Copy settings files ` cp settings/example.development.services.yml settings/development.services.yml && cp settings/example.settings.local.php settings/settings.local.php`
3. Edit setting files with your database parameters for `settings.local.php` and your developments preferences with `development.services.yml`.
4. Initialize project with `composer site-install` command to install a fresh install of Drupal 8 with your specified profile (standard if not changed in `site.profile` app parameter).
5. After continue export your configuration to initialize the re-install feature if you want to use this mode in your project `composer export-conf`
6. If you need to use re-install from your previous exported configuration you will edit your app `/app/Drupal/config/parameters.yml` file and change `site.profile (standard): your_choice` with site.profile (standard): `config_installer` see [Re-Run Complete instance from existing configuration folder] for more example.
7. Now you can use all feature provide by this skeleton `composer site-install` / `composer site-update`.
8. ENJOY !

## DEMONSTRATION
You can watch [this video] for more explanation/example of how use all features provided by this skeleton.

[this video]: https://youtu.be/-4nh6IJZLTw

## Re-Run Complete instance from existing configuration folder
To re-install your instance with new configuration or after an `composer export-conf` we have a dependency with [Config Installer]: https://www.drupal.org/project/config_installer to synchronize your configuration with active instance.

After that profile correctly configured you can edit `app/Drupal/config/parameters.yml` and edit file as bellow :

```YAML
# This file is auto-generated during the composer install
parameters:
    site.name: 'Woprrr site'
    site.locale: en
    site.profile: standard
    admin.account.name: admin
    admin.account.password: admin
    admin.account.mail: your@mail.fr
    site.language.uuid: ''
    site.language.locale: en
    dev.modules:
        - devel
        - kint
        - admin_toolbar
```
to

```YAML
# This file is auto-generated during the composer install
parameters:
    site.name: 'Woprrr site'
    site.locale: en
    site.profile: config_installer
    admin.account.name: admin
    admin.account.password: admin
    admin.account.mail: your@mail.fr
    site.language.uuid: ''
    site.language.locale: en
    dev.modules:
        - devel
        - kint
        - admin_toolbar
```

## Run update

To update your code or applie your exported configuration without complete (re)install, you should use site-update command.
```bash
composer site-update your_config_name_to_import
```

> Note : That command execute for you usuals drush commands (updb, entup, config-import). 

### Add new packages Drupal-modules
Like normal way to add packages in composer you should use the command `composer require drupal/module_machine_name`. For more explanations @see [Drupal.org documentation](https://www.drupal.org/node/2718229)

### Update project modules

To process an modules update with update script you need to edit your composer.json file and modify version of existing module. After modifications of `composer.json` or `composer.*.json file` you can use the common command
```bash
composer update drupal/module_machine_name
```

By example for update drupal core version with that method
```bash
composer update drupal/core
```

If we need to directly enable your module in your site you can edit `core.extensions.yml` file and add your module name to active modules. To tell Drupal configuration system this changes you need to use the following command.
```bash
composer site-update your_config_name
```

> Note : We can just add your package and enable your module in UI to export your changes after via `composer export-conf your_config_name_to_import` command.

## FAQ
- These project are forked to [drupal-composer/drupal-project](https://github.com/drupal-composer/drupal-project) ?

No it's not, few part of these project are totally different. These project are based on full php application, without any bash or external scripts.
These project not have same goal of drupal-project template. I really love drupal-project template, that an amazing project and I would love to participate to this with that project !!!! That project inspire me at the begining, the architecture of folders are similar.
