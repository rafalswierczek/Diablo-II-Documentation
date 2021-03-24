#!/bin/bash

cd ../../

#ps aux | egrep '(apache|httpd)' # find web server user 
#chown -R apache * # set web server user as owner for all application files

chmod -R o-rwx * # drop all permissions for others
chmod -R ug=r * # set minimal permissions (for web server user and group)
chmod -R ug+x bin/* # set execute permission for binaries
chmod -R ug+X * # set entrance permission for all directories
chmod -R ug+w var/* public/build/* # set write permission for var directory contents (cache/log) and build output (webpack.config.js > .setOutputPath('public/build/'))
chmod -R u+w .emv

#  ug+w public/* ???
#  ug+w .env ???