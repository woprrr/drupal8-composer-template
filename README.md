# Drupal 8 skeleton docker
Drupal 8 starter-kit for multi container stack.

## Overview

1. [Install prerequisites](#install-prerequisites)

    Before installing project make sure the following prerequisites have been met.

2. [Clone the project](#clone-the-project)

    We’ll download the code from its repository on GitHub.

3. [Configure Xdebug](#configure-xdebug) [`Optional`]

    Soon available...

4. [Run the application](#run-the-application)

    All will works well now.
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

* Nginx
* Varnish
* Mysql
* Adminer

You should be careful when installing third party web servers such as MySQL or Nginx.

This project use the following ports :

| Server     | Port |
|------------|------|
| Mysql      | 3306 |
| Adminer | 2000 |
| Nginx      | 8080 |
|  H2-Proxy (SSL) | 80 |

___

## Clone the project

To install [Git](http://git-scm.com/book/en/v2/Getting-Started-Installing-Git), download it and install following the instructions :

```sh
git clone -b drupal-8-skeleton-docker git@github.com:woprrr/drupal8-composer-template.git
```

Go to the project directory :

```sh
cd drupal-8-skeleton-docker
```

### Project tree

```sh
.
├── LICENSE
├── Makefile
├── README.md
├── client_secrets.json.enc
├── docker-compose.yml
├── docker-compose.yml.dist
├── drupal8
│   ├── Dockerfile
│   ├── Dockerfile.nginx
│   ├── Dockerfile.varnish
│   ├── app
│   ├── bin
│   ├── composer.contributed.json
│   ├── composer.json
│   ├── composer.json.dist
│   ├── composer.lock
│   ├── composer.required.json
│   ├── composer.required.json.dist
│   ├── composer.suggested.json
│   ├── composer.suggested.json.dist
│   ├── config
│   ├── docker
│   ├── scripts
│   ├── settings
│   ├── vendor
│   └── web
├── h2-proxy
│   ├── Dockerfile
│   └── conf.d
└── helm
    └── drupal8
```

___


## Configure Xdebug

Comming soon....
___

## Run the application

1. Setup project environment variables :

    Setup your project by editing the `.env` file and customize all environment variables. By default the ENV file are in drupal folder `./drupal8/.env`

2. Initialize/Install project dependencies :

    ```sh
    make docker-start && make drupal8-c-install
    ```
3. Installing Drupal 8 project :

    ```sh
    make drupal8-si
    ```

3. Open your favorite browser :

    * [http://localhost:8080](http://localhost:8080/) (Web Front).
    * [http://localhost:80](https://localhost/) (Web Front HTTPS).
    * [http://localhost:2000](http://localhost:2000/) Adminer (username: root, password: root)

4. Stop and clear services :

    ```sh
    sudo docker-compose down -v
    ```

5. Stop and delete all traces of changes from skeleton :

    ```sh
    sudo make clean
    ```
    That delete all files to reset skeleton at his initial state.

## Help us

Any thought, feedback or (hopefully not!)
