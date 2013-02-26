<img src="https://secure.gravatar.com/avatar/2a8242057d58d3e601063be3135a216c?s=420&d=https://a248.e.akamai.net/assets.github.com%2Fimages%2Fgravatars%2Fgravatar-org-420.png">

# AnimeDB #

This is the anplication for making your home collection anime<br/>
The project is for home use only

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
