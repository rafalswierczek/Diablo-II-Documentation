#!/bin/bash

script_path=$( cd "$(dirname "${BASH_SOURCE[0]}")" ; pwd -P )

cd $script_path/.. # move to application directory

HTTPDUSER=$(ps axo user,comm | grep -E '[a]pache|[h]ttpd|[_]www|[w]ww-data|[n]ginx' | grep -v root | head -1 | cut -d\  -f1)

chown -R $HTTPDUSER * # set web server user as owner for all application files

chmod -R o-rwx * # drop all permissions for others

chmod -R ug-x * # drop execute to allow entrance

chmod -R ug=rX * # set only read permission for all files and entrance permission for all directories

chmod -R ug+x bin # set execute permission for binaries

chmod -R ug+w var public/build .git .gitignore # set write permission for var directory contents (cache/log) and build output (webpack.config.js > .setOutputPath('public/build/')) and local git config

chmod ug=r .env # read only for environment variables