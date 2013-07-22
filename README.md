<img src="http://anime-db.org/images/logo.jpg" /><br />
[![Build Status](https://travis-ci.org/peter-gribanov/application.png?branch=framework)](https://travis-ci.org/peter-gribanov/application)<br />
<img src="http://www.php.net/images/logos/php5-power-micro.png" />

# AnimeDB #

This is the application for making your home collection anime<br />
The application is for home use only<br />
As of PHP 5.4.0

## Repositories ##

The official source code for this application can be retrieved from<br />
<http://github.com/anime-db/application>

## Installation ##

Clone this repository to fetch the latest version of this application

    git clone git://github.com/anime-db/application.git


## Quick start ##

### From Windows ###

To run the application, call the script

    bin/Run.vbs

To stop the application, call the script

    bin/Stop.vbs

### From Linux ###

*To launch the application of Linux you need to install PHP version 5.4 and above*


    # command to start the application
    $ bin/service start

    # command to stop the application
    $ bin/service stop

    # command to restart the application
    $ bin/service restart


After starting the application, open the browser <http://localhost:56780/>

If you want to access an application on your local network, you need open on another computer the browser with address <http://{ip_addres}:56780/>, where {ip_addres} is the IP address of the computer on which the application is running

*If you run the application only on Linux or Mac, you can delete the files needed to run the application on Windows*

    $ rm -rf bin/php bin/Run.vbs bin/Stop.vbs

## Install as service ##

*Work only in Linux*

To start the application as a service, you need edit startup-file of the application to specify the path to it

    vim bin/service

    # set real path to application
    path=.

Create a symbolic link on service

    $ ln -s /path/to/app/bin/service /etc/init.d/animedb

Run service

    $ service animedb start

For the application is launched after the computer start, run the command

    $ update-rc.d animedb defaults

## Depending ##

PHP version > 5.4.x<br />
PHP extensions:
* curl
* exif
* gd2
* intl
* mbstring
* pdo_sqlite
* tidy
