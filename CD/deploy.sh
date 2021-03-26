#!/bin/bash

#>--------------------- CONSTANS ---------------------

PROD_SERVER_HOST=

PROD_USER=

PROD_APP_PARENTDIR_PATH=/var/www/html

PROD_APP_BACKUP_PATH=/var/backups

SCRIPT_PATH=$( cd "$(dirname "${BASH_SOURCE[0]}")" ; pwd -P )

APPLICATION_DIR_NAME=$(basename "$(cd ${SCRIPT_PATH}/.. ; pwd -P)")

#<--------------------- CONSTANS ---------------------


#>--------------------- SET OWNER IN PRODUCTION TO USE RSYNC AND TAR ---------------------

echo '#--------- CHOWN: START ---------#'

ssh -v ${PROD_USER}@${PROD_SERVER_HOST} "cd ${PROD_APP_PARENTDIR_PATH}/${APPLICATION_DIR_NAME} ; sudo chown -R ${PROD_USER} *"

echo '#--------- CHOWN: END ---------#'

#<--------------------- SET OWNER IN PRODUCTION TO USE RSYNC AND TAR ---------------------


#>--------------------- PRODUCTION BACKUP ---------------------

DATE=$(date '+%d-%m-%Y');

SALT=$(cat /dev/urandom | tr -dc 'a-zA-Z0-9' | fold -w 32 | head -n 1)

echo '#--------- BACKUP: START ---------#'

ssh -v ${PROD_USER}@${PROD_SERVER_HOST} tar -czvf ${PROD_APP_BACKUP_PATH}/${APPLICATION_DIR_NAME}_${DATE}_${SALT}.tar.gz ${PROD_APP_PARENTDIR_PATH}/${APPLICATION_DIR_NAME}

echo '#--------- BACKUP: END ---------#'

#<--------------------- PRODUCTION BACKUP ---------------------


#>--------------------- SYNCHRONIZE: LOCAL >> PRODUCTION ---------------------

# move to application directory
cd ${SCRIPT_PATH}/..

echo '#--------- RSYNC: START ---------#'

rsync -rtzP -iv --delete \
--exclude=.env \
--exclude=.env.local \
--exclude=.env.test \
--exclude=.git \
--exclude=.gitignore \
--exclude=todo.txt \
--exclude=var \
--exclude=CD \
../${APPLICATION_DIR_NAME}/ ${PROD_USER}@${PROD_SERVER_HOST}:${PROD_APP_PARENTDIR_PATH}/${APPLICATION_DIR_NAME}

echo '#--------- RSYNC: END ---------#'

#<--------------------- SYNCHRONIZE: LOCAL >> PRODUCTION ---------------------


#>--------------------- SET PERMISSIONS IN PRODUCTION ---------------------

# move to script directory
cd ${SCRIPT_PATH}

echo '#--------- SET PERMISSIONS AND WEB SERVER OWNER: START ---------#'

ssh -v ${PROD_USER}@${PROD_SERVER_HOST} 'bash -s' < ./set_permissions_production.sh ${PROD_APP_PARENTDIR_PATH} ${APPLICATION_DIR_NAME}

echo '#--------- SET PERMISSIONS AND WEB SERVER OWNER: END ---------#'

#<--------------------- SET PERMISSIONS IN PRODUCTION ---------------------