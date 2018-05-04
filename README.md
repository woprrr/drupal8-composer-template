# Drupal 8 Composer Skeleton
Drupal 8 skeleton dockerized in sperate containers (Nginx, PHP-FPM, MySQL and PHPMyAdmin).

## Overview

1. [Install prerequisites](#install-prerequisites)

    Before installing project make sure the following prerequisites have been met.

2. [Clone the project](#clone-the-project)

    We’ll download the code from its repository on GitHub.

4. [Configure Xdebug](#configure-xdebug) [`Optional`]

    We'll configure Xdebug for IDE (PHPStorm or Netbeans).

5. [Run the application](#run-the-application)

    By this point we’ll have all the project pieces in place.

6. [Use Makefile](#use-makefile) [`Optional` but strongly encouraged for beginner]

    When developing, you can use `Makefile` for doing recurrent operations.

7. [Use Docker Commands](#use-docker-commands)

    When running, you can use docker commands for doing recurrent operations.

___

## Install prerequisites

For now, this project has been mainly created for Unix `(Linux/MacOS)`. Perhaps it could work on Windows.

All requisites should be available for your distribution. The most important are :

* [Git](https://git-scm.com/downloads)
* [Docker](https://docs.docker.com/engine/installation/)
* [Docker Compose](https://docs.docker.com/compose/install/)

Check if `docker-compose` is already installed by entering the following command : 

```sh
which docker-compose
```

Check Docker Compose compatibility :

* [Compose file version 3 reference](https://docs.docker.com/compose/compose-file/)

The following is optional but makes life better :

```sh
which make
```

### Images to use

* [Nginx](https://hub.docker.com/_/nginx/)
* [MySQL](https://hub.docker.com/_/mysql/)
* [PHP-FPM](https://hub.docker.com/r/woprrr/php-fpm/)
* [PHPMyAdmin](https://hub.docker.com/r/phpmyadmin/phpmyadmin/)
* [Generate Certificate](https://hub.docker.com/r/jacoelho/generate-certificate/)

You should be careful when installing third party web servers such as MySQL or Nginx.

This project use the following ports :

| Server     | Port |
|------------|------|
| MySQL      | 8989 |
| PHPMyAdmin | 8080 |
| Nginx      | 8000 |
| Nginx SSL  | 3000 |

___

## Clone the project

To install [Git](http://git-scm.com/book/en/v2/Getting-Started-Installing-Git), download it and install following the instructions :

```sh
git clone -b drupal8-skeleton-docker git@github.com:woprrr/drupal8-composer-template.git
```

Go to the project directory :

```sh
cd drupal8-composer-template
```

### Project tree

```sh
.
├── LICENSE
├── Makefile
├── README.md
├── app
│   └── Drupal
│       └── parameters.yml.dist
├── composer.json.dist
├── composer.require.json
├── composer.required.json.dist
├── composer.suggested.json.dist
├── config
├── data
│   └── db
│       ├── dumps
│       └── mysql
├── doc
├── docker-compose.yml
├── etc
│   ├── nginx
│   │   ├── default.conf
│   │   └── default.template.conf
│   ├── php
│   │   └── php.ini
│   └── ssl
├── scripts
│   └── Composer
│       ├── DrupalExportConf.php
│       ├── DrupalHandlerBase.php
│       ├── DrupalInstall.php
│       └── DrupalUpdate.php
└── settings
    ├── development.services.yml.dist
    ├── phpunit.xml.dist
    ├── services.yml
    ├── settings.local.php.dist
    └── settings.php
```

___


## Configure Xdebug

If you use another IDE than [PHPStorm](https://www.jetbrains.com/phpstorm/) or [Netbeans](https://netbeans.org/), go to the [remote debugging](https://xdebug.org/docs/remote) section of Xdebug documentation.

For a better integration of Docker to PHPStorm, use the [documentation](https://github.com/nanoninja/docker-nginx-php-mysql/blob/master/doc/phpstorm-macosx.md).

1. Get your own local IP address :

    ```sh
    sudo ifconfig
    ```

2. Edit php file `etc/php/php.ini` and comment or uncomment the configuration as needed.

3. Set the `remote_host` parameter with your IP :

    ```sh
    xdebug.remote_host=192.168.0.1 # your IP
    ```
___

## Run the application

1. Setup project environment variables :

    Setup your project by editing the `.env` file and customize all environement variables. Specifically all `Drupal_*` variable are criticaly important to next steps and to customize your drupal instances.

2. Initialize/Install project dependencies :

    ```sh
    make docker-start
    ```

    **Please wait this might take a several minutes...**

    ```sh
    sudo docker-compose logs -f # Follow log output
    ```

4. Install Drupal instance :

    ```sh
    make drupal-si
    ```

    **Or specify name of configuration instance**

    ```sh
    make drupal-si my_configuration_name
    ```
    All of configuration available are defined in your `settings/settings.local.php` file from 
    
    ```php
    # Config directories
    $config_directories = array(
      my_configuration_name => '/absolute/path/to/config'
    );
    ```
    Example of typical workflow with configuration
    ```php
    # Config directories
    $config_directories = array(
      dev => getcwd() . '/../config/dev',
      preprod => getcwd() . '/../config/preprod',
      prod => getcwd() . '/../config/prod',
      stage => getcwd() . '/../config/stage',
    );
    ```

5. Open your favorite browser :

    * [http://localhost:8000](http://localhost:8000/) (Web Front).
    * [https://localhost:3000](https://localhost:3000/) (Web Front HTTPS).
    * [http://localhost:8080](http://localhost:8080/) PHPMyAdmin (username: dev, password: dev)

6. Stop and clear services :

    ```sh
    sudo docker-compose down -v
    ```
    
7. Stop and delete all traces of changes from skeleton :

    ```sh
    sudo make docker-stop
    ```
    That delete all files to reset skeleton at his initial state.

### Play with Drupal Configuration workflow
1. Export your current configuration instance

    ```sh
    make drupal-config-export
    ```
    
    **Or with Docker Compose**

    ```sh
    docker-compose exec -T php composer export-conf
    ```

2. After your first install of Drupal instance edit the `.env` file and change the following variable `DRUPAL_INSTALL_PROFILE=standard` to `DRUPAL_INSTALL_PROFILE=config_installer`. That take ability to re-install / update your drupal instance with ./config/* exported configuration states.

3. Re-install or update your instance from exported configuration

    **Re-install:**
    With Drop of current drupal database and complete re-import of ./config
        ```sh
        make drupal-si
        ```

    **Update:**
    With following drupal commands (up-db / ent-up ).
    > Every action processed by scripts switch your Drupal instance on `maintenance` mode and switch Online after every action automatically.

    ```sh
    make drupal-update
    ```

4. In more advanced usage you can also specified a drupal configuration name

    ```sh
    make drupal-si preprod || make drupal-update preprod
    ```
    
    **Or with Docker Compose**

    ```sh
    docker-compose exec -T php composer site-install preprod || docker-compose exec -T php composer site-update preprod
    ```

### Examples of life cycle

1. Start the Project containers :
    
    ```sh
    sudo make docker-start
    ```
    
2. Edit .env file.
    
3. Install drupal 8 instance :
    
    ```sh
    sudo make docker-si
    ```
    
3. Exporting Drupal configuration files :
    
        ```sh
        make drupal-config-export
        ```
        **Or with a specific destination**
        ```sh
        make drupal-config-export my_configuration_name
        ```
    
5. Enable Re-install from configuration mode :
        Edit `.env` file by changing `DRUPAL_INSTALL_PROFILE=standard` to `DRUPAL_INSTALL_PROFILE=config_installer`.
    
6. Re-installation of project from exported configuration :
        ```sh
        make drupal-si
        ```
7. Update of current instance :
    Edit one of configuration yml in your `/config` folder eg: system.site.site_name.
    and process to updating your drupal instance from configuration by using 
    
        ```sh
        make drupal-update
        ```
    Your Site Name will change that you specified in system.site.site_name yml file.
    
8. Another tips ? Call Help ;) :
    Show help :
    
    ```sh
    make help
    ```
___

## Use Makefile

When developing, you can use [Makefile](https://en.wikipedia.org/wiki/Make_(software)) for doing the following operations :

| Name                 | Description                                                                                                             |
|----------------------|-------------------------------------------------------------------------------------------------------------------------|
| code-sniff           | Check the API with PHP Code Sniffer (Drupal Standards).                                                                 |
| clean                | Clean directories for reset.                                                                                            |
| c-install            | Install PHP/Drupal dependencies with composer.                                                                          |
| c-update             | Update PHP/Drupal dependencies with composer.                                                                           |
| clean-drupal-config  | Delete exported configuration from project.                                                                             |
| docker-start         | Create and start containers.                                                                                            |
| docker-stop          | Stop and clear all services.                                                                                            |
| gen-certs            | Generate SSL certificates.                                                                                              |
| logs                 | Follow log output                                                                                                       |
| mysql-dump           | Create backup of all databases                                                                                          |
| mysql-restore        | Restore backup of all databases                                                                                         |
| test                 | Test all application (custom and contribution modules).                                                                 |
| test-contrib         | Test application with phpunit                                                                                           |
| test-custom-modules  | Test Drupal custom modules.                                                                                             |
| drupal-si            | Install new Drupal instance and drop database.                                                                          |
| drupal-update        | Update your current Drupal instance and (re)import your \`/config\` exported configuration.                             |
| drupal-config-export | Export your current Drupal instance from \`/config\` by default. That can be in sub-folder depend your custom changes.  |
___

## Use Docker commands

### Installing package with composer

```sh
docker-compose exec -T php composer install
```

### Requiring package with composer

```sh
docker-compose exec -T php composer require drupal/core
```

### Updating PHP dependencies with composer

```sh
docker-compose exec -T php composer update
```

### Testing PHP application with PHPUnit

```sh
docker-compose exec -T php bin/phpunit -c ./web/core ./web
```

### Fixing standard code with [CODER](https://www.drupal.org/project/coder)

```sh
docker-compose exec -T php composer phpcs ./web/modules/ or specify more specific path.
```

### Checking the standard code with [CODER](https://www.drupal.org/project/coder)

```sh
sudo docker-compose exec -T php ./app/vendor/bin/phpcs -v --standard=PSR2 ./app/src
```

### Checking installed PHP extensions

```sh
sudo docker-compose exec php php -m
```

### Handling database

#### MySQL shell access

```sh
sudo docker exec -it mysql bash
```

and

```sh
mysql -u"$MYSQL_ROOT_USER" -p"$MYSQL_ROOT_PASSWORD"
```

#### Creating a backup of all databases

```sh
mkdir -p data/db/dumps
```

```sh
source .env && sudo docker exec $(sudo docker-compose ps -q mysqldb) mysqldump --all-databases -u"$MYSQL_ROOT_USER" -p"$MYSQL_ROOT_PASSWORD" > "data/db/dumps/db.sql"
```

#### Restoring a backup of all databases

```sh
source .env && sudo docker exec -i $(sudo docker-compose ps -q mysqldb) mysql -u"$MYSQL_ROOT_USER" -p"$MYSQL_ROOT_PASSWORD" < "data/db/dumps/db.sql"
```

#### Creating a backup of single database

**`Notice:`** Replace "YOUR_DB_NAME" by your custom name.

```sh
source .env && sudo docker exec $(sudo docker-compose ps -q mysqldb) mysqldump -u"$MYSQL_ROOT_USER" -p"$MYSQL_ROOT_PASSWORD" --databases YOUR_DB_NAME > "data/db/dumps/YOUR_DB_NAME_dump.sql"
```

#### Restoring a backup of single database

```sh
source .env && sudo docker exec -i $(sudo docker-compose ps -q mysqldb) mysql -u"$MYSQL_ROOT_USER" -p"$MYSQL_ROOT_PASSWORD" < "data/db/dumps/YOUR_DB_NAME_dump.sql"
```


#### Connecting MySQL from [PDO](http://php.net/manual/en/book.pdo.php)

```php
<?php
    try {
        $dsn = 'mysql:host=mysql;dbname=test;charset=utf8;port=3306';
        $pdo = new PDO($dsn, 'dev', 'dev');
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
?>
```
Or using Drush to check if your database configuration is OK
```sh
    docker-compose exec -T php bin/drush --root="/var/www/html/web" sql-connect
```
___

## Help us

Any thought, feedback or (hopefully not!)