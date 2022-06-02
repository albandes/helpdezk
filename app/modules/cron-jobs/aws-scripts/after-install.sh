#!/bin/bash

FILE=/var/www/html/helpdezk/app/modules/.gitignore
if [ -f $FILE ] ; then
        rm  $FILE
fi
FILE=/var/www/html/helpdezk/app/modules/appspec.yml
if [ -f $FILE ] ; then
        rm  $FILE
fi
FILE=/var/www/html/helpdezk/app/modules/README.md
if [ -f $FILE ] ; then
        rm  $FILE
fi

