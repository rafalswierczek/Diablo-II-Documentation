#!/bin/bash

SCRIPT_PATH=$( cd "$(dirname "${BASH_SOURCE[0]}")" ; pwd -P )

APPLICATION_DIR_NAME=$(basename "$(cd ${SCRIPT_PATH}/.. ; pwd -P)")

DATE=$(date '+%d-%m-%Y');

SALT=$(cat /dev/urandom | tr -dc 'a-zA-Z0-9' | fold -w 32 | head -n 1)

BACKUP_NAME="/var/www/backups/${APPLICATION_DIR_NAME}_${DATE}_${SALT}.tar.gz"

cd $SCRIPT_PATH/../.. # move to application parent directory

tar -czvf ${BACKUP_NAME} ${SCRIPT_PATH}/../../${APPLICATION_DIR_NAME}

echo 'New backup in ${BACKUP_NAME}"