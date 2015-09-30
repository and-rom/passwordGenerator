#!/bin/bash
HOST=$1
USER=$2
PASS=$3
BASEFOLDER='/public_html'
TARGETFOLDER='/pwgen'
SOURCEFOLDER=`pwd`
echo $USER
echo $PASS
echo "ftp://"$HOST$BASEFOLDER$TARGETFOLDER
echo $SOURCEFOLDER
read
lftp -e "
open $HOST
user $USER $PASS
lcd $SOURCEFOLDER
mirror --exclude mistakes.txt --exclude config_db.php --exclude upload.sh --exclude .git/ --exclude .gitignore --exclude pwgen_1.sql --exclude pwgen_2.sql --exclude pwgen_3.sql --reverse --delete --verbose $SOURCEFOLDER $BASEFOLDER$TARGETFOLDER
bye
"
