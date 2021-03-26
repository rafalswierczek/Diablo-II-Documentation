#!/bin/bash

PROD_APP_PARENTDIR_PATH=$1
APPLICATION_DIR_NAME=$2

# move to application directory
cd ${PROD_APP_PARENTDIR_PATH}/${APPLICATION_DIR_NAME}

# drop all permissions for others
sudo chmod -R o-rwx *

# drop execute to allow entrance (X)
sudo chmod -R ug-x *

# set only read permission for all files and entrance permission for all directories
sudo chmod -R ug=rX *

# set execute permission for binaries
sudo chmod -R ug+x bin

# set write permission for var directory contents (cache/log) and build output (webpack.config.js > .setOutputPath('public/build/'))
sudo chmod -R ug+w var public/build

# read only for environment variables
sudo chmod ug=r .env

# select web server user
HTTPDUSER=$(ps axo user,comm | grep -E '[a]pache|[h]ttpd|[_]www|[w]ww-data|[n]ginx' | grep -v root | head -1 | cut -d\  -f1)

# set web server user as owner for all application files
sudo chown -R ${HTTPDUSER} *