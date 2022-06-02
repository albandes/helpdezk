#!/bin/bash

FILE=/home/workdir/modules-mq/.gitignore
if [ -f "$FILE" ]; then
    rm -f $FILE
fi

FILE=/home/workdir/modules-mq/.gitattributes
if [ -f "$FILE" ]; then
    rm -f $FILE
fi

FILE=/home/workdir/modules-mq/appspec.yml
if [ -f "$FILE" ]; then
    rm -f $FILE
fi

FILE=/home/workdir/modules-mq/README.md
if [ -f "$FILE" ]; then
    rm -f $FILE
fi



