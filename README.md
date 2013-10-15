<img src="http://anime-db.org/images/logo.jpg" /><br />
[![Build Status](https://travis-ci.org/anime-db/anime-db.png)](https://travis-ci.org/anime-db/anime-db)<br />
<img src="http://www.php.net/images/logos/php5-power-micro.png" />

# Anime DB #

This is the application for making your home collection anime<br />
The application is for home use only<br />
As of PHP 5.4.0

## Repositories ##

The official source code for this application can be retrieved from<br />
<http://github.com/anime-db/anime-db>

## Installation ##

Clone this repository to fetch the latest version of this application

    git clone git://github.com/anime-db/anime-db.git && cd anime-db

Start by downloading Composer. If you have curl installed, it's as easy as:

    curl -s https://getcomposer.org/installer | php

Installation of dependencies using Composer

    php composer.phar install

**Note:** For Windows you can download PHP sourse from [php.net](http://windows.php.net/downloads/releases/php-5.4.17-nts-Win32-VC9-x86.zip)
and extract to folder bin/php for a quick start. Then you can install from the Composer, the following command:

    bin/php/php.exe composer.phar install

**Note:** Do not forget list of extensions specified in the depending section of this document

## Quick start ##

### From Windows ###

**Note:** The default is expected that PHP is installed on your computer.
If you put in the PHP directory bin/php, you need edit startup-file of the application to specify the path to PHP.
Open file bin/Run.vbs and set real path to PHP.

    sPhp = sPath & "/bin/php/php.exe"

To run the application, call the script

    bin/Run.vbs

To stop the application, call the script

    bin/Stop.vbs

### From Linux ###

To run the application, call the script

    bin/service start

To stop the application, call the script

    bin/service stop

To restart the application, call the script

    bin/service restart

### Open application ###

After starting the application, open the browser <http://localhost:56780/>

If you want to access an application on your local network, you need open on another computer the browser with address <http://IP_ADDRES:56780/>,
where **IP_ADDRES** is the IP address of the computer on which the application is running

## Install as service ##

**Note:** Work only in Linux

To start the application as a service, you need edit startup-file of the application to specify the path to it

    vim bin/service

Set real path to application

    path=/path/to/anime-db

Create a symbolic link on service

    ln -s /path/to/anime-db/bin/service /etc/init.d/anime-db

Run service

    service anime-db start

For the application is launched after the computer start, run the command

    update-rc.d anime-db defaults

## Depending ##

SQLite >= 3 <br />
PHP version >= 5.4.x<br />
PHP extensions:
* pdo_sqlite
