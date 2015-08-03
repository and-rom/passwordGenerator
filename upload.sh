#!/bin/bash
HOST=$1
USER=$2
PASS=$3
BASEFOLDER='/public_html'
TARGETFOLDER='/pwgen'
SOURCEFOLDER=`pwd`
echo $HOST
echo $USER
echo $PASS
echo $BASEFOLDER
echo $TARGETFOLDER
echo $SOURCEFOLDER
read
lftp -f "
open $HOST
user $USER $PASS
lcd $SOURCEFOLDER
mirror --exclude upload.sh --exclude .git/ --reverse --delete --verbose $SOURCEFOLDER $BASEFOLDER$TARGETFOLDER
"
