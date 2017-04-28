#!/bin/bash
# add chmod and chown command to spec dirs
# As su You need execute command chmod +x config-linux.sh

directory="./cookies"

# bash check if directory exists
if [ -d $directory ]; then
	echo "Directory exists and changes initiated... "
        sudo chmod 0775 cookies;
        sudo chmod 0755 web;
        sudo chown www-data:www-data cookies;
        sudo chown www-data:www-data web;
        sudo find cookies -type d -exec chown www-data:www-data {} \;
        sudo find cookies -type f -exec chown www-data:www-data {} \;
        sudo find cookies -type d -exec chmod 0775 {} \;
        sudo find cookies -type f -exec chmod 0775 {} \;
        echo "All operations performed for Linux."
else 
	echo "Directory does not exists, run 'mkdir cookies' command."
fi
