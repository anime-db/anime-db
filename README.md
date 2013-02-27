<img src="http://anime-db.org/images/logo.jpg">

# AnimeDB #

This is the anplication for making your home collection anime<br/>
The project is for home use only
As of PHP 5.4.0

## Repositories ##

The official source code for this application can be retrieved from<br/>
<http://github.com/anime-db/client>

## Installation ##
Clone this repository to fetch the latest version of this application

    git clone git://github.com/anime-db/client.git

## Quick start ##

    cd .. # go to the directory with the client
    cd bin

    # command to start the application
    ./service start

    # command to stop the application
    ./service stop

    # command to restart the application
    ./service restart

After starting the application, open the browser <http://localhost:56780/>

If you want to access an application on your local network, you need to edit startup-file of the application

    vim bin/service

    # find the line
    addr='localhost'

    # and replace it her 
    addr='0.0.0.0'

Seve the file and restart the application

    cd bin
    ./service restart


After restart, open on another computer the browser with address <http://{ip_addres}:56780/>, where {ip_addres} is the IP address of the computer on which the application is running
