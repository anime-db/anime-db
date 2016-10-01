![Anime DB](http://anime-db.org/bundles/animedboffsite/images/logo.jpg)

[![Latest Stable Version](https://img.shields.io/packagist/v/anime-db/anime-db.svg?maxAge=3600&label=stable)](https://packagist.org/packages/anime-db/anime-db)
[![Latest Unstable Version](https://img.shields.io/packagist/vpre/anime-db/anime-db.svg?maxAge=3600&label=unstable)](https://packagist.org/packages/anime-db/anime-db)
[![Total Downloads](https://img.shields.io/packagist/dt/anime-db/anime-db.svg?maxAge=3600)](https://packagist.org/packages/anime-db/anime-db)
[![Build Status](https://img.shields.io/travis/anime-db/anime-db.svg?maxAge=3600)](https://travis-ci.org/anime-db/anime-db)
[![Coverage Status](https://img.shields.io/coveralls/anime-db/anime-db.svg?maxAge=3600)](https://coveralls.io/github/anime-db/anime-db?branch=master)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/anime-db/anime-db.svg?maxAge=3600)](https://scrutinizer-ci.com/g/anime-db/anime-db/?branch=master)
[![SensioLabs Insight](https://img.shields.io/sensiolabs/i/863c136d-5f15-4cb1-b9c6-1ec86b8da8ed.svg?maxAge=3600&label=SLInsight)](https://insight.sensiolabs.com/projects/863c136d-5f15-4cb1-b9c6-1ec86b8da8ed)
[![StyleCI](https://styleci.io/repos/8353416/shield?branch=master)](https://styleci.io/repos/8353416)
[![License](https://img.shields.io/packagist/l/anime-db/anime-db.svg?maxAge=3600)](https://github.com/anime-db/anime-db)

# Anime DB #

This is the application for making your home collection anime<br />
The application is for home use only<br />
As of PHP 5.4.0

## Repositories ##

The official source code for this application can be retrieved from<br />
<http://github.com/anime-db/anime-db>

## Documentation ##

Recommend that you read the [user guide](http://anime-db.org/en/guide/).

## Installation ##

Clone this repository to fetch the latest version of this application

    git clone git://github.com/anime-db/anime-db.git && cd anime-db

Start by downloading Composer. If you have curl installed, it's as easy as:

    curl -s https://getcomposer.org/installer | php

Installation of dependencies using Composer

    php composer.phar install

**Note:** After install the application you can uninstall Composer and use this command for update the application if you need:

    php bin/composer update

**Note:** For Windows you can download PHP archive from [php.net](http://windows.php.net/download/). You need extract the archive to folder `bin/php` for a quick start. Then you can install from the Composer, the following command:

    bin/php/php.exe composer.phar install --no-dev --prefer-dist

**Note:** Do not forget list of extensions specified in the depending section of this document

## Quick start ##

### From Windows ###

**Note:** The default is expected that PHP is installed on directory `bin/php`.
If you put the PHP is installed on your computer, you need edit config file to specify the path to PHP.
Open file `config.ini` and set real path to PHP.

    php=php

To run the application, call programm

    AnimeDB.exe

### From Linux ###

To run the application, call the script

    ./AnimeDB start

To stop the application, call the script

    ./AnimeDB stop

To restart the application, call the script

    ./AnimeDB restart

### Open application ###

After starting the application, open the browser <http://localhost:56780/>

If you want to access an application on your local network, you need open on another computer the browser with address <http://IP_ADDRES:56780/>,
where **IP_ADDRES** is the IP address of the computer on which the application is running

## Install as service ##

**Note:** Work only in Linux

To start the application as a service, you need edit startup-file of the application to specify the path to it

    vim AnimeDB

Set real path to application

    path=/path/to/anime-db

Create a symbolic link on service

    ln -s /path/to/anime-db/AnimeDB /etc/init.d/AnimeDB

Run service

    service AnimeDB start

For the application is launched after the computer start, run the command

    update-rc.d AnimeDB defaults

## Depending ##

SQLite >= 3 <br />
PHP version >= 5.4.x<br />
PHP extensions:
* pdo_sqlite
